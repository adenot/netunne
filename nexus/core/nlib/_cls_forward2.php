<?php
/*
 * 
FASTAUTH
	createuser_route
		- escolhe o gateway
		- cria as rotas
		- executa
MERGE
	createuser_forward
		processuser 		(cria as primeiras regras do usuario)
		processfixuser		(regras que fixam o mac dele)
		processuseracls		(cria as regras de acl dele)
		(os user_route ele reutiliza os q jah estavam no temp)

CHECKUSER
	fastauth	(ve se jah nao tava fixado)
	removeuser_forward
	createuser_forward
	createuser_route

Precisa Mudar:
- o CHECKUSER precisa tambem criar regras de banda
- preciso salvar os scripts q fixam os usuarios pra executar na inicializacao
- 

TODO
- Refazer os scripts cbq_restore e guest_restore
- Precisa gerar arquivos individuais com a banda e firewall de cada um
 */

class Forward {

	var $fwtpl;
	var $out="";
	var $shaper_out="";
	var $mark=1;
	var $conf;
	var $netconf;
	var $obj;
	var $cbq=array();
	var $file_cbq_from=0; // soh armazenar o cbq a partir de X
	var $shaperpool=0;
	var $shaperrules=array();
	var $poolparent;
	
	var $chapsecrets;
	
	var $linktable; // pra nao precisar ler e gravar o write_ini toda hora
	var $nxoff;	// mesmo acima


	function returnuser($login) {
		$this->openforwardxml();
		
		$users = xml::normalizeseq($this->conf[users][user]);
		for ($i=0;$i<count($users);$i++) {
			if ($users[$i][login]==trim($login)) {
				$found=1;
				$user = $users[$i];
			}
		} 
		if (!$found) {
			return false;
		}
		return $user;
	}
	function returnguest($key) {
		$this->openforwardxml();
		
		$guests = xml::normalizeseq($this->conf[guests][guest]);
		for ($i=0;$i<count($guests);$i++) {
			if ($guests[$i][key]==trim($key)) {
				$found=1;
				$guest = $guests[$i];
			}
		} 
		if (!$found) {
			return false;
		}
		return $guest;
	}

	function returnacl($aclnum) {
		$acls = xml::normalizeseq($this->conf[acls][acl]);
		for ($i=0;$i<count($acls);$i++) {
			if ($acls[$i][id]==$aclnum)
				return $acls[$i];
		}
		return -1;
	}
	
	function returnplanacls ($plannum) {
		$plans = xml::normalizeseq($this->conf[plans][plan]);
		foreach ($plans as $plan) {
			if ($plan[id]==$plannum) {
				return explode(",",$plan[acls]);
			}
		}
		return array();
	}
	function returnplan ($plannum) {
		$plans = xml::normalizeseq($this->conf[plans][plan]);
		foreach ($plans as $plan) {
			if ($plan[id]==$plannum) {
				return $plan;
			}
		}
		return array();
	}
	function returnplanlimit ($plannum) {
		$plans = xml::normalizeseq($this->conf[plans][plan]);
		$limits = xml::normalizeseq($this->conf[limits][limit]);
		foreach ($plans as $plan) {
			if ($plan[id]==$plannum) {
				if (!$plan[acllimit]) { break; }
				$acllimit = $plan[acllimit];
				foreach ($limits as $limit) {
					if ($limit[id]==$acllimit) { break; }
				}
				return $limit;
			}
		}
		return false;
	}
	

	// FUNCAO GLOBAL (?) - FALTA RETIRAR, JAH TAH NO COMMON
	function tplreplace($tpl,$array) {
		//echo "recebido: $tpl";print_r($array);
		foreach ($array as $k => $v) {
			$v = $this->obj->get($v);
			$tpl = str_replace("{".$k."}",$v,$tpl);
		}
		//echo $tpl."\n";
		return $tpl;
	}
	
	/** 
	 * verifica se o usuario existe,
	 * nao deve instanciar o objeto
	 */
	function validuser($login) {
		$conf = xml::loadxml("forward.xml");
		$conf = $conf[forward];
		$users = xml::normalizeseq($conf[users][user]);
		//print_r($users);
		foreach ($users as $user) {
			//record::msg_log( "testing ".$user[login]."<BR>\n");
			if (trim($user[login])==trim($login)) 
				return true;
		}
				
		return false;
	}
	
	/*** 
	 * guesttotal (key)
	 * retorna o total navegado do usuario ateh o momento
	 * nao precisa instanciar o objeto para funcionar
	 */
	function guesttotal($key) {
		
		$key = strval(sprintf("%06s",strval($key)));
		
		$guestsfile = DIRDATA."/user/guest.totals";
		if (!file_exists($guestsfile)) {
			if (defined("ININTERFACE")) {
				$conn = new Conn();
				$conn->command(message::generate_function("NORMALIZE"));
			}
		}
		//clearstatcache();

		$guests = parse_ini_file($guestsfile);
		
		if (!$guests[strval($key)]) { record::msg_log("key error: $key","guest"); return 0; }
		
		$totals = $guests[strval($key)];
		
		$ret = intval($totals);
		/*
		$totals = explode(",",$totals);
		for ($i=0;$i<count($totals);$i++) {
			$total = explode("-",$totals[$i]);

			if ((trim($total[1])=="")&&($i==count($totals)-1)) {
					$total[1]=time();
			}
			
			if (trim($total[1])!="") {
				$tmp = $total[1]-$total[0];
				$ret = $ret + $tmp;
			}
			
		}
		*/
		return $ret;
	} 	
	

	/************
	 * checkguest_valid (key,ip)
	 * retorna false caso o guest tenha passado seu tempo ou expirado
	 * o ip passado eh apenas para poder logar o ip, nao eh requisito
	 * offset eh somado ao total antes de compara-lo com o limite
	 */
	function checkguest_valid ($key,$ip="",$offset=0) {
		$this->openforwardxml();
		$this->openfwtpl();
		$guests = xml::normalizeseq($this->conf[guests][guest]);
		
		foreach ($guests as $guest) {
			if (trim(strval($guest[key]))==trim(strval($key))) {
				break;
			}
		}
			
		$expire = $guest[expire];
		$limit  = intval($guest[timelimit]);
		
		if ((trim($expire)!="")&&($expire<time())) {
			record::msg_log("Guest Key {".$key."} Ip {".$ip."} Cannot login: key expiration","guest");
			return false;
		}

		//record::msg_log("Checkguest ".$this->guesttotal($key)." with offset $offset","guest");
		
		if ($limit>0&&($this->guesttotal($key)+$offset)>$limit) {
			record::msg_log("Guest Key {".$key."} Ip {".$ip."} Cannot login: exceed of time","guest");
			return false;
		}
		
		return true;
	}			
		
	/*********************
	 * checkguest(key,ip)
	 * autentica o guest e abre uma conexao pra ele
	 */
	function checkguest($key,$ip) {
		
		$key = strval(sprintf("%06s",strval($key)));
		
		$this->openforwardxml();
		$this->openfwtpl();
		$guests = xml::normalizeseq($this->conf[guests][guest]);
		
		$lic = new Checklicense();
		$maxguests = $lic->checkout("maxguests");
		
		$addguestcmd = $this->fwtpl[guest][add][command];
		
		$connguestsfile = DIRTMP."nx_guests";
		$guestsfile = DIRDATA."/user/guest.totals";
		
		if (!file_exists($connguestsfile))
			shell_exec("echo \"\" > $connguestsfile");

		if (!file_exists($guestsfile))
			shell_exec("echo \"\" > $guestsfile");
			
		$connguests = parse_ini_file($connguestsfile);
		$gueststotals = parse_ini_file($guestsfile);
		
		if (!is_bool($maxguests)&&(count($connguests)>=$maxguests)) {
			record::msg_log("Reached max numbers of connections","guest");
			record::dmesg_log("Maxconn Guests");
			return "MAXCONN";
		}
		
		if ($connguests[$key]) {
			record::msg_log("Guest Key {".$key."} Ip {".$ip."} already logged in, disconnecting first","guest");
			$this->disconnect_guest($key,$ip,0);
			clearstatcache();
			//return "FAIL";
		}
		
		
		for ($i=0;$i<count($guests);$i++) {
			//echo "test:".$guests[$i]["key"]."::".$key;
			if (strtoupper(trim($guests[$i]["key"]))==strtoupper(trim($key))) {
				$connguests[$key]=$ip;

				if ($this->checkguest_valid($key,$ip)==false) {
					return "FAIL";
				}
				
				write_ini_file($connguestsfile,$connguests);
				
				//$guesttimes = explode(",",$gueststotals[$key]);
				//$guesttimes[count($guesttimes)] = time()."-";
				//$gueststotals[$key] = implode(",",$guesttimes);
				if (!$gueststotals[$key]) {
					$gueststotals[$key]="0";
				}
				
				write_ini_file($guestsfile,$gueststotals);
				
				//$var[guestip]=$ip;
				//$var[guestkey]=$key;
				//$addguest = conv::tplreplace($addguestcmd,$var);
				//exec($addguest);
				
				$this->createuser_route("guest.$key","",1);
				
				record::msg_log("Guest Key {".$key."} Ip {".$ip."} Connects","guest");				
				return "OK";
			}
		}

		return "FAIL";

	}
	
	function disconnect_guest($key,$ip="",$guestidle=0) {
		$key = strval(sprintf("%06s",strval($key)));
		
		$totalsfile = DIRDATA."/user/guest.totals";
		if (!file_exists($totalsfile)) {
			// cria um arquivo vazio
			shell_exec("echo \"\" > $totalsfile");
		}
		$totals = parse_ini_file($totalsfile);
		
		$connguestsfile = DIRTMP."nx_guests";
		$connguests = parse_ini_file($connguestsfile);
		
		$totals[$key] = $totals[$key] - $guestidle;
		if ($totals[$key]<0) { $totals[$key]=0; }
		
		write_ini_file($totalsfile,$totals); clearstatcache();
		
		// fechando a conexao
		unset($connguests[$key]);
		write_ini_file($connguestsfile,$connguests); clearstatcache();
		
		$var[guestip]=$ip;
		$var[guestkey]=$key;
		
		//$removeguest = conv::tplreplace($removecmd,$var);
		//echo $removeguest;
		//exec($removeguest);
		
		$this->removeuser_route("guest.".$key);

		Network::linktable_remove_user("","guest.$key");
		
		record::msg_log("Guest Key {".$key."} Ip {".$ip."} Disconnected (Idle for {$guestidle} seconds)","guest");

	}
	
	function update_guest($key,$sum) {
		$totalsfile = DIRDATA."/user/guest.totals";
		if (!file_exists($totalsfile)) {
			// cria um arquivo vazio
			shell_exec("echo \"\" > $totalsfile");
		}
		$totals = parse_ini_file($totalsfile);
		$key = strval(sprintf("%06s",strval($key)));
		$totals[$key] = intval($totals[$key]) + $sum;
		write_ini_file($totalsfile,$totals); clearstatcache();
		
	}
	
	function checkuser_limit ($login) {
		$users = xml::normalizeseq($this->conf[users][user]);
		foreach ($users as $user) {
			if ($user[login]==$login) {
				break;
			}
		}
		$limit = $this->returnplanlimit($user[plan]);
		if (is_array($limit)&&$limit[action]=="drop") {
			$userlimit = $limit;
		}
		if (!$userlimit) { return true; }

		$userlimit = explode(" ",$userlimit[traffic]);


		$totalsfile = DIRDATA."user/user.totals";
		$totals = @parse_ini_file($totalsfile);
		$usertotals = explode(" ",$totals[$login]);
		$total_up = $usertotals[0];
		$total_down=$usertotals[1];
		
		if ((($usertotals[0]>$userlimit[0])&&($userlimit[0]!=0)) ||
			(($usertotals[1]>$userlimit[1])&&($userlimit[1]!=0))) {
			// passou o trafego de upload ou download!
			return false;
		}
		return true;
		
		
	}
	
	function checkuser($user,$pass,$ip,$mac) {
		//$this->openforwardxml();
		$conf = new Conf("forward");
		$users = xml::normalizeseq($conf->conf[forward][users][user]);
		$this->conf=$conf->conf[forward];
		$this->openfwtpl();
		
		$noauthcmd = $this->fwtpl[user][noauth][command];
		
		for ($i=0;$i<count($users);$i++) {
			if ($user==$users[$i][login]) {
				$plan = $this->returnplan($users[$i][plan]);
				if ($plan[pppoe]==1) {
					return "PPPOEONLY";
				}
				if ($pass==$users[$i][pass]) {
					if (intval($users[$i][disabled])>0) {
						$dis = intval($users[$i][disabled]);
						$dis = $dis - 1;
						return "DISABLED".intval($dis);
					}


					//echo "UIP: ".$users[$i][ip]."<BR>\nIP:$ip";
					//$arp = new cmd_Arp();
					//$mac = $arp->getmac($ip);
					//$int = $arp->getint($ip);
					
					
					$int = $this->obj->get("`INTERFACE.INTERNAL`");
					$int = $int[device];
					
					$macs = explode(",",trim($users[$i][macs]));
						
						// eh novo (ou alguem mudando de maquina), preciso:
						// 1) pegar o mac, 
						// 2) pegar a placa de rede
						// 3) fixar ip/mac no xml
						// 4) merge(forward)

						
					if ($mac==FALSE||$int==FALSE)
						return "TRYAGAIN";

					// vamos ver se o IP MAC jah tah fixado! se tiver tah feito!
					$fast_ret = $this->fastauth($ip,$mac,1);
					$fast_ret2 = str_replace("\"","",$fast_ret);
					if (substr($fast_ret2,0,2)=="OK") {
						return $fast_ret;					
					}
					file_put_contents("/tmp/saida11",$fast_ret.":$ip,$mac");
					
					$users[$i][macs]=$mac;
					$users[$i][ip]=$ip;
					// $users[$i][int]=$int; - tirei pq se nao tiver ele usa o interno lah
					$users[_num]=count($users);
					$conf->conf[forward][users][user]=$users;
					$this->conf[users][user]=$users;
					
					//$conf->set("forward/users/user/$i/macs",$mac);
					//$conf->set("forward/users/user/$i/ip",$ip);
					//$conf->set("forward/users/user/$i/int",$int);
					
					$conf->write();
					//$conf->printconf();
					clearstatcache();
					//merge("FORWARD");
					
					// AQUI VAI APENAS CRIAR A REGRA DE FORWARD PRO USUARIO PODER NAVEGAR
					record::msg_log("Fixing IP/MAC for user {$user}","forward");
					
					$this->removeuser_forward($user);
					$this->createuser_forward($user,1);
					$this->createuser_route($user,"",1);
					
						
					
					if ($this->checkuser_limit($user)==false) {
						return "OUTLIMIT";
					}
					return "OK";
				} else {
					return "FAIL";
				}
			}
		}
		return "USERFAIL";
		
	}
	
	function fastauth($ip,$mac,$forced=0) {
		$this->openforwardxml();
		$users = xml::normalizeseq($this->conf[users][user]);
		
		
		for ($i=0;$i<count($users);$i++) {
			if ($users[$i][ip]==$ip&&$mac=="pppoe") {
				$found=1;
				break;
			}
			
			if ($users[$i][ip]==$ip&&in_array($mac,explode(",",trim($users[$i][macs])))) {
				$found=1;
				break;
			}
		}
		if (!$found) {
			return "FAIL";
		}
		
		if (intval($users[$i][disabled])>0) {
			return "\"FAIL\",\"forceauth\"";
		}
		
		
		$plan = $this->returnplan($users[$i][plan]);
		
		$user = $users[$i][login];
		$msg = $users[$i][msg];
		
		if ($plan[forceauth]==1 && $forced==0) 
			return "\"FAIL\",\"forceauth\"";
		
		$gateway="";
		
		// verificando se jah estah logado...
		clearstatcache();
		$linktable = @parse_ini_file(DIRTMP."nx_linktable",1);
		foreach ($linktable[gateway] as $int => $val) {
			if (!$linktable[$int]) { continue; }
				foreach ($linktable[$int] as $login => $timelogin) {
					if ($login==$user) {
						// ele jah tah online, vamos desconecta-lo e colocar no mesmo link q tava
						$gateway = $val;
					}
				}
						
		}
		
		// CRIA A REGRA DE FIREWALL com o gateway
		record::msg_log("User {$user} fast login","forward");
		
		$this->createuser_route($user,$gateway,1);
				
		return "\"OK\",\"$msg\"";
		
	}
	
	function disconnect_user ($login,$int,$useridle) {
		if (file_exists(DIRTMP."nx_unroute.$login"))
			shell_exec ("/bin/sh ".DIRTMP."nx_unroute.$login");	
			
		$this->removeuser_route($login);
			
		shell_exec("rm -fr ".DIRTMP."/nx_unroute.$login");
		//shell_exec("rm -fr ".DIRTMP."/nx_route.$login"); //removeuser_route jah apaga

		// tirando do nx_linktable
		if (!$this->linktable) {
			clearstatcache();
			$this->linktable = parse_ini_file(DIRTMP."nx_linktable",1);
			$read=1;
		}
		
		unset($this->linktable[$int][$login]);
				
		if ($read==1) {
			write_ini_file(DIRTMP."nx_linktable",$this->linktable);
			clearstatcache();
			unset($this->linktable);
		}
		
		$user = $this->returnuser($login);
		$ip = $user[ip];
		
		// caso for pppoe, preciso desconecta-lo.
		$dev = trim(shell_exec ("grep \"$ip\\\"\" ".DIRTMP."pppoe-up.*"));
		
		$dev = explode(" ",$dev);
		$dev = trim(str_replace("\"","",$dev[1]));
		//file_put_contents("/tmp/saida13",$ip.$dev);
		if ($dev) {
			exec ("kill `cat /var/run/$dev.pid`");
		}
		
		record::msg_log("User {$login} Disconnected from {$int} (Idle for {$useridle} seconds)","forward");
	}
	
	
	function addcbq($array,$pppoe=0,$ret=0) {
		// recebo:
		// out,width,weight,prio,mark,ip,bounded
		// lista do q eh comando de Cbq:
		$vars=explode(";","DEVICE;RATE;WEIGHT;PRIO;MARK;RULE;BOUNDED;ISOLATED;PARENT;LEAF;#USERIP");
		$i = count($this->cbq)+2;
		if ($i==0) { $i=2; }
		
		$this->cbq[$i][pppoe]=$pppoe;
		
		$var[seq]=$i;
		foreach ($array as $k=>$v) {
			$var[strtolower($k)]=$v; // v2
		
			$k = strtoupper($k);
			if ($k=="OUT") {
				$this->cbq[$i][out]=$v;
				$k="DEVICE";
				$v="$v,10Mbit,1Mbit";

			}
			if ($k=="RATE") {
				$v=$v."Kbit";
			}
			if ($k=="WEIGHT") {
				$v=ceil($v)."Kbit";
			}
			if ($k=="POOL") {
				$this->cbq[$i][pool]=$v;
			}
			if ($k=="POOLPARENT") {
				// salvo o numero do cbq dentro de poolparent para os filhos usarem
				// poolparent[num-pool][download|upload]
				// pode ter mais de um upload (multilink), entao ele salva o i em um array.
				// NAO ESTOU USANDO ESSA VARIAVEL, SALVO O RETORNO NO SHAPERPOOL[i][poolparent]
				$this->poolparent[$this->cbq[$i][pool]][$this->cbq[$i][dir]][]=$i;
			}
			if ($k=="DIR") {
				$this->cbq[$i][dir]=$v;
			}

			if (in_array($k,$vars)) {
				$this->cbq[$i][cbq].=$k."=$v\n";
			}
			
		}
		
		$shaper_out = "# SHAPER IP ".$array["#USERIP"]." ($pppoe)\n";
		$shaper_out = conv::tplreplace($this->fwtpl[cbq][rule],$var)."\n";
		
		if ($ret==1) {
			return $shaper_out;
		} else {
			$this->shaper_out .= $shaper_out;
		}
		
		return sprintf("%04d",$i);
	}
	
	function addchap ($user) {
		$this->chapsecrets .= $user[login]."\t*\t".$user[pass]."\t".$user[ip]."\n";
	}
	
	// poderia utilizar a sessao PRE do tpl pra limpar (?)
	function fileclean() {
		foreach (glob(DIROUT."/forward/*") as $filename) {
			if (substr_count($filename,"pppoe")==0) 
				unlink($filename);
		}
	}
	
	function filecbq() {
		foreach ($this->cbq as $k => $v) {
			if (intval($k)<$this->file_cbq_from) { continue; }
			$k = sprintf("%04d",$k);
			if ($v[pppoe]==1) {
				file_put_contents(DIROUT."/forward/pppoe/cbq-$k",$v[cbq]);
			} else {
				file_put_contents(DIROUT."/forward/cbq-$k",$v[cbq]);
			}
		}
	}
	function filefw() {
		file_put_contents(DIROUT."/forward/firewall.sh",$this->out);
	}
	
	function filechap() {
		file_put_contents(DIROUT."/forward/chap-secrets",$this->chapsecrets);
	}
	
	function filelasts() {
		file_put_contents("/tmp/nx_aclmark",$this->mark);
		file_put_contents("/tmp/nx_cbq",serialize($this->cbq));
	}
	
	function fileshaper() {
		
		file_put_contents(DIROUT."/forward/shaper.sh",$this->shaper_out);
		
		
	}
	
	// retorna o service separado por proto/dport/sport
	// pode receber um `SERVICE` ou os 2 parametros separados
	// retorna um array com todos os proto/dport/sport
	function returnservice ($var2) { 
		// processando o service
		// ser o var2 tiver service, preciso interpretar os itens
		if ($var2[service]) {
			$service2 = $this->obj->get($var2[service]);
			//echo "serv2:";print_r($service2);
			if ($service2[_num]) {
				foreach ($service2 as $k => $v) {
					if (is_array($v))
						$service[]=$v;
				}
			} else {
				$service[0]=$service2;
			}
			
		} else if ($var2[proto]&&$var2[dport]&&$var2[sport]){
/*				$service[proto]=$var2[proto];
				$service[dport]=$var2[dport];
				$service[sport]=$var2[sport]; */
			
			$service = $var2;
			$service[0]=$service;
		} else {
			$service[0][proto]=$service[0][dport]=$service[0][sport]="";
		}
		
		// gambiarra pra colocar o proto no inicio do array	
		if ($service[proto]) {
			$proto = $service[proto];
			unset ($service[proto]);
			$nserv[proto]=$proto;
			$service = array_merge($nserv,$service);
		}
			
		return $service;
	}
	
	function returntpltype () {
		// pego o template q vou usar (baseado no proto)
		$tplacls = xml::normalizeseq($this->fwtpl[acl]);
		
		// normalizo em:
		// tplacls[type]=comando
		for ($j=0;$j<count($tplacls);$j++) {
			$tplproto[$tplacls[$j][type]]=$tplacls[$j][command];
		}
		return $tplproto;
	}
	function returntpl ($var) {
		$tpl = $this->returntpltype();
		
		// o Proto precisa ir antes do dport/sport!
		if ($var[proto]&&$var[proto]!="") {
			$acl .= " ".$this->tplreplace($tpl[proto],$var);
			unset($var[proto]);
		}
		// o -m Time precisa ir antes do timestop/start/days
		if ($var[time]&&$var[time]!="") {
			$acl .= " ".$this->tplreplace($tpl[time],$var);
			unset($var[time]);
		}
		foreach ($var as $k => $v) {
			if (($tpl[$k])&&(trim($var[$k]!=""))) {
				$acl .= " ".$this->tplreplace($tpl[$k],$var);
			}
		}
		return $acl;
	}
	
	/*
	 * Cria as regras de pool (precisa ser antes de processar as shaperrules dos usuarios
	 * pq elas podem ser de pool e o pool precisa estar criado 
	 * salva os pools criados na variavel this->shaperpool
	 */
	function processpool () {
		
		// agora vem o cbq.init
		/* Roteiro:
		 * - pegar interface de saida (saida pra dentro (download-placa de rede do usuario) 
		 * - ou saida pra fora (upload-todas as placas externas ou a especificada no acl)
		 * - calcular o weight
		 * - nao vai ter rule pra saida (upload)
		 * - na entrada (download) nao sei se o mark vai funcionar (TESTAR )
		 * - verificar se vai ter bounded ou isolated
		 * - - se for pelo maximo, BOUNDED=yes, ISOLATED=yes
		 * - - se for pelo pool, PARENT=XXXX, LEAF=sfq, BOUNDED=no, ISOLATED=no
		 * - - - o XXXX refere-se a classe q tem o maximo do pool
		 * - - - precisa ser BOUNDED=yes, ISOLATED=no e LEAF=sfq
		 */
		 /*
		  * Roteiro tecnico:
		  * - Objetos:
		  * 		this.addcbq(array)
		  * 			pega um array e adiciona ao $this.cbq
		  * 			se for pool, tambem adiciona ao $this.pool a referencia
		  * 		$this.pool
		  * 			tabela com os pools e seus ID
		  * 
		  */
		// primeiro: crio as regras para cada pool
		// soh posso criar uma vez, entao basta ver se a variavel
		// this->pools jah foi setada.
		$pools= xml::normalizeseq($this->conf[pools][pool]);
		$sp=0;
		
		if (trim($pools[0])=="") { $pools=array(); }
		
		//echo "pools:";print_r($pools);
		/*
		 *     [0] => Array
			        (
			            [id] => 1
			            [prio] => 3
			            [maxdownload] => 200
			            [maxupload] => 100
			        )
		 */
		
		// para cada placa de rede interna eu tenho q salvar o pool de download lah
		$int_internal=$this->obj->getinterfaces("internal");
		foreach ($int_internal as $k=>$v) {
			foreach ($pools as $kp=>$vp) {
				$shaperpool[$sp][out]=$v[device];
				$shaperpool[$sp][weight]=$vp[maxdownload]/10;
				$shaperpool[$sp][rate]=$vp[maxdownload];
				$shaperpool[$sp][pool]=$vp[id];
				$shaperpool[$sp][prio]=$vp[prio];
				$shaperpool[$sp][dir]="download";
				$sp++;
			}			
		}
		$int_external=$this->obj->getinterfaces("external");
		foreach ($int_external as $k=>$v) {
			foreach ($pools as $kp=>$vp) {
				$shaperpool[$sp][out]=$v[device];
				$shaperpool[$sp][weight]=$vp[maxupload]/10;
				$shaperpool[$sp][rate]=$vp[maxupload];
				$shaperpool[$sp][pool]=$vp[id];
				$shaperpool[$sp][prio]=$vp[prio];
				$shaperpool[$sp][dir]="upload";
				$sp++;			
			}
		}
		//echo "shaperpool:";print_r($shaperpool);
		/*
		 * 
		    [0] => Array
		        (
		            [out] => eth0
		            [weight] => 20
		            [rate] => 200
		            [pool] => 1
		            [prio] => 3
		            [dir] => download
		        )
		 */
		// crio as regras de pool e salvo o seu ID no shaperpool pra ser usado depois
		// essas sao as regras pais (parent) que definem o maximo para as regras filhas usarem
		$varpooldefaults=array("leaf"=>"none","prio"=>"5");
		for ($i=0;$i<count($shaperpool);$i++) {
			$varpool[$i]=array_merge($shaperpool[$i]);
			foreach ($varpooldefaults as $k=>$v) {
				if (!$varpool[$i][$k])
					$varpool[$i][$k]=$v;
			}
			$shaperpool[$i][poolparent] = $this->addcbq($varpool[$i]);
		}
		$this->shaperpool = $shaperpool;
	}
	
	/*
	 * Recebe um array de acls e retorna um array com todos os parametros dos
	 * acls para serem usados no tplreplace (o var) 
	 * antes de dar o tplreplace, precisa mergear com o var do usuario
	 */ 
	function processacl ($acl,$var,$tplacl,$tplacldrop,$int=0,$user=0) {

		$tplproto=$this->returntpltype();
		$tpltime =$this->fwtpl[time][command];
		$tplroute=$this->fwtpl[route][command];
		
		$var[aclmark]=$this->mark;
		$this->mark++;
		if (isset($cmdtime)) 	{ unset($cmdtime);}
		if (isset($cmdacl)) 	{ unset($cmdacl);}
		if (isset($cmddrop)) 	{ unset($cmddrop);}
		if (isset($var2)) 		{ unset($var2);}
		if (isset($vartime)) 	{ unset($vartime);}
		if (isset($shapertemp)) { unset($shapertemp);}
		if (isset($service)) 	{ unset($service);}

		// salvando as definicoes de banda por acl (tudo q eh necessario pra usar depois no cbq.init)
		if ($acl[upload]||$acl[download]||$acl[pool]) {
			foreach ($acl as $k => $v)
				if ($k=="minupload"||$k=="mindownload"||$k=="upload"||$k=="download"||$k=="pool"||$k=="id"||$k=="int"||$k=="out")
					$shapertemp[$k]=$v;
					
			// NAO VAI TER MAIS POOL
			// TOU MANTENDO O CODIGO PARA FUTURAS MODIFICACOES
			// VAI SEMPRE CAIR NO ACLMARK
			/////////////////////////////////////////////////////////
			if ($shapertemp[pool]) {
				// se for uma regra de pool, tenho q usar o usermark
				$shapertemp[mark]=$var[usermark];
			} else {
				// senao uso o aclmark mesmo
				$shapertemp[mark]=$var[aclmark];
			}
			////////////////////////////////////////////////////////
			
			if ((!$shapertemp["int"])&&($int!=0)) 
					$shapertemp["int"]=$int;
			
			if ($shapertemp["int"]) 
				$shapertemp["int"]=$this->obj->get($shapertemp["int"]);
			if ($shapertemp["out"]) 
				$shapertemp["out"]=$this->obj->get($shapertemp["out"]);

			if (is_array($user)) {
				$shapertemp[ip]=$user[ip];
				$shapertemp[user]=$user[login];
			}
			$this->shaperrules[]=$shapertemp;
		}

		// junto as variaveis do usuarios com as do acl
		// var2 = var + useracls[i]
		$var2=@array_merge($var,$acl);
		
		// processando o src e dst
		$var2[src]=$this->obj->get($var2[src]);
		$var2[dst]=$this->obj->get($var2[dst]);
		
		/*
		foreach (array("dst"=>$var2[dst],"src"=>$var2[src]) as $k => $v)
			if (trim($v)=="")
				$var2[$k]=$this->obj->get("`NETWORK.world`");
		*/
		
		
		// processando o service
		$service = $this->returnservice($var2);

		if ($var2[time]) {
			$vartime = $this->obj->get($var2[time]);
			$var2 = array_merge($vartime,$var2);
		}

		if ($var2[timestart]&&
		    		$var2[timestop]&&
		    		$var2[days]) 
		{
			//$cmdtime = " ".$this->tplreplace($tpltime,$var2);
			$var2[time]=1;
			if (($var2[timestart]=="00:00"||$var2[timestart]=="0:00"||$var2[timestart]==0||$var2[timestart]=="")
				&&
				($var2[timestop]=="00:00"||$var2[timestop]=="0:00"||$var2[timestop]==0||$var2[timestop]=="")) {
					
				unset($var2[timestop]);
				unset($var2[timestart]);
			}
			

				
			// se nao tiver o cmdacl eh pq eh um acl soh de time pra controlar banda
			if (!$cmdacl) 
				$cmdacl[0]=" ";
		}

		//print_r($var2);
		//print_r($service);

		// pego o template q vou usar (baseado no proto)
		$cmdacl=array();
		
		if (is_array($service)) {
			foreach ($service as $s=>$v) {
					//$cmdacl[]=$this->conv::tplreplace($tplproto[$service[$s][proto]],array_merge($service[$s],$var2));
					$cmdacl[]=$this->returntpl(array_merge($service[$s],$var2));
			}
		}

		//echo "cmdacl:"; print_r($cmdacl);
	
		
		for ($a=0;$a<count($cmdacl);$a++) {
			$var2[acl]=$cmdacl[$a].$cmdtime;
			// escrevendo os comandos de acl
			//$tpl3 .= $this->tplreplace($tpl31cmd,$var2)."\n";
			$tpl3 .= $this->tplreplace($tplacl,$var2)."\n";
		}
	
		// colocando comando de drop se tiver
		if (isset($acl[drop]))
			$tpl4 .= $this->tplreplace($tplacldrop,$var2)."\n";


		// forcando uma rota se tiver out no acl
		if (isset($acl[gw])) {
			// preciso pegar o gateway e a placa
			$tpl5 .= $this->tplreplace($tplroute,$var2)."\n";
		}

		return $tpl3."\n".$tpl4."\n".$tpl5;	
	}
	/*********
	 * recebe um shaperrules
	 * adiciona ao cbq automaticamente, nao retorna nada
	 */
	function processshaper () { 
		
		$shaperpool = $this->shaperpool;
		$shaperrules= $this->shaperrules;
				
		// segundo: comeco a ler cada shaperrule e vou criando os cbqs

		/* echo "shaperrules:";print_r($shaperrules);
		shaperrules:Array
		(
		    [0] => Array
		        (
		            [id] => 1
		            [download] => 900
		            [upload] => 800
		            [mark] => 2
		            [ip] => 
		        )
		
		    [1] => Array
		        (
		            [id] => 1
		            [download] => 900
		            [upload] => 800
		            [mark] => 4
		            [ip] => 192.168.31.12
		        )
		*/
		
		
		//echo "shaperpool:";print_r($shaperpool);
		$varruledefaults=array("bounded"=>"no","isolated"=>"no","prio"=>"5");
		$varrulepooldefaults=array("bounded"=>"no","LEAF"=>"sfq","prio"=>"5");
		for ($i=0;$i<count($shaperrules);$i++) {
			unset($varrule);
			if ($shaperrules[$i][pool]) {
				$varrule=$varrulepooldefaults;
				$varrule[mark]=		$shaperrules[$i][mark];
				// comentado abaixo pq tou pegando o prio do parent (faz sentido neh?)
				//if ($shaperrules[$i][prio]) // pode ter tambem prio pra download/upload no futuro
				//	$varrule[prio]=		$shaperrules[$i][prio];
				
				// perai.. falta uma coisa!
				// preciso pegar o pai pra marcar esse como filho, tambem colocar o device do pai aqui
				
				// roteiro:
				// vou dar um loop no shaperpool, procurando pelo [pool]=meupool
				// quando achar eu mando um addcbq, usando os dados q jah tenho, mais
				// os dados do shaperpool encontrado
				

				foreach ($shaperpool as $k => $v) {
					if ($v[pool]==$shaperrules[$i][pool]) {	
						$varrule[out]=$v[out];
						$varrule[parent]=$v[poolparent];
						$varrule[prio]=$v[prio];
						if ($v[dir]=="download") {
							$varrule[rate]=$shaperrules[$i][mindownload];
						} else if ($v[dir]=="upload") {
							$varrule[rate]=$shaperrules[$i][minupload];
						}
						$varrule[weight]=$varrule[rate]/10;
					
						$this->addcbq($varrule);
						
						
						if ($v[dir]=="download") {
							$varrule[out]="INTERFACE";
							if (trim($shaperrules[$i][ip])!="") {
								$varrule["#USERIP"]=$shaperrules[$i][ip];
								$this->addcbq($varrule,1);
							}
						}
						
					}
				}
				
			} else {

				$varrule=$varruledefaults;
				$varrule[mark]=		$shaperrules[$i][mark];
				
				// nao sei se vai precisar do rule! FALTA TESTAR (v) !NAOPRECISA!
				//$varrule[rule]=		$shaperrules[$i][ip]; 
				// se precisar vai ficar em upload e download
				

				if ($shaperrules[$i][prio]) // pode ter tambem prio pra download/upload no futuro
					$varrule[prio]=		$shaperrules[$i][prio];

				if ($shaperrules[$i][download]) {
					// para o download
					$varrule[rate]=	$shaperrules[$i][download];
					$varrule[weight]=$varrule[rate]/10;					

					// se shaperrule nao tiver interface, vamos pegar todas as internas
					if ($shaperrules[$i]["int"]) {
						$varrule[out]=$shaperrules[$i]["int"];
						$this->addcbq($varrule);
					} else {
						// para cada interface, eu adiciono o cbq
						$varrule[outs]=		$this->obj->getinterfaces("internal");
						foreach ($varrule[outs] as $k=>$v) {
							$varrule[out]=$v[device];
							$this->addcbq($varrule);
						}		
					}
					
					// para cada regra de download, tambem preciso adiciona-la ao
					// /etc/nexus/shaper-pppoe
					// no caso do cliente conectar via pppoe
					$varrule[out]="INTERFACE";
					if (trim($shaperrules[$i][ip])!="") {
						$varrule["#USERIP"]=$shaperrules[$i][ip];
						$this->addcbq($varrule,1);
					}
					
				}
				if ($shaperrules[$i][upload]) {
					// para o upload
					// se nao tiver especificado o out, eu coloco pra todas as placas externas
					$varrule[rate]=	$shaperrules[$i][upload];
					$varrule[weight]=$varrule[rate]/10;
					if ($shaperrules[$i][out]) {
						$varrule[out]=		$shaperrules[$i][out];
						$this->addcbq($varrule);
					} else {
						$varrule[outs]=		$this->obj->getinterfaces("external");
						foreach ($varrule[outs] as $k=>$v) {
							$varrule[out]=$v[device];
							$this->addcbq($varrule);
						}
					}
				}
			}
		}
	}

	/*******
	 * createuser_forward
	 * essa funcao cria e executa as regras de firewall do usuario (fixa mac/ip)
	 * retorna 1 se OK, 
	 * -1 caso o usuario esteja desabilitado
	 * -2 caso o usuario nao exista
	 */
	function createuser_forward ($user,$execute=0) {
		$login=$user;
		$user = $this->returnuser($user);
		if (is_bool($user) && $user==false )
			return -1;

		$plan = $this->returnplan($user[plan]);

		/*
		if (intval($user[disabled])>=1) {
			return -1;
		}
		*/
		file_put_contents("/tmp/saida7",$user[macs]);
		
		/*
		if ($execute==1) {
			$this->mark = int_val(@file_get_contents("/tmp/nx_aclmark"));
			if ($this->mark==0) { $this->mark=1;}
			
			//$this->cbq = unserialize(@file_get_contents("/tmp/nx_cbq"));
			//if (!$this->cbq) { $this->cbq = array(); }
			//$this->file_cbq_from = count($this->cbq)-1;
		}
		*/
		
		// POPULANDO O VAR ---------------------------
		
		$var[userchain] = $user[login];

		// vou ter q salvar esse mark em uma tabela contendo o num
		// e o que � pra fazer, pra usar depois no cbq
		$var[usermark]  = $this->mark;
		$this->mark++;
		$var[userip]    = $user[ip];
	
		// O MAC EH SEPARADO POR VIRGULA
		//if ($user[macs]) 
		//	$usermacs   = explode(",",$user[macs]);
			
		$var[usermac] = $user[macs];
		$var[userlogin] = $user[login];
		
		$internal = $this->obj->get("`INTERFACE.INTERNAL`");
		$internal = $internal[device];
		
		if (!$user["int"]) 
			$var[userint]=$internal;
		else 
			$var[userint]	= $user["int"];
		
		// FIM POPULANDO O VAR ------------------------------------
		
		$cmd = $this->processuser ($user,$var);

		if ($user[macs]&&$user[ip])
			$cmd .= $this->processfixuser($user,$var);
		else if ($plan[pppoe]==1)
			$cmd .= $this->processfixuser($user,$var,1);

		$cmd = conv::cleanout($cmd);

		if ($execute==1) {
			$ret = shell_exec ($cmd);
			file_put_contents("/tmp/saida6",$ret);
			file_put_contents("/tmp/nx_fix.$login",$cmd);
			
			$this->filelasts();
			
		} else {
			$cmd .= $this->processuseracls($user,$var);
			$this->out .= "\n".$cmd."\n";
		}
		
		file_put_contents("/tmp/nx_forward.$login",$cmd);
	}
	
	function removeuser_forward ($user) {
		//file_put_contents("/tmp/saida9",DIRTMP.".nx_unfix.$user");
		if (file_exists(DIRTMP."nx_unfix.$user"))
			file_put_contents("/tmp/saida8",shell_exec ("/bin/sh ".DIRTMP."nx_unfix.$user"));	
	}
	
	function createuser_route ($user,$gateway="",$execute=0) {
		$this->openfwtpl();

		$nxoff = explode(",",file_get_contents(DIRTMP."nx_off.tmp"));
		if (!$this->linktable) {
			clearstatcache();
			$this->linktable = @parse_ini_file(DIRTMP."nx_linktable",1);
			$read=1;
		}
		

		// vamos ver se eh usar ou guest, essa funcao serve pra ambos
		$tmpuser = explode(".",$user);
		if ($tmpuser[0]=="guest") {
			$is_guest=1;
			$guest = strval(sprintf("%06s",strval($tmpuser[1])));
			$login = "guest.".$guest;
			
			$guest = $this->returnguest($guest);
			$guest[key] = strval(sprintf("%06s",strval($guest[key])));
			
			if (trim($guest[plan])!="")
				$plan = $guest[plan];
			else
				$plan = $this->conf[guestconfig][plan];
		
			$plan=$this->returnplan($plan);
			
		} else {
			$login=$user;
			$user = $this->returnuser($user);
			if (is_bool($user) && $user==false )
				return -1;
			$plan = $this->returnplan($user[plan]);
			
		}
		
		ob_start();
		print_r($plan);
		print_r($this->linktable);
		file_put_contents("/tmp/saida21",ob_get_contents());
		ob_end_clean();
		
		
		// se tem plan[link] e nao eh AUTO, entao o gateway jah estah setado.
		if ($plan[link] && $plan[link]!="auto") {
			if ($this->linktable[gateway][$plan[link]]) {
				$gateway = $this->linktable[gateway][$plan[link]];
				$bestchoice = $plan[link];
			}
			
	
			// mas se o link tah fora do ar,
			if (in_array($plan[link],$nxoff)) {
				// eu vejo se posso joga-lo para outro link..
				// linkfail=0, USO OUTRO GATEWAY
				// =1, USO O MESMO GATEWAY Q SABEMOS Q TAH RUIM
				if (!$plan[linkfail] || $plan[linkfail]==0) {
					unset($gateway);
					unset($bestchoice);
				}
			}

		}
		

		//ob_start();
		
		$weights = $this->linktable[weight];
		$gateways = $this->linktable[gateway];
			
		//echo "linktable:";print_r($this->linktable);
		
		// se recebeu link, entao cria a regra direto, nem pensa em qual link vai
		if ($gateway=="") {
			/* ROTEIRO
			 * 1. ver qual o link tah mais liberado
			 * 2. adicionar o usuario a esse link em um arquivo
			 * 3. criar a regra dele baseado no gateway escolido
			 */
			
			// 1. verificando o link mais liberado
	

			$tmp_maxprop=0;
			foreach ($weights as $int => $weight) {
				if (!$gateways[$int] || trim($gateways[$int])=="")
					continue;
				
				// vendo quantos usuarios tem online nesse link
				if ($this->linktable[$int])
					$online = count($this->linktable[$int]);
				else 
					$online =0;
				
				// se tiver mais de zero, calcula proporcao
				// se tiver zero, escolho esse link
				if ($online!=0) {
					$prop[$int] = $weight / $online;
				} else {
					$bestchoice=$int;
					break;
				}
				
				// se a proporcao desse link for maior q a ultima maior, escolho ele
				if ($prop[$int]>$tmp_maxprop) {
					$tmp_maxprop = $prop[$int];
					$bestchoice = $int;
				}
			}
			$gateway = $this->linktable[gateway][$bestchoice];
		} else {
			// nesse caso jah chegou com gateway, soh preciso pegar a placa
			if (!$bestchoice) {
				foreach ($gateways as $int => $gw) {
					if ($gw==$gateway) {
						$bestchoice=$int;
						break;
					}
				}
			}
		}
		
		record::msg_log("Link chosen to {$login} int {$bestchoice} via {$gateway}","network");
		
		$this->linktable[$bestchoice][$login]=time();
		
		if ($is_guest) {
			// preciso de guestip e guestkey
			$nxguests = parse_ini_file(DIRTMP."nx_guests");
			$guestip = $nxguests[$guest[key]];
			$var[userip]=$guestip;
			$var[userchain]="guests.".$guest[key];
			
			$tplcmd = $this->fwtpl[guest][add][command];
			$tpluncmd = $this->fwtpl[guest][remove][command];
		} else {
			// preciso de userip, usermac, gateway, userchain
			$var[userip]=$user[ip];
			$var[usermac]=$user[macs];
			$var[userchain]=$user[login];

			
			$tplcmd = $this->fwtpl[user][route][command];
			$tpluncmd = $this->fwtpl[user][unroute][command];
		}
		$var[gateway]=$gateway;
		$var[int]=$bestchoice;
		
		$tpl = conv::tplreplace($tplcmd,$var)."\n";
		
		$tplun = conv::tplreplace($tpluncmd,$var)."\n";
		
		
		if ($execute==1) {
			// tento remover soh pra evitar duplicados
			$this->removeuser_route($login);
			
			$ret = shell_exec($tpl);
			file_put_contents("/tmp/saida5",$ret);
			file_put_contents("/tmp/saida5b",$tpl);
		}
		
		if ($read==1) {
			write_ini_file(DIRTMP."nx_linktable",$this->linktable);
			clearstatcache();
			unset($this->linktable);
		}
		
		file_put_contents(DIRTMP."nx_unroute.$login",$tplun);
		file_put_contents(DIRTMP."nx_route.$login",$tpl);
		
		return $tpl."\n";
		
	}
	
	function removeuser_routes($login) {
		// aqui devera apagar todas as rotas
	}
	function removeuser_route ($user) {
		if (file_exists(DIRTMP."nx_unroute.$user"))
			shell_exec ("/bin/sh ".DIRTMP."nx_unroute.$user");
		if (file_exists(DIRTMP."nx_route.$user"))
			shell_exec ("rm -fr ".DIRTMP."nx_route.$user");
	}
		
	
	function processuser($user,$var) {
			
		//echo "PROCESSANDO ".$user[login]."\n";
		//print_r($var);
			
		// agora nao vai mais sair, sempre cria as regras de ACL
		/*
		if (!$user[ip]&&!$user[macs]) {
			return;
		}
		*/
		/*
		if (intval($user[disabled])>=1) {
			return;
		}
		*/
		
	
		$tpl1cmd = $this->fwtpl[user][command];
				
		$tpl1 = $this->tplreplace($tpl1cmd,$var);
	
		$out .= "# USER ".$user[login]."\n";
		$out .= $tpl1."\n";

		//echo "OUT: $out";

		return $out;
	
	}
	
	function processfixuser ($user,$var,$pppoe=0) {
		if ($pppoe==1) {
			$tpl1cmd = $this->fwtpl[user][pppoefix][command];
			$tplun1cmd = $this->fwtpl[user][pppoeunfix][command];
		} else {
			$tpl1cmd = $this->fwtpl[user][fix][command];
			$tplun1cmd = $this->fwtpl[user][unfix][command];
		}
				
		$tpl1 = $this->tplreplace($tpl1cmd,$var);
		$tplun1 = $this->tplreplace($tplun1cmd,$var);
	
		$out .= "# USER MAC ".$user[login]."\n";
		$out .= $tpl1."\n";
		
		file_put_contents(DIRTMP."nx_unfix.".$user[login],$tplun1);
		
		return $out;
	}
	
	function processuseracls ($user,$var) {
		
		/////////////////////////////////////////////////
		// terceiro, quarto e quinto: comandos por acl
		$tpl3cmd = $this->fwtpl[user][preacl][command]."\n".$this->fwtpl[user][acl][command];

		$tpl4cmd = $this->fwtpl[user][acldrop][command];
		
		$tpl5cmd = $this->fwtpl[route][command];
	
		// para cada acl do usuario...
		$a=0;
		if ($user[acls]) {
			$acls = $user[acls];
			$user[acls]=explode(",",$user[acls]);
		} else if ($user[plan]) {
			$acls = $this->returnplanacls($user[plan]);
			
			$plan = $this->returnplan($user[plan]);
			if (intval($plan[pppoe])==1)
				$this->addchap($user);
				
		}


		if ($acls) {
			for ($i=0;$i<count($acls);$i++) {
				$useracls[$a] = $this->returnacl($acls[$i]);
				// se o acl nao existe, simplismente nao tento usa-lo
				// deixo essa inconsistencia com a interface
				if ($useracls[$a]==-1) 
					unset($useracls[$a]);
				else 
					$a++;
			}
		} else {
			$useracls = array();
		}
	
	
		//print_r($useracls);
		$tpltime = $this->fwtpl[time][command];

		$shaperrules=array();
		
		if ($var[userint])
			$int=$var[userint];
		else
			$int=0;
		
		// agora que jah tenho os acls q vou usar, comeco a criar os comandos a partir deles
		for ($i=0;$i<count($useracls);$i++) {
			$tplacl .= $this->processacl($useracls[$i],$var,$tpl3cmd,$tpl4cmd,$int,$user);
		}

		return "# USER ACLS ".$user[login]."\n".$tplacl."\n";

	}
	
	function processrule () { 
		$rules = xml::normalizeseq($this->conf[rules][rule]);
		
		$tplacl = $this->fwtpl[rule][acl][command];
		$tplacldrop = $this->fwtpl[rule][acldrop][command];
		$var = array();
		
		//function processacl ($acl,$var,$tplacl,$tplacldrop,$int=0) {
		
		for ($i=0;$i<count($rules);$i++) {
			$rule=$rules[$i];
			$acl=$this->returnacl($rule[acl]);
			$cmd .= $this->processacl($acl,$var,$tplacl,$tplacldrop);
		}
		$this->out .= "\n# RULES\n".$cmd;
	}
	
	function processinterface() {
		$tplint = $this->fwtpl["interface"][command];
		$networks = $this->obj->get("`NETWORK.INTERFACE.INTERNAL`");
		//print_r($networks);
		
		$var=array();
		foreach ($networks as $k => $v) {
			$var["interface"]=$k;
			$var[network]=$v;
			$var[out]=$k;
			$this->out .= "\n# INTERNAL INTERFACE: $k\n".$this->tplreplace($tplint,$var)."\n";
			$shaper_out .= conv::tplreplace($this->fwtpl[cbq]["interface"],$var)."\n";
		}
		
		
		// extrenas agora
		$tplint = $this->fwtpl["interface"][external][command];
		$networks = $this->obj->get("`NETWORK.INTERFACE.EXTERNAL`");
		//echo"EXTERNAL";print_r($networks);
		
		$var=array();
		foreach ($networks as $k => $v) {
			$var["interface"]=$k;
			$var["network"]=$v;
			$var["ip"]=$this->obj->get("`HOST.INTERFACE.$k`");
			$this->out .= "\n# EXTERNAL INTERFACE: $k\n".$this->tplreplace($tplint,$var)."\n";
			
			$var[out]=$k;
			$shaper_out .= conv::tplreplace($this->fwtpl[cbq]["interface"],$var)."\n";
		}
		
		$this->shaper_out = $shaper_out.$this->shaper_out;
		
	}
	function processglobal() {
		$tplglobal = $this->fwtpl["global"][command];
		$var = array();
		$this->out .= "\n# GLOBAL\n".$this->tplreplace($tplglobal,$var)."\n";
	}
	function processclean() {
		$tplclean = $this->fwtpl["clean"][command];
		$var = array();
		$this->out .= "\n# CLEAN\n".$this->tplreplace($tplclean,$var)."\n";
		
	}
	
	function processtraffic() {
		$traffic = xml::normalizeseq($this->conf[traffics][traffic]);
		//print_r($traffic);
		// primeiro:
		// pego o template
		$tpltraffic = $this->fwtpl["traffic"][command];
		
		//print_r($traffic);
		
		// para cada traffic
		
		for ($i=0;$i<count($traffic);$i++) {
			if (trim($traffic[$i])=="") { continue; } 
			
			$var=array();
			// jogo aname e aaddr no var
			$var[aname]="traffic".$traffic[$i][id];
			$var[aaddr]=$this->obj->get($traffic[$i][addr]);
			//$var[dst]=$var[src]=$this->obj->get("`NETWORK.world`");

			// processando o service
			$service = $this->returnservice($traffic[$i]);
			$cmdacl=array();
			if (is_array($service)) {
				foreach ($service as $s=>$v) {
//$cmdacl[]=$this->tplreplace($tplproto[$service[$s][proto]],array_merge($service[$s],$traffic[$i],$var));
					$cmdacl[]=$this->returntpl(array_merge($service[$s],$traffic[$i],$var));
				}
			}
			for ($a=0;$a<count($cmdacl);$a++) {
				$var[acl]=$cmdacl[$a];
				$out .= $this->tplreplace($tpltraffic,$var)."\n";
			}
		}
		$this->out .= "\n# TRAFFIC\n".$out;
	}
	function processservices() {
		$tplserv0=xml::normalizeseq($this->fwtpl[services][service]);
		
		foreach ($tplserv0 as $s)
			$tplserv[$s[id]]=$s[command];
		
		$ints=xml::normalizeseq($this->netconf[interfaces]["interface"]);
		for ($i=0;$i<count($ints);$i++) {
			unset($var);

			$intfw=$ints[$i][firewall];
			$var["interface"]=xml::getxmlval("device",$ints[$i]);
			
			$out .= "\n# FIREWALL ".$var["interface"]."\n";
			
			foreach ($intfw as $sname=>$sfw) {
				$var[action]=xml::getxmlval("action",$sfw);
				$out .= conv::tplreplace($tplserv[$sname],$var)."\n";
			}
		}
		$this->out .= $out;
	}
	
	function processguest ($guest) {

		if (trim($guest[plan])!="") {
			$acls = $this->returnplanacls($guest[plan]);
		} else {
			$acls = $this->returnplanacls($this->conf[guestconfig][plan]);
		}
		
		$tplguest = $this->fwtpl[guest][command];
		
		
		$tpl3cmd = $this->fwtpl[user][preacl][command]."\n".$this->fwtpl[user][acl][command];
		$tpl4cmd = $this->fwtpl[user][acldrop][command];
		
		//print_r($acls);
		
		if ($acls) {	
			$a=0;
			for ($i=0;$i<count($acls);$i++) {
				$useracls[$a] = $this->returnacl($acls[$i]);
				// se o acl nao existe, simplismente nao tento usa-lo
				// deixo essa inconsistencia com a interface
				if ($useracls[$a]==-1) 
					unset($useracls[$a]);
				else 
					$a++;
			}
		} else {
			$useracls = array();
		}
		//print_r($useracls);
		
		$var[userchain]="guests.".$guest[key];
		
		$tplguest = conv::tplreplace($tplguest,$var)."\n";
		
		for ($i=0;$i<count($useracls);$i++) {	
			$tplacl .= $this->processacl($useracls[$i],$var,$tpl3cmd,$tpl4cmd);
		}
		
		$this->out .= "# GUEST ".$guest[key]."\n";
		$this->out .= $tplguest."\n".$tplacl;


	}
	
	function processdsluser () {
		$ints=xml::normalizeseq($this->netconf[interfaces]["interface"]);
		for ($i=0;$i<count($ints);$i++) {
			if (trim($ints[$i][assignment])=="dsl"&&trim($ints[$i][type])=="external") {
				$chap[login]=$ints[$i][dsluser];
				$chap[pass]=$ints[$i][dslpass];
				$this->addchap($chap);
				unset($chap);
			}
		}
	}
	
	function processpublish() {
		$ints = xml::normalizeseq($this->netconf[interfaces]["interface"]);
		$pubs = xml::normalizeseq($this->conf[publishs][publish]);
		
		$tpl  = $this->fwtpl[publish]["interface"][command];

		$eints = $this->obj->getinterfaces("external");

		foreach ($pubs as $pub) {
			foreach ($eints as $eint) {	
				$var["interface"] = $eint[device];
				$var[proto] = $pub[proto];
				$var[newip] = $this->obj->get($pub[newip]);
				
				if (trim($var[newip])=="") { continue; }
				
				if ($pub[newdport])
					$var[newdport] = $pub[newdport];
				else 
					$var[newdport] = $pub[dport];
				$var[dport] = $pub[dport];
				
				$out .= conv::tplreplace($tpl,$var)."\n\n";
				unset($var);
			}
		}
		
		if ($out) {
			$this->out .= "# PUBLISHED SERVERS\n\n";
			$this->out .= $out;
		}
	}
	

	function openforwardxml () {
		if (!$this->conf) { 
			$this->conf = xml::loadxml("forward.xml");
			$this->conf = $this->conf[forward];
		}
		//print_r($this->conf);
	}
	
	function opennetworkxml() {
		if (!$this->netconf) {
			$this->netconf = xml::loadxml("network.xml");
			$this->netconf = $this->netconf[network];
		}
	}
	
	function openfwtpl () {
		if (!$this->fwtpl) 
			$this->fwtpl = xml::loadxml(DIRTPL."/forward.xml.tpl");
	}
	
	/* TOU TIRANDO PRA PODER MERGEAR SEM O NETWORK
	 * COMO SEMPRE VAI MERGEAR-
	 * OU TUDO OU TUDO-MENOS-NETWORK
	 * acredito q nao haja problemas
	function dependency () {
		return array("NETWORK");
	}
	*/
	
	function merge() {
	
		include_once "common.nx";
		
		echo "Begin forward\n";
		
		echo "Step 1\n";
		
		$lic = new Checklicense();
		$maxusers = $lic->checkout("maxusers");

		$this->openforwardxml();	
		$this->opennetworkxml();
		$this->openfwtpl();
		
		echo "Step 2\n";
	
		// precisa ser o PRIMEIRO
		$this->processclean();
	
		// regras globais
		$this->processglobal();
		
		echo "Step 3";
		
		//$this->processpool();

		$users = xml::normalizeseq($this->conf[users][user]);
				
		for ($i=0;$i<count($users);$i++) {
			if (!is_bool($maxusers)&&(($i+1)>$maxusers)) {
				record::msg_log("Max users reached","license");
				break;
			}
			//$this->processuser($users[$i]);
			$this->createuser_forward($users[$i][login]);
			echo ".";
		}
		
		echo "\nStep 4";
		
		// regras de guest
		$guests = xml::normalizeseq($this->conf[guests][guest]);
		for ($i=0;$i<count($guests);$i++) {
			// verificar se o credito nao tah expirado pra evitar lixo
			
			$this->processguest($guests[$i]);
			echo ".";
		}
		echo "\nStep 4\n";
			
		// regras de trafego
		$this->processtraffic();
		
		// regras de rule (?) 
		// sao simplismente acls n�o atrelados a usuarios
		
		echo "Step 5\n";
		$this->processrule();
		
		
		echo "Step 6\n";
		$this->processshaper();
		

		echo "Step 7\n";
		// regras por interface e regras de cbq da interface
		$this->processinterface();	
		
		echo "Step 8\n";
		// regras de input dos servicos
		$this->processservices();	
		
		
		echo "Step 9\n";
		// os usuarios/senhas das placas com DSL
		$this->processdsluser();


		echo "Step 10\n";
		// portas publicadas
		$this->processpublish();

		// normalizando

		$this->out = conv::cleanout($this->out);

		$this->fileclean();
		$this->filechap();
		$this->filefw();
		$this->filecbq();
		$this->fileshaper(); //v2
		
		// armazena os ultimos ponteiros pra usar no createuser_forward independente
		$this->filelasts();
		
		//echo $this->out;
		
		echo "SHAPEROUT:\n".$this->shaper_out."--------------------\n";

		echo "End forward\n";
	}
	
	function __construct () {
		$this->obj = new Object();
	}
	
}
?>
