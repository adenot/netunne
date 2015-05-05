<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 03/07/2006					*
	*																*
	****************************************************************/

class Userlog {
	/**************************
	 * retorna: 
	 * res[0] = icone
	 * res[1] = mensagem
	 * res[2] = 1|0 = terminou ou nao
	 */
	function parse($res,$action) {
		$ret = array();
		$ret[2]="0";
		
		if (ereg("/EOF/.*\$",$res)) {
			$ret[2]="1";
			$res = trim(str_replace("/EOF/","",$res));
			$res .= Act::process($action,$res);
			//echo $res;
		}
		
		
		
		$res = explode("\")\n",trim($res));
		//print_r($res);
		$res = trim($res[count($res)-1]);
	
		if (substr_count($res,"(\"")!=0&&substr_count($res,"\")")==0) {
			$res=$res."\")";
		} 
		//echo "A".$res."B";	
		
		$witch = message::witch_function($res);
		//print_r($witch);
		switch ($witch[0]) {
			case "SERVERERROR":
				$ret[0]="fail";
				switch ($witch[1][1]) {
					case "KEYERROR":
						$ret[1]=_("Invalid Key");
						break;
					case "INVALIDLICENSE":
						$ret[1]=_("Invalid License");
						break;
					case "NULLUSER":
						$ret[1]=_("Null User");
						break;
					default:
						$ret[1]=$witch[1][1];
						break;
				}
				break;
			case "SERVERINFO":
				$ret=0;
				break;
			case "INFO":
				$ret[0]="info";
				$ret[1]=$witch[1][0];
				break;
			case "OK":
				$ret[0]="ok";
				if (trim($witch[1][0])=="") {
					$ret[1]=_("Ok");
				} else {
					$ret[1]=$witch[1][0];
				}
				break;
			case "FAIL": case "ERROR":
				$ret[0]="fail";
				if (trim($witch[1][0])=="") {
					$ret[1]=_("Failed");
				} else {
					$ret[1]=$witch[1][0];
				}
				break;
			
		}

		return $ret;
					
	}
		
}

?>
