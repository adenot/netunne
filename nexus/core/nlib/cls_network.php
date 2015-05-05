<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 30/03/2006					*
	*																*
	****************************************************************/

class Network {
	var $conf;
	var $networktpl;
	var $interfacesout;
	var $dnsout;
	var $dslout;
	var $waitroutesout;
	var $waitinterfaces;
	var $disableout;
	
	var $dhcp;
	var $obj;
	
	var $pppoeopts;
	var $pppoeserver;
	
	var $ddclientout;
	
	var $maxusers; // vem da licencas
	
	var $int_primary;
	
	/**
	 * enableinterface (int)
	 * tira ela da lista de interfaces desabilitadas e chama o task_network
	 * nao precisa instanciar
	 */
	function enableinterface ($int) {
		$off = trim(@file_get_contents(DIRTMP."/nx_off.tmp"));
		$off = explode(",",$off);
		for ($i=0;$i<count($off);$i++)
			if ($off[$i]==$int)
				unset($off[$i]);
		
		if (count($off)>=1) 
			@file_put_contents(DIRTMP."/nx_off.tmp",implode(",",$off));
		else
			shell_exec("rm -fr ".DIRTMP."/nx_off.tmp");
			
		exec ("sh ".NEXUS."/core/bin/scripts/exec.sh sh /etc/init.d/networking restart");
		//exec ("sh ".NEXUS."/core/bin/scripts/exec.sh /usr/bin/php5.0 ".NEXUS."/core/nlib/task_network.nx");
		
	}
	
	/*** getprimary ()
	 * retorna a placa primaria, seja ela a marcada como primaria ou a de maior peso
	 * nao precisa instanciar
	 */
	function getprimary() {
		if ($this)
			$obj = $this->obj;	
		else 
			$obj = new Object();
		
		
		$int_externals = $obj->getinterfaces("EXTERNAL");
		if (!$int_externals) {
			return "";
		}
		
		$int_backup_maxweight=0;
		//$linktable = @parse_ini_file(DIRTMP."nx_linktable",1);
		clearstatcache();
		$nxoff = explode(",",@file_get_contents(DIRTMP."nx_off.tmp"));
		
		foreach ($int_externals as $inte) {
			$inte[device]=xml::getxmlval("device",$inte);
			
			if ($inte[primary]==1) {
				$int_primary = $inte[device];
			}
			if (intval($inte[weight])==0) { $inte[weight]=1; }
			/*
			if ($inte[weight]>$int_backup_maxweight) {
				$int_backup[$inte[weight]]=$inte[device];
				$int_backup_maxweight = $inte[weight];
				// vai ficar no formato: int_backup[peso]=nome_da_interface
			}
			*/
			
			$int_backup[$inte[device]]=$inte[weight];
			// vai ficar no formato: int_backup[interface]=peso
			
		}
		
		if (in_array($int_primary,$nxoff))
			unset($int_primary);
		
		arsort($int_backup); // peso maior vem primeiro
		
		//echo "pri:$int_primary";print_r($int_backup);
		
		// se nao teve nenhum primary, vou pegar o q tiver maior peso
		if (!$int_primary) {
			foreach ($int_backup as $backup_int => $backup_weight) {
				if (in_array($backup_int,$nxoff))
					continue;
				$int_primary=$backup_int;
			}
		}
		//echo "pri2:$int_primary";
		// se nao tem nenhuma int_backup ou entao todas estao nxoff, entao a primaria vai permanecer o q ele marcou
		
		// se mesmo assim nao tiver nenhuma, pego a primeira external soh pra constar..
		if (!$int_primary)
			$int_primary = $int_externals[0][device];
		
		return $int_primary;
		
	}
	
	function linktable_remove_user($linktable="",$user) {
		if (is_string($linktable) && $linktable=="") {
			$linktable = @parse_ini_file(DIRTMP."nx_linktable",1);
			$read=1;
		}
		
		foreach ($linktable[gateway] as $int => $gw) {
			if ($linktable[$int][$user]) {
				unset($linktable[$int][$user]);
			}
		}
		
		if ($read==1) {
			write_ini_file(DIRTMP."nx_linktable",$linktable);
		} else {
			return $linktable;
		}
	}
	
	
	function opennetworkxml() {
		$this->conf = xml::loadxml("network.xml");
		$this->conf = $this->conf[network];	
	}
	function opennetworktpl () {
		$this->networktpl = xml::loadxml(DIRTPL."/network.xml.tpl");
	}
	function processinterfaces() {
		$ints=xml::normalizeseq($this->conf[interfaces]["interface"]);
		
		$dh=0;
		$unit=1;

		for ($i=0;$i<count($ints);$i++) {

			if (!$var[device]=xml::getxmlval("device",$ints[$i]))
				continue;
			
			$ip = $this->obj->get("`HOST.INTERFACE.".$var[device]."`");
			//print_r($ip);
			if (trim($ip)!="") { 
				$var[hasip][0][ip]=$ip;
			}			
			$this->disableout .= conv::tplreplace($this->networktpl[disable],$var)."\n";
			
			if ($ints[$i][disabled]==1)
				continue;

			$assign=trim(strtolower($ints[$i][assignment]));
			$type=trim(strtolower($ints[$i][type]));
			$inttpl=$this->networktpl[$assign][$type];
			if ($assign=="static") {
				$var[address]=$ints[$i][address];
				$var[netmask]=$ints[$i][netmask];
				if ($type=="external") {
					//if ($ints[$i][primary]==1) {
					$var[hasgateway][0][gateway]=$ints[$i][gateway];
					//}
				}
				$net = Net_ipv4::parseAddress($var[address]."/".$var[netmask]);
				$var[network]=$net->network;
				$var[broadcast]=$net->broadcast;

			} else if ($assign=="dsl"&&$type=="external") {
				$var[dsluser]=$ints[$i][dsluser];
				$var[unit]=$unit;
				$this->waitinterfaces.=" ppp$unit";
				$unit++;
				$this->dslout["dsl-provider-".$var[device]]=conv::tplreplace($this->networktpl[dslprovider],$var);
			}
			
			if (($this->dhcp->conf[config][autonetwork]=="yes"||
				$this->dhcp->conf[config][autonetwork]=="1")&&
				($type=="internal"))
			{
				
				if (xml::getxmlval("action",$ints[$i][firewall][dhcp])=="allow") {
				
					if ($ints[$i][firstdhcp]) {
						$from = $ints[$i][firstdhcp];
					} else {
						$from = conv::ipsum($ints[$i][address],2);
					}
					$fromlong = ip2long($from);
					
					if (intval($this->maxusers)!=0) 
						$maxips = $this->maxusers;
					else
						$maxips = 15; // se nao tem licenca, soh libero 15
			
					$to = conv::ipsum($from,$maxips);
					
					$long_to = ip2long($to);
					$broad_to = ip2long($net->broadcast);
					
					if ($long_to > $broad_to) {
						$to = conv::ipsum($net->broadcast,-1);
					}
					
					$dhcpnetworks[$dh]["interface"]=$var[device];
					$dhcpnetworks[$dh]["range"][from]=$from;
					$dhcpnetworks[$dh]["range"][to]=$to;
					$dh++;
				} else {
					// soh pra ele refazer as redes - FALTA
					// provavlemente nao vai funcionar qdo tiver com autonetwork=no
					$dhcpnetworks=array();
				}
				//$dhcpnetworks[$i][dnss][dns]="`HOST.INTERFACE.".$var[device]."`";
			}
			
			
			$ifaces .= conv::tplreplace($inttpl,$var)."\n"; 
			unset($var);
			
		}
		$var[iface]=$ifaces;
		$tplglobal=$this->networktpl["global"];
		$out = conv::tplreplace($tplglobal,$var);
		//print_r($var);
		$this->interfacesout=$out;
		
		if (is_array($dhcpnetworks)) {
			$this->dhcp->conf[networks][network]=$dhcpnetworks;
			$this->dhcp->conf[networks][network][_num]=count($dhcpnetworks);
		}
		
	}
	function processresolvconf() {
		$dnsconf = $this->conf[dns];
		$dnstpl = $this->networktpl[resolvconf];
		//$var[nameserver]=$dnsconf[nameserver];
		if ($dnsconf[search])
			$var[hassearch][0][search]=$dnsconf[search];
		
		/*
		$ints=xml::normalizeseq($this->conf[interfaces]["interface"]);
		foreach ($ints as $int) {
			$int[device]=xml::getxmlval("device",$int);
			if($int[device]==$this->int_primary) {
				$dns=$int[dns];
				break;
			}
			if ($int[type]=="external" && $int[dns]) {
				$dns=$int[dns];
				// senao pegou o dns da primaria, pega o ultimo dns da ultima placa
			}
		}
		// se mesmo assim nao tiver dns, pega oq tah no nameserver antigo...
		if (!$dns)
			$dns=$dnsconf[nameserver];
		
		*/
		
		$out = conv::tplreplace($dnstpl,$var);
		$this->dnsout=$out;
		
	}
	function processpppoe() {
		$tplpserver = $this->networktpl[pppoeserver];
		$tplpppoe = $this->networktpl[pppoe];
		
		$ints=xml::normalizeseq($this->conf[interfaces]["interface"]);

		$in=0;
		$dn=0;
		for ($i=0;$i<count($ints);$i++) {
			if (!$device=xml::getxmlval("device",$ints[$i]))
				continue;
			$type=trim(strtolower($ints[$i][type]));
			$pppoe=trim(strtolower($ints[$i][pppoe]));
			  
			if ($type=="internal"&&$pppoe==1) {
				$var2[interfaces][$in]["interface"] = $device;
				$var2[interfaces][$in]["cname"] = $this->conf[pppoe][cname];
				$var2[interfaces][$in]["localip"] = $ints[$i][address];
				
				if (!$this->conf[pppoe][servicename]) 
					$var2[interfaces][$in]["servicename"] = "";
				
				if (trim($this->conf[pppoe][servicename])!="") 
					$var2[interfaces][$in]["servicename"] = "-S ".$this->conf[pppoe][servicename];
				$in++;
				
				$var1[dnss][$dn][dns]=$ints[$i][address];
				$dn++;
			}
		}
		$this->pppoeopts   = conv::tplreplace($tplpppoe,$var1);
		$this->pppoeserver = conv::tplreplace($tplpserver,$var2);
		
	}
	
	function processwaitroutes () {
		$tpl = $this->networktpl[waitroutes];
		$var[ifaces]=trim($this->waitinterfaces);
		$this->waitroutesout = conv::tplreplace($tpl,$var);
	}
	function processddclient () {
		
		if ($this->conf[ddclient][login] &&
			$this->conf[ddclient][password] &&
			$this->conf[ddclient][domains]) {
				
			$tplddclient = $this->networktpl[ddclient];
			
			$var[login]=$this->conf[ddclient][login];
			$var[password]=$this->conf[ddclient][password];
			$domains=explode(",",$this->conf[ddclient][domains]);
			foreach ($domains as $domain) 
				$var[domains][]=trim($domain);
			
			$var[domains]=implode("\n",$var[domains]);
			$this->ddclientout = conv::tplreplace($tplddclient,$var);
		}
			
	}
		
	function addlinktable($interface,$weight,$gateway) {
		if (file_exists(DIRTMP."nx_linktable")) 
			$linktable = parse_ini_file(DIRTMP."nx_linktable",1);
		else
			$linktable = array();
			
		if (intval($weight)==0)
			$weight=1;
			
		$linktable[weight][$interface]=$weight;
		$linktable[gateway][$interface]=$gateway;
		$linktable[$interface]=array();
		
		write_ini_file(DIRTMP."nx_linktable",$linktable);
		clearstatcache();
	}
	function cleanlinktable() {
		$linktable[weight]=array();
		write_ini_file(DIRTMP."nx_linktable",$linktable);
		clearstatcache();
	}
	
	function fileinterfaces() {
		file_put_contents(DIROUT."/network/interfaces",$this->interfacesout);
		
		$internal = $this->obj->get("`INTERFACE.INTERNAL`");
		$internal=$internal[device];
		file_put_contents(DIRTMP."nx_internal",$internal);
	}
	function fileresolvconf() {
		file_put_contents(DIROUT."/network/resolv.conf.nx",$this->dnsout);
	}
	function filepppoe() {
		file_put_contents(DIROUT."/network/pserver",$this->pppoeserver);
		file_put_contents(DIROUT."/network/pppoe-server-options",$this->pppoeopts);
	}
	
	function filedsl() {
		if (!is_array($this->dslout)) { return; }
		foreach ($this->dslout as $file=>$content) {
			file_put_contents(DIROUT."/network/$file",$content);
		}
	}
	function filewaitroutes () {
		file_put_contents(DIROUT."/network/waitroutes.sh",$this->waitroutesout);
	}
	function filedisable () {
		file_put_contents(DIROUT."/network/disable.sh",$this->disableout);
	}
	function fileddclient () {
		exec ("rm -fr ".DIROUT."/network/ddclient.conf");
		if (trim($this->ddclientout)!="") {
			file_put_contents(DIROUT."/network/ddclient.conf",$this->ddclientout);
		}
	}
	
	
	/*
	function dependency () {
		return array("DHCP");
	}
	*/

	function merge() {
		@include_once "common.nx";
		
		$lic = new Checklicense();
		$this->maxusers = $lic->checkout("maxusers");
		
		$this->dhcp = new Dhcp();
		$this->dhcp->opendhcpxml();
		$this->obj = new Object();
		
		$this->int_primary = $this->getprimary();

		$this->opennetworkxml();
		$this->opennetworktpl();
		
		$this->processinterfaces();
		$this->processresolvconf();		
		$this->processpppoe();
		//$this->processwaitroutes();
		$this->processddclient();

		
		$this->fileinterfaces();
		$this->fileresolvconf();
		$this->filepppoe();
		$this->filedsl();
		//$this->filewaitroutes();
		$this->fileddclient();
		$this->filedisable();

		
		$this->dhcp->writedhcpxml();
			
		
	}
	
}


?>
