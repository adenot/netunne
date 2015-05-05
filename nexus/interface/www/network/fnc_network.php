<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 20/07/2006					*
	*																*
	****************************************************************/


class networksetup {
	
	var $obj;
	var $conf;
	var $hw;
	
	function __construct() {
		$this->obj = new Object();
		$this->conf = new Conf("network");
	}
	
	/****
	 * networksetup::getconncheck
	 * pega os eventos (como em server.php)
	 * mas filtra pelo evento de conncheck
	 */
	function getconncheck() {
		if (file_exists(DIRDATA."events.ser")) {
			$err = unserialize(file_get_contents(DIRDATA."events.ser"));
			$ok=1;
		} else { $err = array(); }
		
		$ret = array();
		if (!$ok||!is_array($err)) { return $ret; }
		foreach ($err as $er) {
			if ($er[id]=="conncheck") {
				$er[desc]="[".conv::formatdate($er[time])."]\n ".$er[desc];
				$ret[]=$er;
			}
		}
		return $ret;
		
	}
	
	function getspeeds () {
		$list[1] = "256 Kbps";
		$list[2] = "512 Kbps";
		$list[3] = "768 Kbps";
		$list[4] = "1 Mbps";
		$list[6] = "1.5 Mbps";
		$list[8] = "2 Mbps";
		$list[16] = "4 Mbps";
		$list[32] = "8 Mbps";
		return $list;
	}
	
	
	function getnetmasks ($excludezero=0) {
		$list = "255.255.255.255;255.255.255.252;255.255.255.248;255.255.255.240;255.255.255.224;255.255.255.192;255.255.255.128;".
				"255.255.255.0;255.255.254.0;255.255.252.0;255.255.248.0;255.255.240.0;255.255.224.0;255.255.192.0;255.255.128.0;255.255.0.0;".
				"255.254.0.0;255.252.0.0;255.248.0.0;255.240.0.0;255.224.0.0;255.192.0.0;255.128.0.0;255.0.0.0;0.0.0.0";
		$list = explode(";",$list);
		if ($excludezero==1) { array_pop($list); }
		foreach ($list as $l) {
			$ret[$l]=$l;
		}
		return $ret;
	}
	
	function getcarddata() {
		$obj = $this->obj;
		$conf = $this->conf;
		if (!$this->hw) {
			$this->gethardware();
		}
		//print_r($this->hw);
		$cards = xml::normalizeseq($conf->conf[network][interfaces]["interface"]);
		
		for ($i=0;$i<count($cards);$i++) {
			$device = xml::getxmlval("device",$cards[$i]);
			$ip = $obj->get("`HOST.INTERFACE.$device`");
						
			if ($cards[$i][type]=="internal") {
				$type=_("Internal");
				
			} else if ($cards[$i][type]=="external") {
				$type=_("External");
				
			} else {
				$type = $cards[$i][type];
			}
			if ($cards[$i][assignment]=="static") {
				$addr = $cards[$i][address];
				
			} else if ($cards[$i][assignment]=="dynamic") {
				$ip = $obj->get("`HOST.INTERFACE.$device`");
				$addr = $ip." (dhcp)";
			}
			
			if (trim($ip)=="") { $status=_("Down"); } else { $status=_("Up"); }
			
			$hw = croptext($this->hw[all][$device],25);
			
			$ret[] = array($device,$type,$addr,$hw,$status);

		}
		return $ret;
		
	}
	function getcard($id) {
		$conf = new Conf("network");
		$obj = new Object();
		
		$cards = xml::normalizeseq($conf->conf[network][interfaces]["interface"]);
		
		
		$ip = $obj->get("`HOST.INTERFACE.$device`");
		
		//echo $id;print_r($cards);
		
		foreach ($cards as $card) {
			$device = xml::getxmlval("device",$card);
			if ($device==$id) {
				$card[realip]=$ip;
				return $card;
			}
		}
	
		return false;
	}
	
	/***************
	 * gethardware ()
	 * retorna: 
	 * 	ret[all][eth1]="AMD Pcnet..."
	 * nao configuradas:
	 *  ret[eth2]="amd..."
	 */
	function gethardware() {
		if ($this->hw)
			return $this->hw;
		
		$conf = $this->conf;
		$cards = xml::normalizeseq($conf->conf[network][interfaces]["interface"]);
		$os = new Os();
		$hw = $os->found_networkcards();
		if (!$hw) { return; }
		$ret[all]=$hw;
		
		foreach ($cards as $card) {
			$devices[]=xml::getxmlval("device",$card);
		}
		
		
		foreach ($hw as $int => $v) {
			if (!in_array($int,$devices)) {
				$ret[$int]=$v;
			}
		}
		$this->hw=$ret;
		return $ret;
	}
	function gethardware_noconfig() {
		$hw = $this->gethardware();
		$cards = xml::normalizeseq($conf->conf[network][interfaces]["interface"]);
		unset($hw[all]);
		foreach ($cards as $card) {
			unset($hw[xml::getxmlval("device",$card)]);
		}
		
		
		foreach ($hw as $int => $h) {
			$hw2[]=array($int,_("Unconfigured"),"",croptext($h,25),"Down");
		}
		
		return $hw2;
	}

	function getdns() {
		$ret =  $this->conf->conf[network][dns][nameserver];
		return $ret;

	}
	function getprimary() {
		
		return Network::getprimary();
		
		/*
		// retorna a interface de maior peso OU a marcada como primary
		$cards = xml::normalizeseq($this->conf->conf[network][interfaces]["interface"]);
		$maxweight=0;
		foreach ($cards as $card) {
			if ($card[type]=="external") {
				if ($card[weight]>$maxweight) {
					$maxweight=$card[weight];
					$primary=$card[device];
				}
				if ($card[primary]==1)
					return $card[device];
			}
		}
		return $primary;
		*/
	}
	function getgateways() {
		
		$cards = xml::normalizeseq($this->conf->conf[network][interfaces]["interface"]);
		$speeds=$this->getspeeds();
		
		foreach ($cards as $card) {
			if ($card[type]=="external") {
				$card[device] = xml::getxmlval("device",$card);
				$ret[$card[device]]=$card[device]." (".$speeds[$card[weight]].",".$this->obj->get("`HOST.INTERFACE.".$card[device]."`").")";
			}
		}
		return $ret;
		
	}
	
	
}

?>
