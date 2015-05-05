<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 19/06/2006					*
	*																*
	****************************************************************/

//require_once "lib_common.php";

class Conn {
	var $port;
	var $host;
	
	function conn() {
		$this->initialize();
	}
	
	function initialize () {
		$conn=explode(":",$GLOBALS[CONF][CONN]);
		$this->port=$conn[1];
		$this->host=$conn[0];
		//print_r($GLOBALS[CONF][CONN]);		
		
	}
	
	function is_result($res0) {
		//echo "testando $res\n";
		
		// pego sempre a ultima linha com texto
		$res0 = trim($res0);
		$res = explode("\n",$res0);
		$res = $res[count($res)-1];
		
		if ($res=="OK"||$res=="FAIL") {
			return true;
		}
		
		$ret = eregi("result.*\\(([\\s\\n\\t\\r\\w\\\"\\']*)\\)*",$res,$tmp);
		//var_dump($tmp);
		return $ret;		
	}
	
	function command($str,$wall="") {
		if (!$this->host)
			$this->initialize();
		
		$er=0;
		
		do {
			$fp = @fsockopen($this->host, $this->port, $errno, $errstr, 2);
			$er++;
		} while (!$fp&&$er!=5);
		
		
		if (!$fp) {
			echo "$errstr ($errno)\n";
			return -1;
		}
		
		//echo "Connected to ".$this->host.":".$this->port."\n";
		if ($wall!="")
			fwrite($fp,message::generate_function("WALL",$wall)."\n");
		
		fwrite($fp,$str."\n");
		
		if ($wall!="")
			return true;
		
		while (!feof($fp)&&!$this->is_result($res)) {
			if ($wall!="") {
				$res .= record::wall($wall,fgets($fp,4096));
			} else {
				$res .= fgets($fp,4096);
			}
		}
		//echo $res;
		//record::wall($wall,"EOF");
		
		fclose($fp);
		return trim($res);
	}
}

//$a = new Conn();
//echo $a->command("checkuser(spiked,visual)");
//print_r($GLOBALS);
?>
