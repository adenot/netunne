<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Aug 30, 2006					*
	*																*
	****************************************************************/

	$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
	define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);
	
	include_once NEXUS."/core/nlib/common.nx";

	/* ROTEIRO
	 * 1) primeiro pego os hosts e resolvo pingando
	 * 		pra cada um eu salvo um array do tipo:
	 * 
	 * 		array[interface][0][host]=www.google.com
	 * 		array[interface][0][ip]=10.0.0.1
	 * 		array[interface][0][return]=0
	 * 
	 * 	ou:  (nao conseguiu pingar)
	 * 		array[interface][0][host]=www.google.com
	 * 		array[interface][0][ip]=10.0.0.1
	 * 		array[interface][0][return]=1
	 * 
	 * 	ou:  (nao conseguiu resolver)
	 * 		array[interface][0][host]=www.google.com
	 * 		array[interface][0][ip]=
	 * 		array[interface][0][return]=2
	 * 
	 * 2) estatistica: se tiver mais de 30% na interface com return 2, 
	 * 		apenas aviso q o dns tah ruim
	 * 	  se tiver mais de 30% na interface com return 1,
	 * 		marco esse link pra desativar
	 * 
	 */

class Conncheck {

	var $iplist;
	var $config;
	var $lastint;

	var $result;
	
	var $fw;
	var $linktable;
	
	var $nodns; // se tiver nodns, nem tenta resolver nas proximas
	
	var $disable_conncheck;

	function __construct() {
		$this->fw = new Forward();
		$this->fw->openfwtpl();
		$this->linktable = @parse_ini_file(DIRTMP."nx_linktable",1);
		$this->openlist();
		
		$conf = new Conf("network");
		$this->disable_conncheck = $conf->get("network/disable_conncheck");
	
	}

	function pinghost ($host,$ip,$gateway,$int) {
		$try = $this->config["try"];
		if (trim($try)=="") {
			$try=2;
		}
		
		//var_dump($this->fw);
		
		$tpladd = $this->fw->fwtpl[routeping][add][command];
		$tplrem = $this->fw->fwtpl[routeping][remove][command];
		//echo $tpladd;
		
		$var[ip]=$host;
		$var[gw]=$gateway;
		$var[int]=$int;
		$cmdadd = conv::tplreplace($tpladd,$var);
		$cmdrem = conv::tplreplace($tplrem,$var);
		
		if (($this) && ($this->nodns!=1)) {
			echo $cmdadd.shell_exec($cmdadd);
			exec("ping -c $try $host",$ret,$retcode);
			//print_r($ret);
			//sleep(10);
			echo $cmdrem.shell_exec($cmdrem);
			echo "$host ret: $ret retcode: $retcode ";
		} else {
			// se jah tiver nodns, finge q tentou e nao conseguiu resolver..
			$retcode=2;
		}
		
		if ($retcode==0||$retcode==1) {
			$ret = explode("\n",$ret[0]);
			$tmp = sscanf($ret[0],"%s%s%s%s");
			$ip=str_replace(array("(",")",":"),"",$tmp[2]);
			echo "ip $ip\n";
		} else if ($retcode==2) {
			if ($this)
				$this->nodns=1;
			echo "DNS error, ip pinging...\n";
			$var[ip]=$ip;
			$cmdadd = conv::tplreplace($tpladd,$var);
			echo $cmdadd.shell_exec($cmdadd);
			exec ("ping -c $try $ip",$ret,$retcode);
			//print_r($ret);
			$nodns = 1;
			echo $cmdrem.shell_exec($cmdrem);
		} else {
			return false;
		}
		
		// chegando aqui o retcode soh pode ser 0 ou 1
		
		/* 
		// vamos verificar por quem saiu...
		// #ip route get 65.110.59.1
		// 65.110.59.1 via 192.168.200.1 dev eth2  src 192.168.200.195 
		
		exec ("ip route get $ip",$ret2);
		$ret2 = explode("\n",$ret2[0]);
		$tmp = sscanf($ret2[0],"%s%s%s%s%s%s");
		//print_r($tmp);
		
		// poderia pegar o gateway tambem, mas vou pegar soh a interface,
		// pq presumo q existe 1 gateway por interface e pra mim tanto faz se 
		// cair o gateway ou a interface q vou desativar tudo
		$int = $tmp[4];
		$this->lastint=$int;
		
		*/

		$i = count($this->result[$int]);
		
		$this->result[$int][$i][host]=$host;
		$this->result[$int][$i][ip]=$ip;
		$this->result[$int][$i][nodns]=$nodns;
		$this->result[$int][$i][result]=$retcode;
		
		$this->iplist[$host]=$ip;
		
		/* retornos (retcode)
		 * 0 = OK
		 * 1 = FALHA
		 * 2 = FALHA DNS
		 */
		
		if ($retcode==1) {
			return false;
		} 
		return true;
		
	}
	
	function openlist () {
		$inifile = parse_ini_file(DIRSET."conncheck.ini",true);
		
		$this->iplist = $inifile["list"];
		$this->config = $inifile["config"];	
	}
	
	function updatelist () {
		$newlist["config"] = $this->config;
		$newlist["list"] = $this->iplist;
		clearstatcache();
		write_ini_file(DIRSET."conncheck.ini",$newlist);
		
	}
	function checkinterface() {
		$intoff=array();
		$dnsoff=array();
		foreach ($this->result as $int=>$v) {
			
			$total[$int] = count($this->result[$int]);
			$err[$int]=0;
			$dnserr[$int]=0;
			foreach ($v as $res) {
				if ($res[result]==1) 
					$err[$int]++;
				if ($res[nodns]==1) 
					$dnserr[$int]++;
			}
			//echo "Errors in $int: ".$err[$int]." total: ".$total[$int]."\n";
			
			if ((($err[$int] / $total[$int])*100) > 50) {
				// interface down
				$intoff[]=$int;
				//echo "Offline: $int\n";
			}
			if ((($dnserr[$int] / $total[$int])*100) > 50) {
				// dns da interface down
				$dnsoff[]=$int;
			}
		}
		$ret[intoff]=$intoff;
		$ret[dnsoff]=$dnsoff;
		return $ret;
	}
	
	function check() {
		
		
		$gateways = $this->linktable[gateway];
		
		foreach ($gateways as $int => $gateway) {
			foreach ($this->iplist as $host=>$ip) {
				echo "Pinging $host ($ip) via $gateway ($int)\n";
				$this->pinghost($host,$ip,$gateway,$int);
			}
		}
			
		$this->updatelist();
		

		$check = $this->checkinterface();
		$intoff = $check[intoff];
		$dnsoff = $check[dnsoff];
		
		//echo "off:";print_r($check);
		//echo "dnsoff";print_r($dnsoff);
		
		//print_r($this->result);
		
		$txt_off = 		@implode(",",$intoff);
		$txt_dnsoff = 	@implode(",",$dnsoff);
		
		//echo "TXT_OFF: $txt_off\n";
		
		// preciso saber se um link saiu do pool, rebalancear
		// se um link entrou, basta atualizar no nx_linktable (chamando o task_network)
		
		$nxoff_old=array();
		if (file_exists(DIRTMP."nx_off.tmp"))
			$nxoff_old = explode(",",file_get_contents(DIRTMP."nx_off.tmp"));
		
		$obj = new Object();
		$externals = $obj->getinterfaces("external");
		
		if (count($externals)>1 && $this->disable_conncheck != 1) {
			system("echo -n \"$txt_off\" > ".DIRTMP."nx_off.tmp");
			system("echo -n \"$txt_dnsoff\" > ".DIRTMP."nx_dnsoff.tmp");
		}
		
		sort($intoff);
		sort($nxoff_old);
		
		$str_off = implode(",",$intoff);
		$str_oldoff = implode(",",$nxoff_old);
		
		if ($str_off != $str_oldoff)
			$modified=1;
		
		//if (trim($txt_off)!=trim(@implode(",",$nxoff_old)))
		//	$modified=1;
		
		$time=time();
		

		if ($modified && is_array($intoff)) {
			foreach ($intoff as $int) {
				if (trim($int)=="") { continue; }
				
				system("echo \"$time $int\" >> ".DIRLOG."conncheck.log");
				record::dmesg_log("$int: Conncheck failed");
				record::msg_log("Conncheck failed on $int","network");
				//system("echo \"$int: Conncheck failed\" >> /var/log/dmesg-tmp.log");
			}
		}

		// se tiver desabilitado, avisa mas nao faz nada.
		if ($modified && $this->disable_conncheck != 1) { 
			record::msg_log("Reconfiguring interfaces","network");
			exec ("sh ".NEXUS."/core/bin/scripts/exec.sh sh /etc/init.d/networking restart");
			exec ("sh ".NEXUS."/core/bin/scripts/exec.sh /usr/bin/php5.0 ".NEXUS."/core/nlib/task_network.nx");
			//exec ("sh ".NEXUS."/core/bin/scripts/exec.sh sh ".NEXUS."/core/bin/nexus.sh \"merge(network)\"");
		}
	}
}

record::msg_log("Running task_conncheck...","account");

$a = new Conncheck();
$a->check();

record::msg_log("task_conncheck finish","account");

?>
