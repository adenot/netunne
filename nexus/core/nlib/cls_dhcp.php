<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 13/02/2006					*
	*																*
	****************************************************************/

/* FALTA
 * colocar no ifup.d pra mergear o dhcp novamente,
 * pois pode ter interface interna com dhcp e assim
 * vai alterar o dhcpd.conf
 */

class Dhcp {

	var $dhcptpl;
	var $conf;
	var $obj;
	var $user;
	var $out;
	var $intlist=array();

	function opendhcpxml () {
		if ($this->conf) { return; }
		$this->conf = xml::loadxml("dhcp.xml");
		$this->conf = $this->conf[dhcp];
	}
	function opendhcptpl () {
		$this->dhcptpl = xml::loadxml(DIRTPL."/dhcp.xml.tpl");
	}
	
	function writedhcpxml () {
		$conf = new Conf(false);
		$conf->conf[dhcp]=$this->conf;
		$conf->xmllist="dhcp.xml";
		
		//print_r($conf->conf);
		$conf->write();
		
	}
	
	//function addinterface
	
	
	function processdhcp () {

		/* ROTEIRO:
		 * 1) pego o tpl global, pego os options do conf e coloco no local
		 * 2) para cada conf->network, vai um tpl->subnet
		 * 3) para cada mac de usuario, eu crio um tpl->host pra ele
		 */
		 
		/* TESTES Q FALTA (m) SER FEITOS:
		 * 1) mais de um mac pra cada usuario? NAO
		 * 2) permitir mais de uma subrede por placa? NAO
		 * 3) o host do usuario precisa estar dentro da subnet? SIM
		 */
		
		//print_r($this->dhcptpl);
		//print_r($this->conf);
		
		// OPTION
		
		$options = xml::normalizeseq($this->conf[config][option]);
		for ($i=0;$i<count($options);$i++) {
			$var[option] .= conv::tplreplace($this->dhcptpl[option],$options[$i])."\n";
		}
		//print_r($var);
		
		// SUBNET
		$networks = xml::normalizeseq($this->conf[networks][network]);
		$varsubnet=array();
		$s=0;
		
		//print_r($this->obj->get("`NETWORK.INTERFACE.INTERNAL`"));

		for ($i=0;$i<count($networks);$i++) {
			// variaveis:
			/*				requerido
			 * RANGE		*
			 * DNSs	
			 * GATEWAY
			 * NETMASK
			 * INTERFACE	*
			 * OPTIONs...
			 */
			$varsubnet[$s][range][0][range1]=$networks[$i][range][from];
			if ($networks[$i][range][to])
				$varsubnet[$s][range][0][range2]=$networks[$i][range][to];
			
			$int = $networks[$i]["interface"];
			
			$this->intlist[]=$int;
			
			// essa interface tem q ter ip interno.. 
			$interfacehost = $this->obj->get("`HOST.INTERFACE.$int`");
			$interfacenetwork0 = explode("/",$this->obj->get("`NETWORK.INTERFACE.$int`"));
			$interfacenetwork=$interfacenetwork0[0];
			$interfacenetmask=$interfacenetwork0[1];
			$varsubnet[$s][network]=$interfacenetwork;
			
			$dnss=array();
			if ($networks[$i][dnss]) 
				$dnss=xml::normalizeseq($networks[$i][dnss][dns]);

			if (count($dnss)==0)
				$dnss[0]=$interfacehost;


			foreach ($dnss as $k => $v)
				$dnss[$k] = $this->obj->get($v);
			$varsubnet[$s][option].="\toption domain-name-servers ".implode(", ",conv::arrayclean($dnss)).";\n";
			$gateway=$networks[$i][gateway];
			$gateway = $this->obj->get($gateway);
			
			if (!Net_ipv4::validateIP($gateway)) 
				$gateway=$interfacehost;
			
			$varsubnet[$s][option].="\toption routers ".$gateway.";\n";
			
			if ($networks[$i][netmask]) {
				$netmask = $this->obj->get($networks[$i][netmask]);
			} else {
				$netmask = $interfacenetmask;
			}
			$varsubnet[$s][netmask]=$netmask;
			
			$s++;
		}
		//print_r($varsubnet);
		
		foreach ($varsubnet as $varsn)
			$subnet .= conv::tplreplace($this->dhcptpl[subnet],$varsn)."\n";
		
		
		// HOST ////////////////////////////////////////////////////////
		/* roteiro:
		 * 1) pego os usuarios
		 * 2) para cada mac de cada usuario, eu coloco um host[] com host,mac e ip
		 * ** o host vai ser userX, sendo X a contagem do mac
		 */
		$users = xml::normalizeseq($this->user->conf[users][user]);
		$varhost=array();
		$h=0;
		for ($i=0;$i<count($users);$i++) {
			if (trim($users[$i][macs])=="" ||
				trim($users[$i][ip])=="")
				continue;
			
			$macs=explode(",",$users[$i][macs]);
			$l=1;
			foreach ($macs as $mac) {
				$varhost[$h][host]=$users[$i][login].$l;
				$l++;
				$varhost[$h][mac]=trim($mac);
				$varhost[$h][ip]=$users[$i][ip];
				
				$h++;
			}
		}
		$varhost[$h][host]="thisserver";
		$varhost[$h][mac]="AA:BB:CC:DD:EE:FF";
		$varhost[$h][ip]=$interfacehost;
		$h++;
		
		
		
		//print_r($varhost);
		foreach ($varhost as $varh) 
			$host .= conv::tplreplace($this->dhcptpl[host],$varh)."\n";
			
		//print($host);
		$var[host]=$host;
		$var[subnet]=$subnet;
		$var[interfacehost]=$interfacehost;
		
		$this->out = conv::tplreplace($this->dhcptpl["global"],$var);
		
	}
	function filedhcpconf() {
		file_put_contents(DIROUT."/dhcp/dhcpd.conf",$this->out);
	}
	function filedhcpdefault() {
		$var["interface"]= implode(" ",$this->intlist);
		$out = conv::tplreplace($this->dhcptpl[filedefault],$var);
		file_put_contents(DIROUT."/dhcp/dhcp.default",$out);
	}

	
	function merge() {
		include_once "common.nx";
		
		$this->obj = new Object();
		
		$this->user= new Forward();
		$this->user->openforwardxml();

		$this->opendhcpxml();		
		$this->opendhcptpl();
		
		$this->processdhcp();
		
		$this->filedhcpconf();
		$this->filedhcpdefault();
		

	}
	
}
?>
