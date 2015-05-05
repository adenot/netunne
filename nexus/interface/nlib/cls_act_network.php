<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 24/07/2006					*
	*																*
	****************************************************************/

class act_networksetup {
	
	var $actident; 
	var $conf;

	function __construct($actident) {
		$this->conf = new Conf("network");
		$this->conf->ident = $actident;
		$this->actident = $actident;
		file_put_contents(DIRTMP."nx_networkchanged","1");
	}
	
	// nao deveria deixar editar a placa em uso
	
	function processdns ($input) {
		$this->conf->conf[network][dns][nameserver]=$input[dns];
		
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		record::act_log(_("Network DNS changed"));
		return true;	
	}
	function processprimary ($input) {
		$cards = xml::normalizeseq($this->conf->conf[network][interfaces]["interface"]);

		for ($i=0;$i<count($cards);$i++) {
			if (xml::getxmlval("device",$cards[$i])==$input[primary]) {
				$cards[$i][primary]=1;
			} else {
				unset($cards[$i][primary]);
			}
		}
		$cards["_num"]=count($cards);
		$this->conf->conf[network][interfaces]["interface"]=$cards;
		
		
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		record::act_log(sprintf(_("Primary Interface changed to %s"),$input[primary]));
		return true;	
	}
	
	function processddclient ($input) {
		if (count($input)==0) {
			$input[login]="";
			$input[password]="";
			$input[domains]="";
		} else if (trim($input[login])=="" ||
				trim($input[password])=="" ||
				trim($input[domains])=="") {
			//print_r($input);
			return "REQUIRED";
				
		}
				
		
		$this->conf->conf[network][ddclient][login]=$input[login];
		$this->conf->conf[network][ddclient][password]=$input[password];
		$this->conf->conf[network][ddclient][domains]=$input[domains];
			
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		record::act_log(_("DDNS config changed"));
		return true;	
	}

	function processconncheck ($input) {
		
		$this->conf->conf[network][disable_conncheck]=$input[conncheck];
			
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}

		return true;	
	
	}

	
	function process ($input) {

		$cards = xml::normalizeseq($this->conf->conf[network][interfaces]["interface"]);

		for ($i=0;$i<count($cards);$i++) {
			if (xml::getxmlval("device",$cards[$i])==$input[device]) {
				$intnum = $i;
				$card = $cards[$i];
				break;
			}
		}
		if (!isset($intnum))
			$intnum = count($cards);
		
		if (!$card) {
			$card[device]=$input[device];
		}
		
		if ($input[type]=="internal" && $input[fwdhcp]=="allow" && trim($input[firstdhcp])!="") {
			if (!Net_ipv4::validateIP($input[firstdhcp])) {
				return "INVALIDFIRSTDHCP";
			}
		}
		if ($input[assignment]=="static") {
			if (!Net_ipv4::validateIP($input[address])) {
				return "INVALIDADDRESS";
			}
		}
		if ($input[type]=="internal") {
			// preciso ver se soh tem uma internal
			for ($i=0;$i<count($cards);$i++) {
				if ($cards[$i][type]=="internal" && $i != $intnum)
					return "ONEINTERNAL";
			}
		} else {
			if (trim($input[dns])=="") {
				return "NULLDNS";
			}
		}
		
		
		if ($input[status]=="disable") {
			$card[disabled]=1;
		} else {
			unset($card[disabled]);
		}
		
		$card[type]=$input[type];
		$card[assignment]=$input[assignment];
		if ($card[assignment]=="static") {
			$card[address]=$input[address];
			$card[netmask]=$input[netmask];
			if ($input[type]=="external") {
				$card[gateway]=$input[gateway];
				$card[weight]=$input[weight];
			}
		} else {
			unset($card[address]);
			unset($card[netmask]);
			unset($card[gateway]);
		}
		if ($input[weight]) 
			$card[weight]=$input[weight];
		
		if ($input[type]=="internal") 
			$card[pppoe]=1;


		if ($input[type]=="internal") {
			$fw[dhcp][_attributes][action]=$input[fwdhcp];
			$fw[webuser][_attributes][action]="allow";
			$fw[dns][_attributes][action]="allow";

		} else if ($input[type]=="external") {
			$fw[dhcp][_attributes][action]="drop";
			$fw[webuser][_attributes][action]="drop";
			$fw[dns][_attributes][action]="allow";
		}	
		$fw[ssh][_attributes][action]=$input[fwadmin];
		$fw[webadm][_attributes][action]=$input[fwadmin];
		
		$card[firewall]=$fw;
		
		$card[dns]=$input[dns];

		$card[firstdhcp]=$input[firstdhcp];

		$cards[$intnum]=$card;
		$cards["_num"]=count($cards);	
		$this->conf->conf[network][interfaces]["interface"]=$cards;
		

		ob_start();
		print_r($this->conf->conf);
		$tmp = ob_get_contents();
		ob_end_clean();
		file_put_contents("/tmp/saida0-".$this->actident,$tmp);	

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		record::act_log(_("Network card config changed"));
		return true;
	}
	

}


class act_proxysetup {

	var $actident; 
	var $conf;

	function __construct($actident) {
		$this->conf = new Conf("proxy");
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}
	
	
	function process ($input) {
		$cache_mb = intval($input[cache]);
		
		if ($cache_mb==0)
			$cache_mb=200; // padrao eh 200
		
		/*
		$cache_bytes = $cache_mb * 1024;
		
		$free_bytes = sysinfo::datadiskuse(1);
		
		// pegando somente 70% do que tem livre
		$safefree_bytes = $free_bytes * 0.70;
		*/
			
		if ($cache_mb > 1000)
			return "CACHETOOBIG";
		
		
		$this->conf->conf[proxy][memcache]	= intval($input[memcache]);
		$this->conf->conf[proxy][cache]		= $cache_mb;
		$this->conf->conf[proxy]["object"]	= intval($input["object"]);
		
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		record::act_log(_("Proxy Config changed"));
		return true;	
	}
	
	
	
	
}

?>