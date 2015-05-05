<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 07/07/2006					*
	*																*
	****************************************************************/

class cmd_Arp {
	
	var $res;
	
	function __construct () {
		$this->cmdarp();
	}
	
	function cmdarp() {
		$this->res = shell_exec("arp -n");
	}
	function getmac($ip) {
		$res = $this->res;
		$res = explode("\n",$res);
		for ($i=1;$i<count($res);$i++) {
			$res0 = sscanf($res[$i],"%s%s%s%s%s%s%s");
			if (trim($res0[0])==$ip) {
				return trim($res0[2]);
			}
		}
		return false;
	}	
	function getint ($ip) {
		$res = $this->res;
		$res = explode("\n",$res);
		for ($i=1;$i<count($res);$i++) {
			$res0 = sscanf($res[$i],"%s%s%s%s%s%s%s");
			if (trim($res0[0])==$ip) {
				return trim($res0[4]);
			}
		}
		return false;
		
		
	}
}

//echo cmd_Arp::getmac("192.168.100.41");

?>
