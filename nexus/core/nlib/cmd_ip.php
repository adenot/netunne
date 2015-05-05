<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 09/12/2005					*
	*																*
	****************************************************************/

//include_once "common.nx";	


class cmd_Ip {
	
	var $iproute;
	var $ipaddr;
	var $iplink;
	var $addr;
	var $gateways;
	
	// Chama o comando ip cmd
	function ipcmd ($cmd) {
		$result=shell_exec("ip $cmd");
		return $result;
	}
	
	// Resultado de `ip route`
	function route () {
		if (!$this->iproute)
			$this->iproute = $this->ipcmd("route");
		return $this->iproute;
	}
	function addr () {
		if (!$this->ipaddr)
			$this->ipaddr = $this->ipcmd("addr");
		return $this->ipaddr;
	}
	function link () {
		if (!$this->iplink)
			$this->iplink = $this->ipcmd("link");
		return $this->iplink;
	}

	// function GATEWAYS
	// Retorna um Array com os gateways por interface
	/* Array
	 * (
	 *    [eth1] => Array
	 *         (
	 *             [0] => 192.168.100.1
	 *         )
	 * 
	 * )
	 */
	function gateways() {
		if ($this->gateways)
			return $this->gateways;
			
		// FALTA:
		// (1 ) Testar se for multilink
		
		// qdo for multilink, vai mudar um pouco a sintaxe,
		// preciso q essa funcao retorne quando tem mais de um gateway do modo normal 
		// e depois q subir o multilink, a funcao precisa tratar diferente
		
		$cmd = $this->route();
		$cmd = explode("\n",$cmd);
		$gw=array();
		for ($i=0;$i<count($cmd);$i++) {
			$tmp=array();
			$cmd[$i]=trim($cmd[$i]);
			if (ereg("default.+via (.+) dev ([a-zA-Z0-9]+)",$cmd[$i],$tmp))
				if (!empty($tmp))
					$gw[$tmp[2]][]=$tmp[1];
		}
		$this->gateways=$gw;
		return $this->gateways;
	}
	// function INTERFACES
	// retorna um array como o abaixo
	/*
	 *     [eth1] => Array
	 *         (
	 *             [type] => ether
	 *             [mac] => 00:11:2f:c2:a2:03
	 *             [ip] => Array
	 *                 (
	 *                     [0] => Array
	 *                         (
	 *                             [address] => 192.168.100.31
	 *                             [netmask] => 24
	 *                             [broadcast] => 192.168.100.255
	 *                             [name] => eth1 (* aqui pode ser eth1:1 se tiver mais de um ip *)
	 *                         )
	 * 
	 *                 )
	 * 
	 *         )
	 */
	function interfaces($link=0) {
		if ($this->addr)
			return $this->addr;
		
		if ($link==1)
			$cmdaddr = $this->link();
		else 
			$cmdaddr = $this->addr();
		//
		// PROCESSANDO ADDR
		//
		// separando as interfaces:
		$exp = "\n[0-9]+: ";
		$cmdaddr = split($exp,"\n".$cmdaddr);

		// tiramos o 0 e os outros comecam de eth0:...
		//
		$cmdaddr = str_replace("\n","|",$cmdaddr);
		$exp = "^([a-z0-9]+): (.+)";

		for ($c=1;$c<count($cmdaddr);$c++) {
			ereg($exp,$cmdaddr[$c],$addr0[$c]);
		}
		//print_r($addr0);
		for ($i=1;$i<count($addr0)+1;$i++) {		
			$int = $addr0[$i][1];
			
			if (conv::startwith("sit",$int)) { continue; }
			
			// separo por | depois limpo os espacos
			$tmp = explode("|",$addr0[$i][2]);
			foreach ($tmp as $k => $v) {
				$tmp[$k] = trim($v);
			}
			$addr[$int]=$tmp;
			$ipc=0;
			for ($a=0;$a<count($addr[$int]);$a++) { 
				$tmp2=array();
				// agora tenho q procurar por <.> pra pegar o tipo de interface
				if (!$addr2[$int][properties]) {
					if (ereg("<(.+)>",$addr[$int][$a],$tmp2)) 
						$addr[$int][properties]=explode(",",$tmp2[1]);
				}
				if (!$addr2[$int][type]) {
				// agora tenho q procurar pelo link/ether para pegar o mac
					if (ereg("link/(.+) (.+) brd (.+)",$addr[$int][$a],$tmp2)) {
						$addr2[$int][type]=$tmp2[1];
						$addr2[$int][mac]=$tmp2[2];
					}
				} 
				$expip = "\\b\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\b";
				// agora tenho q procurar por inet pra pegar os ips
				//echo "testando ".$addr[$int][$a]."\n";
				if (ereg("inet (.+)/([0-9]{1,2}) brd (.+) scope (.+) (.+)",$addr[$int][$a],$tmp2)) {
					$addr[$int][ip][$ipc]=$tmp2; 
					$addr2[$int][ip][$ipc][address]=$addr[$int][ip][$ipc][1];
					$addr2[$int][ip][$ipc][netmask]=$addr[$int][ip][$ipc][2];
					$addr2[$int][ip][$ipc][broadcast]=$addr[$int][ip][$ipc][3];
					$addr2[$int][ip][$ipc][name]=$addr[$int][ip][$ipc][5];
					$ipc++;
				}
			}
		}
		$this->addr = $addr2;
		return $this->addr;
	}

}

//$ip = new cmd_Ip();
//print_r($ip->gateways());
//print_r($ip->interfaces());

?>
