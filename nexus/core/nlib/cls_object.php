<?php

///////////////////////////////////////////////////////
// FUNCOES PARA RETORNO DO CONTEUDO DE OBJETOS
///////////////////////////////////////////////////////

/* FALTA:
 * Tratar o caso do objeto nao existir!
 * 	retornar vazio?
 * 	retornar vazio e colocar uma mensagem no wall? <- melhor!
 */

//include_once "common.nx";

class Object {
	
	var $objdef;
	var $userdef;
	var $netdef;
	var $obj_cmd_ip;
	var $obj_net_ipv4;
	
	var $linktable;

	function __construct() {
		include_once "common.nx";
		$this->obj_cmd_ip = new cmd_Ip();
	}
	function getinterfaces($type) {
		// NAO RETORNA AS INTERFACES DISABLEDS - modifiquei para o conncheck
		
		if (!$this->netdef)
			$this->netdef = xml::loadxml("network.xml");
		//print_r($this->netdef);
		$interfaces = xml::normalizeseq($this->netdef[network][interfaces]["interface"]);
		
		// coloco os atributos como chaves (se houver)
		for ($i=0;$i<count($interfaces);$i++) {
			if (is_array($interfaces[$i][_attributes])) { 
				foreach ($interfaces[$i][_attributes] as $k => $v) {
					$interfaces[$i][$k]=$v;
					unset($interfaces[$i][_attributes][$k]);
				}
			}
			unset($interfaces[$i][_attributes]);
			if (strtolower($interfaces[$i][type])==strtolower($type)
						&&!$interfaces[$i][disabled]==1) {
				$retint[]=$interfaces[$i];
			}
			
		}
		return $retint;
	}
	function get($object) {
		
		// dependendo da raiz eu chamo a funcao correspondente
		// isso somente se o valor tiver entre crase, senao eu
		// devolvo explodido por ; somente
	
		if (!ereg("^`.+`$",$object)) 
			if (ereg(";",$object)) 
				return explode(";",$object);
			else 
				return $object;
	
		// tirando a crase
		$object = ereg_replace("^`(.+)`$","\\1",$object);
		$object = explode(".",$object);
		
		switch (strtoupper($object[0])) {
			case "HOST":
				return $this->obj_host($object);
			case "IPRANGE":
				return $this->obj_iprange($object);
			case "NETWORK":
				return $this->obj_network($object);
			case "MAC":
				return $this->obj_mac($object);
			case "TIME":
				return $this->obj_time($object);
			case "SERVICE":
				return $this->obj_service($object);
			case "INTERFACE":
				return $this->obj_interface($object);
		}

	}
	// valores raizes q iremos trabalhar:
	// HOST		v
	// IPRANGE	v
	// NETWORK	v
	// MAC		v
	// TIME		v
	// SERVICE	v
	// INTERFACE
	
	
	function obj_interface($object) { 
		if (!$this->netdef)
			$this->netdef = xml::loadxml("network.xml");	


		switch ($object[1]) {
		case "USER":
			if (!$this->userdef)
				$this->userdef = xml::loadxml("forward.xml");		
			$users = xml::normalizeseq($this->userdef[forward][users][user]);
			for ($u=0;$u<count($users);$u++) {
				if ($users[$u][login]==$object[2])
					return $users[$u][int];
			}
		break;
		case "INTERNAL":
			$ints = $this->getinterfaces("internal");
			return $ints[0];
			
		}
		
		
	}
	
	function obj_service($object) {
		if (!$this->objdef)
			$this->objdef = xml::loadxml("objects-static.xml;objects.xml");		
		foreach (array("objects","objects-static") as $o) {
			$oservices = xml::normalizeseq($this->objdef[$o][services][service]);
			//unset($oservices[_num]);
			//print_r($oservices);
			for ($i=0;$i<count($oservices);$i++)
				if ($oservices[$i][id]) {
					$services[$oservices[$i][id]]=$oservices[$i];
					unset($services[$oservices[$i][id]][id]);
				}
		}		
		if ($object[1]=="GROUP") {
		foreach (array("objects","objects-static") as $o) {
				$oservgroup = xml::normalizeseq($this->objdef[$o][services][servicegroup]);
				//unset($oservgroup[_num]);
				
				for ($i=0;$i<count($oservgroup);$i++) {
					if ($oservgroup[$i][id]) {
						$tmp = explode(";",$oservgroup[$i][value]);
						foreach ($tmp as $k=>$v)
							$servgroup[$oservgroup[$i][id]][$v]=$services[$v];
					}
				}
			}  			
			// se tiver especificado o nome de um grupo...
			if ($object[2]) {
				$servgroup[$object[2]][_num]=count($servgroup[$object[2]]);
				return $servgroup[$object[2]];
			}else {
				$servgroup[_num]=count($servgroup);
				return $servgroup;
			}

		} else if ($object[1]) {
			return $services[$object[1]];
		} else {
			$services[_num]=count($services);
			return $services;
		}

	}

	function obj_time($object) {
		if (!$this->objdef)
			$this->objdef = xml::loadxml("objects-static.xml;objects.xml");		
		foreach (array("objects","objects-static") as $o) {
			$otimes = xml::normalizeseq($this->objdef[$o][times][time]);
			//print_r($otimes);
			for ($i=0;$i<count($otimes);$i++)
				if ($otimes[$i][id]) {
					$times[$otimes[$i][id]]=$otimes[$i];
					unset($times[$otimes[$i][id]][id]);
				}
		}		
		if ($object[1])
			return $times[$object[1]];
		else {
			$times[_num]=count($times);
			return $times;
		}
	}

	function interface_network () {
		$int = $this->obj_cmd_ip->interfaces();	
		if (!$this->ipv4)
			$this->obj_net_ipv4 = new Net_ipv4;		

		foreach ($int as $iname=>$ivalue) {
			foreach ($ivalue as $vname => $vvalue) {
				if ($vname=="ip") {
					for ($v=0;$v<count($vvalue);$v++) {
						$addr = $vvalue[$v][address];
						$mask = $vvalue[$v][netmask];
						$this->obj_net_ipv4->ip = $addr;
						$this->obj_net_ipv4->bitmask=$mask;
						$this->obj_net_ipv4->calculate();
						$nint[$vvalue[$v][name]]=$this->obj_net_ipv4->network.
							"/".$this->obj_net_ipv4->netmask;
					}
				}
			}
		}
		return $nint;
	}

	function obj_network ($object) {
		$int = $this->obj_cmd_ip->interfaces();	
		if (!$this->ipv4)
			$this->obj_net_ipv4 = new Net_ipv4;
	
		// a preferencia eh pegar o network direto no kernel.
		// presume-se que o network foi mergeado antes de 
		// querer obter alguma coisa dele, entao eh mais sensato
		// pegar do kernel essa informacao.
		//print_r($int);
		switch ($object[1]) {
		case "INTERFACE":
			switch ($object[2]) {
			case "INTERNAL":
				// pego o interface_network e devolvo soh quem
				// tiver marcado como internal
				$networks = $this->interface_network();
				$internal_int =$this->getinterfaces("INTERNAL");

				$internal_network=array();
				for ($i=0;$i<count($internal_int);$i++) {
					$internal_network[$internal_int[$i][device]]=$networks[$internal_int[$i][device]];
				}
				return $internal_network;
			case "EXTERNAL":
				$networks = $this->interface_network();
				$external_int =$this->getinterfaces("EXTERNAL");
				$external_network=array();
				for ($i=0;$i<count($external_int);$i++) {
					$external_network[$external_int[$i][device]]=$networks[$external_int[$i][device]];
				}
				return $external_network;

			default:
				$nint = $this->interface_network();
				if ($object[2])
					return $nint[$object[2]];
				else {
					$nint[_num]=count($nint);
					return $nint;
				}
			}
		
		break;
		default:
			// aqui eh pra pegar do object.xml
			if (!$this->objdef)
			$this->objdef = xml::loadxml("objects-static.xml;objects.xml");
			
		foreach (array("objects","objects-static") as $o) {
				$onetwork = xml::normalizeseq($this->objdef[$o][networks][network]);
				for ($i=0;$i<count($onetwork);$i++)
					if ($onetwork[$i][id])
						$networks[$onetwork[$i][id]]=$onetwork[$i][value];
			}
			if ($object[1])
				return $networks[$object[1]];
			else {
				$networks[_num]=count($networks);
				return $networks;
			}
		}	
	
	}

	function obj_mac($object) {
		$int = $this->obj_cmd_ip->interfaces();

		// Normalizar tudo maiusculo?

		foreach ($int as $k => $v)
			$macs[$k]=$v[mac];
		
		if ($object[1])
			return $macs[$object[1]];
		else {
			$macs[_num]=count($macs);
			return $macs;
		}		
	}
	
	function obj_iprange($object) {
		if (!$this->objdef)
			$this->objdef = xml::loadxml("objects-static.xml;objects.xml");

		foreach (array("objects","objects-static") as $o) {
			$oranges = xml::normalizeseq($this->objdef[$o][ipranges][iprange]);
			for ($i=0;$i<count($oranges);$i++)
				if ($oranges[$i][id])
					$ranges[$oranges[$i][id]]=$oranges[$i][value];
		}
		
		if ($object[1])
			return $ranges[$object[1]];
		else {
			$ranges[_num]=count($ranges);
			return $ranges;
		}
		
	}
	
	function obj_host($object) {
	
		// palavras reservadas para segunda parte do objeto:
		// USER
		// GATEWAY
		// INTERFACE
		//print_r($this->objdef);
		switch (strtoupper($object[1])) {
		case "USER":
			// temos q pegar o ip em user.xml		
			if (!$this->userdef)
				$this->userdef = xml::loadxml("forward.xml");
				
			$users = xml::normalizeseq($this->userdef[forward][users][user]);
			for ($u=0;$u<count($users);$u++) {
				$uret[$users[$u][login]]=$users[$u][ip];
			}
			if ($object[2]) 
				return $uret[$object[2]];
			else {
				$uret[_num]=count($uret); 
				return $uret;
			}

		case "GATEWAY":
			/*
			// retorna um array com os gateways
			// temos q pegar no ip route

			$gw = $this->obj_cmd_ip->gateways();
			if ($object[2]) {
				return $gw[$object[2]];
			} else {
				$gw[_num]=count($gw);
				return $gw;
			}
			*/
			$this->openlinktable();
			if (!$object[2]) { return $this->linktable[gateway]; }
			
			return $this->linktable[gateway][$object[2]];
			
		case "INTERFACE":
			// temos q pegar no ip route
			$int = $this->obj_cmd_ip->interfaces();
			//print_r($int);
			foreach ($int as $iname=>$ivalue)
				foreach ($ivalue as $vname => $vvalue)
					if ($vname=="ip")
						for ($v=0;$v<count($vvalue);$v++)
							$rint[$vvalue[$v][name]]=$vvalue[$v][address];
			if ($object[2])
				return $rint[$object[2]];
			else {
				$rint[_num]=count($rint); 
				return $rint;
			}
				
		default:
		if (!$this->objdef)
			$this->objdef = xml::loadxml("objects-static.xml;objects.xml");

			//print_r($this->objdef[objects]);
		foreach (array("objects","objects-static") as $o) {
				$ohosts = xml::normalizeseq($this->objdef[$o][hosts][host]);
				for ($i=0;$i<count($ohosts);$i++)
					if ($ohosts[$i][id])
						$hosts[$ohosts[$i][id]]=$ohosts[$i][value];
			}
		}
		if ($object[1])
			return $hosts[$object[1]];
		else {
			$hosts[_num]=count($hosts);
			return $hosts;
		}
	}
	
	function openlinktable() {
		clearstatcache();
		$this->linktable = @parse_ini_file(DIRTMP."nx_linktable",1);
		if (!$this->linktable) { 
			// nessa versao ainda nao tem o linktable, entao vou dar um jeito, usando o gateway atual
			
			$ip_gws = $this->obj_cmd_ip->gateways();
			
			$new_gws = array();
			foreach ($ip_gws as $int => $gws) {
				$new_gws[$int]=$gws[0];
			}
			
			$this->linktable[gateway]=$new_gws;
			
		}
	}
}

//$o=new Object();
//print_r($o->getinterfaces("external"));
//print_r($o->get("`TIME`")); 


?>
