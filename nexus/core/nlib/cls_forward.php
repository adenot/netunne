<?php


class Forward {
	
	// Listas do XML (normalizadas)
	var $users;
	var $plans;
	var $acls;
	var $limits;
	var $guests;
	var $traffics;
	var $interfaces;
	var $publishs;
	var $ipplan;
	var $aclstemplate;
	
	var $mark;
	var $cbq;
	var $cbqnum;
	var $cbqnum_initial;
	var $marknums;
	
	var $linktable;
	var $nxoff;
	var $usermarks;
	
	var $usercbqnums;
	var $cbqnums;
	
	var $removed_users;
	var $changed_users;
	
	var $connguests;
	var $gueststotals;
	
	var $connpppoe;

	var $lic;
	var $lic_max_reached=false;
	var $maxusers;
	
	var $obj;
	var $obj_conf;
	var $datalog;
	
	var $shaperpool;
	var $shaperrules;
	
	var $internal;
	var $internals;
	var $internal_networks;
	var $externals;
	
	var $user_out;
	var $out;
	var $shaper_out;
	var $user_pppoe_out;
	
	var $conf;
	var $netconf;
	var $oldconf;
	
	
	/**
	 * TODO
	 * - refazer o publish do usuario se ele mudar de ip
	 */
	function __construct ($lic=false) {
		
		//echo microtime()."\n";

		$this->openforwardxml();
		
		//echo "apos openforward ".microtime()."\n";

		//$this->openoldforwardxml();
		
		$this->openfwtpl();
		$this->openlinktable();
		$this->opennxoff();
		$this->openmark();
		$this->opencbqnum();
		$this->opennetworkxml();
		$this->openguests();
		$this->openpppoe();
		
		$this->acls		= xml::normalizeseq($this->conf[acls][acl]);
		$this->users 	= xml::normalizeseq($this->conf[users][user]);
		$this->plans 	= xml::normalizeseq($this->conf[plans][plan]);
		$this->limits 	= xml::normalizeseq($this->conf[limits][limit]);
		$this->guests 	= xml::normalizeseq($this->conf[guests][guest]);
		$this->traffics	= xml::normalizeseq($this->conf[traffics][traffic]);
		$this->interfaces=xml::normalizeseq($this->netconf[interfaces]["interface"]);
		$this->publishs	= xml::normalizeseq($this->conf[publishs][publish]);
		
		
		$this->aclstemplate = xml::normalizeseq($this->fwtpl[aclstemplate][template]);
		
		$this->obj = new Object();
		
		
		$int 						= $this->obj->get("`INTERFACE.INTERNAL`");
		$this->internal 			= $int[device]; unset($int);
		$this->internals			= $this->obj->getinterfaces("internal");
		$this->internal_networks 	= $this->obj->get("`NETWORK.INTERFACE.INTERNAL`");
		$this->externals			= $this->obj->getinterfaces("external");
		
		//echo microtime()."\n";
		
		// organizando pelo ip/plano para usar na lista do proxy
		foreach ($this->users as $user) {
			if (trim($user[ip])!="") 
				$this->ipplan[$user[ip]]=$user[plan];
		}
		
		if ($lic) {
			$this->lic=$lic;
		} else {
			$this->lic = new Checklicense();
		}
		
		$user_out = array();
		
		$this->maxusers = $this->lic->checkout("maxusers");
		
		if (!is_bool($this->maxusers)&&(count($this->users)>$this->maxusers)) {
			$this->lic_max_reached=true;
			record::dmesg_log("Max user limit reached");
		}
		
		$this->datalog = new datalog();
		
		
	}
	
	
	function merge() {
	
		include_once "common.nx";
		
		echo "Begin forward\n";
		
		echo "Step 1\n";
		
		$maxusers = $this->lic->checkout("maxusers");
		
		echo "Step 2\n";
	
		// precisa ser o PRIMEIRO
		$this->processclean();
	
		// regras globais
		$this->processglobal();
		
		echo "Step 3";
		
		//$this->processpool();

		/*
		$this->trackchanges();
		$users = $this->users;
		for ($i=0;$i<count($users);$i++) {
			if (!is_bool($maxusers)&&(($i+1)>$maxusers)) {
				record::msg_log("Max users reached","license");
				break;
			}
			
			//$this->processuser($users[$i]);
			
			if (in_array($users[$i][login],$this->changed_users)) {
				$this->createuser_forward($users[$i][login]);
				echo ".";
			}
		}
		*/
		

		// verificando se jah estah logado...
		$linktable = $this->linktable;
		
		foreach ($this->linktable[gateway] as $int => $val) {
			if (!$this->linktable[$int]) { continue; }
				foreach ($this->linktable[$int] as $login => $timelogin) {
					unset($this->linktable[$int][$login]);
					$this->merge_user($login,0);
				}
		}
		
		echo "\nStep 4";
		$this->processpppoe();
		
		/*
		// regras de guest
		$guests = $this->guests;
		for ($i=0;$i<count($guests);$i++) {
			// verificar se o credito nao tah expirado pra evitar lixo
			
			$this->processguest($guests[$i]);
			echo ".";
		}
		*/
		echo "\nStep 5\n";
			
		// regras de trafego
		$this->processtraffic();
		
		// regras de rule (?) 
		// sao simplismente acls nï¿½o atrelados a usuarios
		
		echo "Step 6\n";
		$this->processrule();
		
		echo "Step 7\n";
		//$this->processshaper();

		echo "Step 8\n";
		// regras por interface e regras de cbq da interface
		$this->processinterface();	
		
		echo "Step 9\n";
		// regras de input dos servicos
		$this->processservices();	
		
		echo "Step 10\n";
		// os usuarios/senhas das placas com DSL
		$this->processdsluser();

		echo "Step 11\n";
		// portas publicadas
		$this->processpublish();

		$this->processproxylist();
		
		//$this->fileclean();
		$this->filechap();
		$this->filefw();
		$this->filecbq();
		$this->fileshaper(); //v2
		$this->fileuser(); //v2
		$this->filecbqnum();
		$this->filemark();
		$this->filelinktable();

		
		// armazena os ultimos ponteiros pra usar no createuser_forward independente
		$this->filelasts();
		
		//echo $this->out;
		
		echo "End forward\n";
		
	}
	
	
	function merge_user($login,$execute=1) {
		
		// se ele Ž o 16 (ex), paro por aqui
		if ($this->users[$this->maxusers]) {
			if ($this->users[$this->maxusers][login]==$login) {
				return false;
			}
		}

		$this->freecbqnum($login);
		$this->freemark($login);
		
		if ($execute==1) {
			$this->removeuser_route($login);
			$this->removeuser_fix($login);
		}
		
		if (!$this->createuser_forward($login))  
			return false;
		
		$this->processshaper();
		$this->createuser_route($login);
		
		if ($execute==1) {
			$this->filemark();
			$this->filecbqnum();
			$this->fileuser();
			$this->filelinktable();
			echo shell_exec("sh ".DIRTMP."/nx_forward.".$login);
			echo shell_exec("sh ".DIRTMP."/nx_route.".$login);
			
			// veio pelo fastauth ou pelo checkuser/checkguest
			// o apply vai vir com execute=0
			$this->datalog->insert("log_in",$login);
		}
	}
	
	function trackchanges () {
		// primeiro: verifico os planos q mudaram e marco os usuarios q estavam naquele plano
		// segundo: verifico os usuarios q tiveram o mac/ip/plano alterado
		
		// se um plano novo foi criado, quando verificar os usuarios, vou ver q o plano mudou
		
		// se um plano foi apagado nao tem problema
		//	se o plano foi substituido no mesmo ID, vai apresentar como diferenca no plano
		//  se for um id novo, vai apresentar como diferenca nos usuarios
		
		$oldplans 	= xml::normalizeseq($this->oldconf[plans][plan]);
		$plans		= $this->plans;
		
		$oldacls	= xml::normalizeseq($this->oldconf[acls][acl]);
		$acls		= $this->acls;
		
		$oldusers	= xml::normalizeseq($this->oldconf[users][user]);
		$users		= $this->users;
		
		//print_r($plans);
		//print_r($oldplans);
		
		$plan_change = array("acls","pppoe");
		$user_change = array("plan","ip","macs","int");
		
		// MUDANCAS EM PLANOS
		foreach ($oldplans as $oldplan) {
			$plan = $this->returnplan($oldplan[id]);
			foreach ($plan_change as $pc) 
				if ($plan[$pc] != $oldplan[$pc])
					$changed_plans[]=$plan[id];
		}
		// MUDANCAS EM ACLS
		foreach ($oldacls as $oldacl) { 
			$acl = $this->returnacl($oldacl[id]);
			foreach ($acl as $k=>$v) {
				if ($acl[$k] != $oldacl[$k])
					$changed_acls[]=$acl[id];
			}
		}
		// acllimit nao precisa pq quem bloqueia eh o task_account
		
		// MARCAR OS PLANOS Q USAVAM OS ACLS Q MUDARAM
		if (is_array($changed_acls)) {
			foreach ($plans as $plan) {
				$plan_acls = explode(",",$plan[acls]);
				foreach ($changed_acls as $acl) {
					if (in_array($acl,$plan_acls)) 
						$changed_plans[]=$plan[id];
				}
			}
		}
		// MUDANCAS EM USUARIOS
		foreach ($oldusers as $olduser) {
			foreach ($users as $user) {
				if ($user[login]==$olduser[login]) {
					$found=1;break;
				}
			}
			if ($found) {
				foreach ($user_change as $uc) {
					if ($user[$uc] != $olduser[$uc])
						$changed_users[]=$user[login];
				}
			} else {
				// usuario foi apagado
				$this->removed_users[]=$user[login];
			}
			unset($found);
		}

		
		// AGORA OS USUARIOS AFETADOS PELAS MUDANCAS DOS PLANOS
		if (is_array($changed_plans)) {
			foreach ($users as $user) {
				if (in_array($user[plan],$changed_plans))
					$changed_users[]=$user[login];
			}
		}
		
		// USUARIOS NOVOS
		foreach ($users as $user) {
			foreach ($oldusers as $olduser) {
				if ($olduser[login]==$user[login]) {
					$notnew=1;break;
				}
			}
			if (!$notnew)
				$changed_users[]=$user[login];
				
			unset($notnew);
		}

		$this->changed_users = $changed_users;
		
	}
		
	function openpppoe() {
		$connpppoefile = DIRTMP."nx_pppoe";
		
		if (!file_exists($connpppoefile))
			shell_exec("echo \"\" > $connpppoefile");

		$this->connpppoe = parse_ini_file($connpppoefile);
	}
	function filepppoe() {
		$connpppoefile = DIRTMP."nx_pppoe";
		write_ini_file($connpppoefile,$this->connpppoe); clearstatcache();
	}
	
	function openguests() {
		$connguestsfile = DIRTMP."nx_guests";
		$guestsfile = DIRDATA."/user/guest.totals";
		
		if (!file_exists($connguestsfile))
			shell_exec("echo \"\" > $connguestsfile");

		if (!file_exists($guestsfile))
			shell_exec("echo \"\" > $guestsfile");
			
		$this->connguests = parse_ini_file($connguestsfile);
		$this->gueststotals = parse_ini_file($guestsfile);
	}
	
	function fileguests() {
		$connguestsfile = DIRTMP."nx_guests";
		$guestsfile = DIRDATA."/user/guest.totals";
		
		write_ini_file($guestsfile,$this->gueststotals); clearstatcache();
		write_ini_file($connguestsfile,$this->connguests); clearstatcache();
	}
		
	
	function openmark () {
		if (!file_exists(DIRTMP."nx_usermarks")) 
			shell_exec("echo > ".DIRTMP."nx_usermarks");
			
		$nums = array();
		$this->usermarks = @parse_ini_file(DIRTMP."nx_usermarks");
		foreach ($this->usermarks as $user=>$marks) {
			$marks = explode(",",$marks);
			$this->usermarks[$user]=$marks;
			foreach ($marks as $num)
				$nums[]=$num;
		}
		sort($nums,SORT_NUMERIC);
		$this->marknums = $nums;
	}
	function filemark () {
		foreach ($this->usermarks as $user=>$marks)
			$usermarks[$user]=implode(",",$marks);
		write_ini_file(DIRTMP."nx_usermarks",$usermarks);
	}
	function newmark ($login) {
		$nums = $this->marknums;
		$i=1;
		foreach ($nums as $num) {
			if ($num!=$i)
				break;
			$i++;
		}
		$nums[]=$i;
		sort($nums,SORT_NUMERIC);
		$this->marknums = $nums;
		$this->usermarks[$login][]=$i;
		return $i;
		
	}
	function freemark ($login) {
		if (!$this->usermarks[$login]) { return; }
	
		$usermarks = $this->usermarks[$login];
		$this->marknums = array_diff($this->marknums,$usermarks);
		unset($this->usermarks[$login]);
	}
	function printmark() {
		echo "USER:";print_r($this->usermarks);
		echo "NUMS:";print_r($this->marknums);
	}
	
	
	
	function opencbqnum () {
		if (!file_exists(DIRTMP."nx_usercbqnums")) 
			shell_exec("echo > ".DIRTMP."nx_usercbqnums");
			
		$nums = array();
		$this->usercbqnums = @parse_ini_file(DIRTMP."nx_usercbqnums");
		foreach ($this->usercbqnums as $user=>$marks) {
			$marks = explode(",",$marks);
			$this->usercbqnums[$user]=$marks;
			foreach ($marks as $num)
				$nums[]=$num;
		}
		sort($nums,SORT_NUMERIC);
		$this->cbqnums = $nums;
	}
	function filecbqnum () {
		foreach ($this->usercbqnums as $user=>$marks)
			$usermarks[$user]=implode(",",$marks);
		write_ini_file(DIRTMP."nx_usercbqnums",$usermarks);
	}
	function newcbqnum ($login) {
		$nums = $this->cbqnums;
		$i=2; // comeca de 2
		foreach ($nums as $num) {
			if ($num!=$i)
				break;
			$i++;
		}
		$nums[]=$i;
		sort($nums,SORT_NUMERIC);
		$this->cbqnums = $nums;
		$this->usercbqnums[$login][]=$i;
		return $i;
		
	}
	function freecbqnum ($login) {
		if (!$this->usercbqnums[$login]) { return; }
		
		$usermarks = $this->usercbqnums[$login];
		$this->cbqnums = @array_diff($this->cbqnums,$usermarks);
		unset($this->usercbqnums[$login]);
	}
	function printcbqnum() {
		echo "USER:";print_r($this->usercbqnums);
		echo "NUMS:";print_r($this->cbqnums);
	}
	
	
	
	function openlinktable() {
		clearstatcache();
		$this->linktable = @parse_ini_file(DIRTMP."nx_linktable",1);
		if (!$this->linktable) { $this->linktable=array(); }
	}
	function filelinktable() {
		write_ini_file(DIRTMP."nx_linktable",$this->linktable);
		clearstatcache();
	}
	
	function opennxoff () {
		if (!file_exists(DIRTMP."nx_off.tmp")) {
			$this->nxoff=array();
		} else {
			$this->nxoff = explode(",",@file_get_contents(DIRTMP."nx_off.tmp"));
		}
	}
	
	function openlasts () {
		$this->mark = trim(file_get_contents(DIRTMP."/nx_aclmark"));
		$this->cbqnum_initial  = trim(file_get_contents(DIRTMP."/nx_cbq"));
	}
	
	function openforwardxml () {
		if (!$this->conf) { 
			$this->conf = xml::loadxml("forward.xml");
			$this->conf = $this->conf[forward];
		}
	}
	function openoldforwardxml () {
		if (!$this->oldconf) {
			if (!file_exists(DIRTMP."nx_oldforward.xml")) {
				$this->oldconf = $this->conf;
			} else {
				$this->oldconf = xml::loadxml(DIRTMP."nx_oldforward.xml");
				$this->oldconf = $this->oldconf[forward];
			}
		}
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
	
	
	
	// METO.DOS NAO-INSTANCIAVEIS
	/** validuser (login)
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
	/**
	 * guesttotal (key)
	 * retorna o total navegado do usuario ateh o momento
	 * nao precisa instanciar o objeto para funcionar
	 */
	function guesttotal($key) {
		
		$key = Forward::normalizeguestkey($key);
		
		if (method_exists($this,"fileguests")) {
			$this->fileguests();
		}
		
		$guestsfile = DIRDATA."/user/guest.totals";
		if (!file_exists($guestsfile)) {
			if (defined("ININTERFACE")) {
				$conn = new Conn();
				$conn->command(message::generate_function("NORMALIZE"));
			}
		}
		//clearstatcache();

		$guests = parse_ini_file($guestsfile);
		
		if (!$guests[strval($key)]) {
			// ainda nao foi usado
			return 0;
		}
		
		
		$totals = $guests[$key];
		
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
	
	// METO.DOS DE OBTENCAO DE DADOS
	function returnuser($login) {
		$users = $this->users;
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
		$guests = $this->guests;
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
	function normalizeguestkey ($key) {

		return strval(sprintf("%06s",strval($key)));
	}

	function returnacl($aclnum) {
		$acls = $this->acls;
		for ($i=0;$i<count($acls);$i++) {
			if ($acls[$i][id]==$aclnum)
				return $this->returnacltemplate($acls[$i]);
		}
		return -1;
	}
	
	function returnplanacls ($plannum) {
		$plans = $this->plans;
		foreach ($plans as $plan) {
			if ($plan[id]==$plannum) {
				return explode(",",$plan[acls]);
			}
		}
		return array();
	}
	function returnplan ($plannum) {
		$plans = $this->plans;
		foreach ($plans as $plan) {
			if ($plan[id]==$plannum) {
				return $plan;
			}
		}
		return array();
	}
	function returnplanlimit ($plannum) {
		$plans = $this->plans;
		$limits = $this->limits;
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
	
	/** retorna o service separado por proto/dport/sport
	 pode receber um `SERVICE` ou os 2 parametros separados
	 retorna um array com todos os proto/dport/sport
	*/
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

	function returnacltemplate($aclrule) {
		if (!$aclrule[rule]) { return $aclrule; }
		
		$ruleid = $aclrule[rule];
		
		$aclstemplate = $this->aclstemplate;
		//print_r($this->fwtpl[aclstemplate]);
		
		for ($i=0;$i<count($aclstemplate);$i++) {
			if ($aclstemplate[$i][id]==$ruleid) {
				$acltemplate=$aclstemplate[$i];
				break;
			}
		}
		//print_r($acltemplate);
		
		if (!$acltemplate) { return $aclrule; }
		
		$acls = xml::normalizeseq($acltemplate[acls][acl]);
		$files0= xml::normalizeseq($acltemplate[files][file]);
		
		foreach ($files0 as $file) {
			$files[$file[name]]=$file[content];
			$filename = conv::randkey(6).".txt";
			file_put_contents(DIRCONF."/proxy/".$filename,trim($file[content]));
			$files[$file[name]]=$filename;
		}
		
		$ret=array();
		foreach ($acls as $acl) {
			foreach ($acl as $rule_key=>$rule_value) {
				foreach ($files as $file_key=>$file_name) {
					$count=0;
					$acl[$rule_key] = str_replace("{".$file_key."}",$file_name,$rule_value,$count);
					if ($count>0) { break; }
				}
			}

			$ret[]=$acl+$aclrule; // array_merge

		}
		
		return $ret;
	}
	
	// TEM NO COMMON TB, MAS EH DIFERENTE
	function tplreplace($tpl,$array) {
		//echo "recebido: $tpl";print_r($array);
		$tpl = html_entity_decode($tpl);
		foreach ($array as $k => $v) {
			$v = $this->obj->get($v);
			$tpl = str_replace("{".$k."}",$v,$tpl);
		}
		//echo $tpl."\n";
		return $tpl;
	}

	// METO.DOS de GUEST
		/************
	 * checkguest_valid (key,ip)
	 * retorna false caso o guest tenha passado seu tempo ou expirado
	 * o ip passado eh apenas para poder logar o ip, nao eh requisito
	 * offset eh somado ao total antes de compara-lo com o limite
	 */
	function checkguest_valid ($key,$ip="",$offset=0) {
		$key = $this->normalizeguestkey($key);
		
		$guest = $this->returnguest($key);
		if (is_bool($guest) && $guest==false) 
			return false;
		
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
	function checkguest($key,$ip,$mac) {
		
		$key = $this->normalizeguestkey($key);
		
		$maxguests = $this->lic->checkout("maxguests");
		
		$addguestcmd = $this->fwtpl[guest][add][command];
				
		if (!is_bool($maxguests)&&(count($this->connguests)>=$maxguests)) {
			record::msg_log("Reached max numbers of connections","guest");
			record::dmesg_log("Maxconn Guests");
			return "MAXCONN";
		}
		
		if ($this->connguests[$key]) {
			record::msg_log("Guest Key {".$key."} Ip {".$ip."} already logged in, disconnecting first","guest");
			$this->disconnect_guest($key,$ip,0);
		}
		
		for ($i=0;$i<count($this->guests);$i++) {
			if (strtoupper(trim($this->guests[$i]["key"]))==strtoupper(trim($key))) {
				$this->connguests[$key]=$ip;

				if ($this->checkguest_valid($key,$ip)==false) {
					$this->datalog->insert("log_limit","guest.".$key);
					return "FAIL";
				}
				
				//$guesttimes = explode(",",$gueststotals[$key]);
				//$guesttimes[count($guesttimes)] = time()."-";
				//$gueststotals[$key] = implode(",",$guesttimes);
				if (!$this->gueststotals[$key])
					$this->gueststotals[$key]="0";
				
				$this->fileguests();
				
				$this->guests[$i][ip]=$ip;
				$this->guests[$i][macs]=$mac;
				
				$this->merge_user("guest.$key",1);
				
				record::msg_log("Guest Key {".$key."} Ip {".$ip."} connects","guest");				
				return "OK";
			}
		}

		return "FAIL";

	}
	
	function disconnect_guest($key,$ip="",$guestidle=0) {
		
		$key = $this->normalizeguestkey($key);
		
		$this->gueststotals[$key] = $this->gueststotals[$key] - $guestidle;
		if ($this->gueststotals[$key]<0) { $this->gueststotals[$key]=0; }
		
		// fechando a conexao
		unset($this->connguests[$key]);
		$this->fileguests();
		
		$this->disconnect_user("guest.".$key);

		record::msg_log("Guest Key {".$key."} Ip {".$ip."} Disconnected (Idle for {$guestidle} seconds)","guest");

	}
	
	function update_guest($key,$sum) {
		$key = $this->normalizeguestkey($key);
		$this->gueststotals[$key] = intval($this->gueststotals[$key]) + $sum;
		$this->fileguests();
	}
	
	function checkuser_limit ($login) {
		
		$users = $this->users;
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
		
		$tplguest = $this->tplreplace($tplguest,$var)."\n";
		
		for ($i=0;$i<count($useracls);$i++) 	
			$tplacl .= $this->processacl($useracls[$i],$var,$tpl3cmd,$tpl4cmd,0,0,$var[userchain]);
		
		$this->user_out[$var[userchain]] .= "# GUEST ".$guest[key]."\n";
		$this->user_out[$var[userchain]] .= $tplguest."\n".$tplacl;

	}
	
	
		
	// METO.DOS de USER
	
	function checkuser($user,$pass,$ip,$mac) {
		
		$noauthcmd = $this->fwtpl[user][noauth][command];
		$users = $this->users;
		
				
		for ($i=0;$i<count($users);$i++) {
			if (strtoupper($user)==strtoupper($users[$i][login])) {
				$plan = $this->returnplan($users[$i][plan]);
				if ($plan[pppoe]==1) {
					return "PPPOEONLY";
				}
				if (strtoupper($pass)==strtoupper($users[$i][pass])) {
					if (intval($users[$i][disabled])>0) {
						$dis = intval($users[$i][disabled]);
						$dis = $dis - 1;
						$this->datalog->insert("log_disabled",$user);
						return "DISABLED".intval($dis);
					}
					
				
					$int = $this->internal;
					
					$macs = explode(",",trim($users[$i][macs]));
						
					// eh novo (ou alguem mudando de maquina), preciso:
					// 1) pegar o mac, 
					// 2) pegar a placa de rede
					// 3) fixar ip/mac no xml
					// 4) merge(forward)
						
					if ($mac==FALSE||$int==FALSE)
						return "TRYAGAIN";

					// vamos ver se o IP MAC jah tah fixado! se tiver tah feito!
					$fast_ret = $this->fastauth($ip,$mac);
					$fast_ret2 = str_replace("\"","",$fast_ret);
					if (substr($fast_ret2,0,2)=="OK") {
						return $fast_ret;
					}
					//file_put_contents("/tmp/saida11",$fast_ret.":$ip,$mac");
					
					// atualizando os dados nos arrays q contem usuarios
					$oldmac = $users[$i][macs];
					$oldip 	= $users[$i][ip];
					
					
					
					if (intval($plan[fixmac])>=1) { // fixar MAC !
						if (trim($oldmac) != "") {		// se o MAC antigo nao Ž vazio
							if (trim(strtoupper($oldmac))!=trim(strtoupper($mac))) {	// e Ž diferente do atual
								$this->datalog->insert("log_changemac",$user);
								return "CHANGEMAC";		// bloqueia
							}
						}
						
						if (intval($plan[fixmac])==2) { // fixar IP tambem!
							if (trim($oldip) != "") {
								if (trim($oldip)!=trim($ip)) {
									$this->datalog->insert("log_changeip",$user);
									return "CHANGEIP";		// bloqueia
								}
							}
						}
					}
					
					
					$users[$i][macs]=$mac;
					$users[$i][ip]=$ip;
					// $users[$i][int]=$int; - tirei pq se nao tiver ele usa o interno lah
					$users[_num]=count($users);
					
					if (!$this->obj_conf) { 
						$this->obj_conf = new Conf("forward");
					}
					$this->obj_conf->conf[forward][users][user]=$users;
					$this->conf[users][user]=$users;
					$this->users = $users;
					
					// escrevendo configuracao
					$this->obj_conf->write();clearstatcache();
					
					unset($this->obj_conf);
					
					$msg = $users[$i][msg];
					
					// atualizando a lista do proxy		
					$this->iplist[$ip]=$users[$i][plan];
					
					
					// AQUI VAI APENAS CRIAR A REGRA DE FORWARD PRO USUARIO PODER NAVEGAR
					
					/*
					$this->removeuser_fix($user);
					$this->createuser_forward($user); // como nao vou dar fileuser(), o nxforward vai se perder, soh quero o nx_fix
					$this->createuser_route($user);
				
					shell_exec("sh ".DIROUT."forward/nx_fix.$user");
					shell_exec("sh ".DIRTMP."nx_route.$user");
					
					// copiando pro /etc/nexus/forward
					shell_exec("cp -a ".DIROUT."forward/nx_fix.$user /etc/nexus/forward/");
					*/
					
					/*
					 * AINDA NAO TAH REMOVENDO AS REGRAS DE FIX ANTIGAS E 
					 * NEM ROTEANDO (TESTAR!)!
					 */
					
					$this->removeuser_fix($user);

					if ($this->checkuser_limit($user)==false) {
						record::msg_log("User {$user} out limit","forward");
						$this->datalog->insert("log_limit",$user);
						return "OUTLIMIT";
					}

					$this->merge_user($user);
					shell_exec("touch ".DIRTMP."nx_dhcprestart");
					
					if ($plan[proxy]==1) 
						shell_exec("touch ".DIRTMP."nx_proxyrestart");

					// recarregando proxy
					Proxy::reload();
						
					record::msg_log("Fixing IP/MAC for user {$user}","forward");

					return "\"OK\",\"$msg\"";
				} else {
					return "FAIL";
				}
			}
		}
		return "USERFAIL";
		
	}
	
	function changepass ($user,$pass,$newpass) {
		$users = $this->users;
		for ($i=0;$i<count($users);$i++) {
			if (strtoupper($user)==strtoupper($users[$i][login])) {
				if (strtoupper($pass)==strtoupper($users[$i][pass])) {
					$users[$i][pass]=$newpass;
					$users[_num]=count($users);
					
					$this->obj_conf = new Conf("forward");
					$this->obj_conf->conf[forward][users][user]=$users;
					$this->conf[users][user]=$users;
					$this->users = $users;
					// escrevendo configuracao
					$this->obj_conf->write();clearstatcache();
					unset($this->obj_conf);
					
					$this->datalog->insert("log_changepass",$user);
					return "OK";
				} 
			} 
		}
		return "FAIL";
	}
	
	function fastauth($ip,$mac,$forced=0) {
		
		// primeiro verifico se ele nao eh um guest jah logado
		foreach ($this->connguests as $guestkey => $guestip) {
			if ($ip==$guestip)
				return "OK";
		}
		
		for ($i=0;$i<count($this->users);$i++) {
			if ($this->users[$i][ip]==$ip&&conv::startwith("ppp",$mac)) {
				$found=1;
				$user=$this->users[$i];
				if (intval($this->users[$i][disabled])>0) {
					$disabled=1;
					break;
				}
				$this->connpppoe[$this->users[$i][login]]="$ip,$mac";
				$this->filepppoe();

				break;
			}
			if ($this->users[$i][ip]==$ip&&in_array($mac,explode(",",trim($this->users[$i][macs])))) {
				$found=1;
				$user=$this->users[$i];
				if (intval($this->users[$i][disabled])>0) {
					$disabled=1;
				}
				break;
			}
		}
		
		if (!$found || $disabled) 
			return "FAIL";
		
		//if ($disabled==1) 
		//	return "\"FAIL\",\"forceauth\"";

		$plan = $this->returnplan($user[plan]);
		
		$login = $user[login];
		$msg = $user[msg];
		
		if ($plan[forceauth]==1 && $forced==0) 
			return "\"FAIL\",\"forceauth\"";
		
		if (!$forced) 
			$this->merge_user($login,1);
		else
			$this->createuser_route($login,"",1);
		
		record::msg_log("User {$login} fast login","forward");
			
		return "\"OK\",\"$msg\"";
		
	}
	
	function disconnect_pppoe($ip,$int) {
		foreach ($this->connpppoe as $user => $useripint) {
			list ($userip,$userint) = explode(",",$useripint);
			if ($userip==$ip) { 
				unset($this->connpppoe[$user]);$this->filepppoe();
				$this->disconnect_user($user,$userint);
				break; 
			}
		}		
	}

	function disconnect_user ($login,$int=0,$useridle=0) {

		$this->freecbqnum($login);
		$this->freemark($login);
		$this->filecbqnum();
		$this->filemark();
		
		$this->removeuser_route($login);
		$this->removeuser_fix($login);
		
		$this->filelinktable();
		
		// PRECISA TIRAR do /tmp/nx_arp.dat

		// pegando o IP
		$tmpuser = explode(".",$login);
		if ($tmpuser[0]=="guest") {
			$guestkey = $tmpuser[1];
			$guest = $this->returnguest($guestkey);
			foreach ($this->connguests as $key => $ip) 
				if ($guestkey==$key)
					break;
		} else {
			$user = $this->returnuser($login);
			$ip = $user[ip];
		}
		
		$arpdat = file(DIRTMP."nx_arp.dat");
		for ($i=0;$i<count($arpdat);$i++)  {
			list($tmpip,$tmphw,$tmpflags,$tmpmac) = sscanf($arpdat[$i],"%s%s%s%s%s%s%s%s");
			if ($tmpip==$ip)
				unset($arpdat[$i]);
		}
		file_put_contents(DIRTMP."nx_arp.dat",implode("\n",$arpdat));
		
		
		if ($int) {
			// caso for pppoe, preciso desconecta-lo.
			if (conv::startwith("ppp",$int)) 
				exec ("kill `cat /var/run/$int.pid`");
		}
		
		$this->datalog->insert("log_out",$login);
		
		record::msg_log("User {$login} Disconnected from {$int} (Idle for {$useridle} seconds)","forward");
		
		return 1;
	}
	
	
	/*******
	 * createuser_forward
	 * essa funcao cria e executa as regras de firewall do usuario (fixa mac/ip)
	 * bem como as regras de acl do usuario
	 * retorna 1 se OK, 
	 * -1 caso o usuario esteja desabilitado
	 * -2 caso o usuario nao exista
	 */
	function createuser_forward ($user) {
		
		$login=$user;
		
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
		
			$user		 = $guest;
			$user[login] = $login;
		
			$plan=$this->returnplan($plan);
		} else {
		
			$user = $this->returnuser($user);
			if (is_bool($user) && $user==false )
				return false;
	
			$plan = $this->returnplan($user[plan]);
		}

		
		if (intval($user[disabled])>0) {
			return false;
		}
		

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
		// e o que ï¿½ pra fazer, pra usar depois no cbq
		$var[usermark]  = $this->mark;
		$this->mark++;
		$var[userip]    = $user[ip];
	
		// O MAC EH SEPARADO POR VIRGULA
		//if ($user[macs]) 
		//	$usermacs   = explode(",",$user[macs]);
			
		$var[usermac] = $user[macs];
		$var[userlogin] = $user[login];
		
		$internal = $this->internal;
		
		//if (!$user["int"]) 
			$var[userint]=$internal;
		//else 
		//	$var[userint]	= $user["int"];
		
		// FIM POPULANDO O VAR ------------------------------------
		
		$this->processuser ($user,$var);


		if ($plan[pppoe]==1) {
			list ($userip,$userint) = explode(",",$this->connpppoe[$login]);
			$var[userint]=$userint;
			$user["int"]=$userint;
			$this->processfixuser($user,$var,1);
		} else if ($user[macs]&&$user[ip])
			$this->processfixuser($user,$var);


		$this->processuseracls($user,$var);
		
		//$this->user_out[$login] = $this->user_out[$login]
		
		return true;

	}
	
	function removeuser_fix ($user) {
		//file_put_contents("/tmp/saida9",DIRTMP.".nx_unfix.$user");
		if (file_exists(DIRTMP."/nx_unfix.$user"))
			file_put_contents("/tmp/saida8",shell_exec ("/bin/sh ".DIRTMP."/nx_unfix.$user"));	
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

		$this->user_out[$user[login]] .= $out;

	
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
		
		$this->usermac_out[$user[login]] .= $out;
		
		//file_put_contents(DIRTMP."/nx_fix.".$user[login],$out);
		file_put_contents(DIRTMP."/nx_unfix.".$user[login],$tplun1);
		
		$this->user_out[$user[login]] .= $out;

	}
	
	function processuseracls ($user,$var) {
		
		/////////////////////////////////////////////////
		// terceiro, quarto e quinto: comandos por acl
		$tpl3cmd = $this->fwtpl[user][preacl][command]."\n".$this->fwtpl[user][acl][command];
		$tpl4cmd = $this->fwtpl[user][acldrop][command];
	
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
		
			$user		 = $guest;
			$user[login] = $login;
		
			$plan=$this->returnplan($plan);
	
		} 

		$a=0;
		if ($user[acls]) {
			$acls = $user[acls];
			$user[acls]=explode(",",$user[acls]);
		} else if ($user[plan]) {
			$acls = $this->returnplanacls($user[plan]);
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

		$cmd = "# USER ACLS ".$user[login]."\n".$tplacl."\n";
		
		$this->user_out[$user[login]] .= $cmd;

	}
	
	
	/**
	 * se gateway for 1, ele escolhe um novo, senao tenta usar o antigo
	 */
	function createuser_route ($user,$gateway="",$execute=0) {

		$nxoff = $this->nxoff;

		if ($gateway!="1") {
			$gateway="";
			// vou usar o gateway q jah estava
			foreach ($this->linktable[gateway] as $int => $val) {
			if (!$this->linktable[$int]) { continue; }
				foreach ($this->linktable[$int] as $login => $timelogin) {
					if ($login==$user) {
						$gateway = $val;
						break;
					}
				}	
			}
		}
		
		$login=$user;
		
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
				
			$user		 = $guest;
			$user[login] = $login;
			
			// preciso de guestip e guestkey
			$guestip = $this->connguests[$guest[key]];
			$user[ip]=$guestip;
			
			$plan=$this->returnplan($plan);
		} else {
			$user = $this->returnuser($user);
			if (is_bool($user) && $user==false )
				return -1;
			$plan = $this->returnplan($user[plan]);
			
		}
		
		/*
		ob_start();
		print_r($plan);
		print_r($this->linktable);
		file_put_contents("/tmp/saida21",ob_get_contents());
		ob_end_clean(); */
		
		
		// se tem plan[link] e nao eh AUTO, entao o gateway jah estah setado.
		if ($plan[link] && $plan[link]!="auto") {
			if ($this->linktable[gateway][$plan[link]]) {
				$gateway = $this->linktable[gateway][$plan[link]];
				$bestchoice = $plan[link];
			}
			
	
			// mas se o link tah fora do ar,
			if (in_array($plan[link],$nxoff)) {
				if ($plan[linkfail]==1) {
					// se o link dele tah fora e ele nao pode ir pra outro, nao faco nada.
					return 0;
				}
				// eu vejo se posso joga-lo para outro link..
				// linkfail=0, USO OUTRO GATEWAY
				// =1, USO O MESMO GATEWAY Q SABEMOS Q TAH RUIM
				else if (!$plan[linkfail] || $plan[linkfail]==0) {
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
		if ($gateway==""||$gateway=="1") {
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
		
		if (trim($bestchoice)=="" || trim($gateway)=="") {
			// pego a placa e o gateway primario
			record::msg_log("Error choosing gateway, defaulting it","network");
			$bestchoice=Network::getprimary();
			$gateway = $this->obj->get("`HOST.GATEWAY.".$bestchoice."`");
		}
		
		record::msg_log("Link chosen to {$login} int {$bestchoice} via {$gateway}","network");
		
		$this->linktable[$bestchoice][$login]=time();
		
		
		
		// preciso de userip, usermac (?), gateway, userchain
		$var[userip]	= $user[ip];
		//$var[usermac]	= $user[macs]; // route nao precisa de MAC, alem do q o guest tb usa e nao armazenamos o mac dele
		$var[userchain]	= $user[login];

		
		$tplcmd 	= $this->fwtpl[user][route][command];
		$tpluncmd 	= $this->fwtpl[user][unroute][command];

		$tplproxycmd 	= $this->fwtpl[user][proxy][command];
		$tplunproxycmd 	= $this->fwtpl[user][unproxy][command];

		//}
		
		$var[intip]		= $this->obj->get("`HOST.INTERFACE.$bestchoice`");
		$var[gateway] 	= $gateway;
		$var[int] 		= $bestchoice;
		$var[intnum]	= substr($var[int],3);
		
		$tpl 	= $this->tplreplace($tplcmd,$var)."\n";
		$tplun 	= $this->tplreplace($tpluncmd,$var)."\n";
		
		// se usar proxy...
		if ($plan[proxy]==1) {
			$tpl	.= $this->tplreplace($tplproxycmd,$var)."\n";
			$tplun 	.= $this->tplreplace($tplunproxycmd,$var)."\n";
		}
				
		
		
		if ($execute==1) {
			// tento remover soh pra evitar duplicados
			$this->removeuser_route($login,1);
			$ret1 = shell_exec($tpl);
		}
		
		$this->filelinktable();
		
		file_put_contents(DIRTMP."/nx_unroute.$login",$tplun);
		file_put_contents(DIRTMP."/nx_route.$login",$tpl);
		//$this->user_out[$login] .= $tpl;
		

		
		return 1; //$tpl."\n";
		
	}
	
	function processpppoe () {
		foreach ($this->users as $user) {
			$acls = $this->returnplanacls($user[plan]);
			$plan = $this->returnplan($user[plan]);
			if (intval($plan[pppoe])==1)
				$this->addchap($user);
		}
	}
	
	
	/**
	 * Recebe um array de acls e retorna um array com todos os parametros dos
	 * acls para serem usados no tplreplace (o var) 
	 * antes de dar o tplreplace, precisa mergear com o var do usuario
	 */ 
	function processacl ($acl,$var,$tplacl,$tplacldrop,$int=0,$user=0,$guest=0) {

		if ($acl[0]) {
			// quando eh um ACL de RULE, ele tem varios acls dentro de 1, entao recursivamente eu passo por eles
			for ($i=0;$i<count($acl);$i++) {
				$cmd .= $this->processacl($acl[$i],$var,$tplacl,$tplacldrop,$int,$user,$guest)."\n";
			}
			return $cmd;
		}
		if ($acl[rule]) { return; } // Ž regra de proxy
		
		echo "PROCESSING ACL";print_r($acl);print_r($var);

		$tplproto=$this->returntpltype();
		$tpltime =$this->fwtpl[time][command];
		$tplroute=$this->fwtpl[route][command];


		$var[aclmark]=$this->newmark($user[login]);


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
				$plan = $this->returnplan($user[plan]);
				if ($plan[pppoe]==1) {
					$shapertemp[pppoe]=1;
					list ($userip,$userint) = explode(",",$this->connpppoe[$user[login]]);
					$shapertemp["int"]=$userint;
				}
			} else if ($guest!=0) {
				$shapertemp[user]=$guest;
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

		/*
		// forcando uma rota se tiver out no acl
		if (isset($acl[gw])) {
			// preciso pegar o gateway e a placa
			$tpl5 .= conv::tplreplace($tplroute,$var2)."\n";
		}
		*/

		//$var[aclmark] = $aclmark;

		return $tpl3."\n".$tpl4."\n";	
		
	}
	
	function removeuser_route ($user,$nolinktable=0) {
		if (file_exists(DIRTMP."nx_unroute.$user"))
			shell_exec ("sh ".DIRTMP."nx_unroute.$user");
			
		if ($nolinktable==0) {
			foreach ($this->linktable[gateway] as $int => $val) {
				if (!$this->linktable[$int]) { continue; }
					foreach ($this->linktable[$int] as $login => $timelogin)
						if ($login==$user) 
							unset($this->linktable[$int][$login]);
			}
		}
	}

	
	// METO.DOS de SHAPER
	function addcbq($array,$pppoe=0,$user=0) {
		
		//echo "ADDCBQ:";print_r($array);
		
		// recebo:
		// out,width,weight,prio,mark,ip,bounded
		// lista do q eh comando de Cbq:
		$vars=explode(";","DEVICE;RATE;WEIGHT;PRIO;MARK;RULE;BOUNDED;ISOLATED;PARENT;LEAF;#USERIP");
		
		if ($this->cbqnum) {
			$i = $this->cbqnum+1;
		} else if ($this->cbqnum_initial) {
			$i = $this->cbqnum_initial+1;
			unset($this->cbqnum_initial);
		} else 
			$i = count($this->cbq)+2;
		
		if ($i==0) { $i=2; }
		
		$this->cbq[$i][pppoe]=$pppoe;
		
		$var[seq]=$this->newcbqnum($user);
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

		if ($pppoe)
			$shaper_out = "# SHAPER IP ".$array["#USERIP"]." ($pppoe)\n";
			
		$shaper_out .= conv::cleanout($this->tplreplace($this->fwtpl[cbq][rule],$var))."\n";
		
		// ISSO NAO PODE EXISTIR
		if ($user=="0") 
			$this->shaper_out .= $shaper_out;
		else {
			if ($pppoe)
				$this->user_pppoe_out[$user] .= $shaper_out;
			else {
				//print_r($this->user_out);
				$this->user_out[$user] .= $shaper_out;
			}
				
		}
		
		
		$this->cbqnum=$i;
		
		return sprintf("%04d",$i);
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
			$varrule=array();
				

			$varrule=$varruledefaults;
			$varrule[mark]=		$shaperrules[$i][mark];
			
			// nao sei se vai precisar do rule! FALTA TESTAR (v) !NAOPRECISA!
			//$varrule[rule]=		$shaperrules[$i][ip]; 
			// se precisar vai ficar em upload e download
			
			if ($shaperrules[$i][user]) 
				$user=$shaperrules[$i][user];
			else 
				$user=0;
			

			if ($shaperrules[$i][prio]) // pode ter tambem prio pra download/upload no futuro
				$varrule[prio]=		$shaperrules[$i][prio];

			if ($shaperrules[$i][download]) {
				// para o download
				$varrule[rate]=	$shaperrules[$i][download];
				$varrule[weight]=ceil($varrule[rate]/10);					

				// se shaperrule nao tiver interface, vamos pegar todas as internas
				
				if ($shaperrules[$i][pppoe]==1) {
					// para cada regra de download, tambem preciso adiciona-la ao
					// /etc/nexus/shaper-pppoe
					// no caso do cliente conectar via pppoe
					$varrule[out]=$shaperrules[$i]["int"];
					if (trim($shaperrules[$i][ip])!="") {
						$varrule["#USERIP"]=$shaperrules[$i][ip];
						$this->addcbq($varrule,0,$user);
					}
				} else if ($shaperrules[$i]["int"]) {
					$varrule[out]=$shaperrules[$i]["int"];
					$this->addcbq($varrule,0,$user);
				} else {
					// para cada interface, eu adiciono o cbq
					$varrule[outs]=$this->internals;
					foreach ($varrule[outs] as $k=>$v) {
						$varrule[out]=$v[device];
						$this->addcbq($varrule,0,$user);
					}		
				}
				

				
			}
			if ($shaperrules[$i][upload]) {
				// para o upload
				// se nao tiver especificado o out, eu coloco pra todas as placas externas
				$varrule[rate]=	$shaperrules[$i][upload];
				$varrule[weight]=ceil($varrule[rate]/10);
				if ($shaperrules[$i][out]) {
					$varrule[out]=		$shaperrules[$i][out];
					$this->addcbq($varrule,0,$user);
				} else {
					$varrule[outs]=$this->externals;
					foreach ($varrule[outs] as $k=>$v) {
						$varrule[out]=$v[device];
						$this->addcbq($varrule,0,$user);
					}
				}
			}
		}
		
		$this->shaperpool=array();
		$this->shaperrules=array();
	}
	
	
	
	// METO.DOS de PPPOE
	function addchap ($user) {
		$this->chapsecrets .= $user[login]."\t*\t".$user[pass]."\t".$user[ip]."\n";
	}
	
	// METO.DOS de ARQUIVAMENTO

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
		
		file_put_contents(DIRTMP."/nx_firewall.sh",conv::cleanout($this->out));

	}
	function filechap() {
		
		file_put_contents(DIROUT."/forward/chap-secrets",$this->chapsecrets);
	}
	function filelasts() {
		file_put_contents(DIRTMP."/nx_aclmark",$this->mark);
		file_put_contents(DIRTMP."/nx_cbq",$this->cbqnum);
	}
	function fileshaper() {
		file_put_contents(DIRTMP."/nx_shaper.sh",conv::cleanout($this->shaper_out));
		
	}
	
	function fileuser() {
		foreach ($this->user_out as $login => $out) 
			file_put_contents(DIRTMP."/nx_forward.$login",conv::cleanout($out));
			
		foreach ($this->user_pppoe_out as $login => $out) 
			file_put_contents(DIRTMP."/nx_forward_pppoe.$login",$out);
	
	}
	
	// OUTROS PROCESSADORES
	
	function processrule () { 
		$rules = $this->rules;
		
		$tplacl = $this->fwtpl[rule][acl][command];
		$tplacldrop = $this->fwtpl[rule][acldrop][command];
		$var = array();
		
		//function processacl ($acl,$var,$tplacl,$tplacldrop,$int=0) {
		
		for ($i=0;$i<count($rules);$i++) {
			$rule=$rules[$i];
			$acl=$this->returnacl($rule[acl]);
			$cmd .= $this->processacl($acl,$var,$tplacl,$tplacldrop,0,"nxrules");
		}
		$this->out .= "\n# RULES\n".$cmd;
	}
	
	function processinterface() {
		$tplint = $this->fwtpl["interface"][command];
		$networks = $this->internal_networks;
		//print_r($networks);
		
		$var=array();
		foreach ($networks as $k => $v) {
			$var["interface"]=$k;
			$var[network]=$v;
			$var[out]=$k;
			$this->out .= "\n# INTERNAL INTERFACE: $k\n".$this->tplreplace($tplint,$var)."\n";
			$shaper_out .= $this->tplreplace($this->fwtpl[cbq]["interface"],$var)."\n";
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
			$shaper_out .= $this->tplreplace($this->fwtpl[cbq]["interface"],$var)."\n";
		}
		
		// concatena no inicio
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
		$traffic = $this->traffics;
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
			$var = array();

			$intfw=$ints[$i][firewall];
			$var["interface"]=xml::getxmlval("device",$ints[$i]);
			
			$out .= "\n# FIREWALL ".$var["interface"]."\n";
			
			foreach ($intfw as $sname=>$sfw) {
				$var[action]=xml::getxmlval("action",$sfw);
				$out .= $this->tplreplace($tplserv[$sname],$var)."\n";
			}
		}
		$this->out .= $out;
	}
	
	
	function processdsluser () {
		$ints=$this->interfaces;
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
		$ints = $this->interfaces;
		$pubs = $this->publishs;
		
		$tpl  = $this->fwtpl[publish]["interface"][command];

		$eints = $this->externals;

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
				
				if ($pub[proto]=="tcpudp") { // se for as 2, faco 2 vezes
					$var[proto]="tcp";
					$out .= $this->tplreplace($tpl,$var)."\n\n";
					$var[proto]="udp";
					$out .= $this->tplreplace($tpl,$var)."\n\n";
				} else {
					$out .= $this->tplreplace($tpl,$var)."\n\n";
				}
				unset($var);
			}
		}
		
		if ($out) {
			$this->out .= "# PUBLISHED SERVERS\n\n";
			$this->out .= $out;
		}
	}
	
		
	function processproxylist () {
		
		if (constant("PROXY")!="SQUID") { return; }
		
		foreach ($this->ipplan as $ip => $plan) {
			$list[$plan][]=$ip;
		}
		
		foreach ($this->plans as $plan) {	
			$listtxt = implode("\n",$list[$plan[id]]);
			
			$iplist_file = DIRTMP."nx_proxy_".$plan[id]."_iplist";
			file_put_contents($iplist_file,$listtxt);
		}
		
		Proxy::reload();
		
	}

	
}
?>
