<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 17/01/2006					*
	*																*
	****************************************************************/

class pdata { 
	
	var $coreconf;
	var $pdo;
	function __construct() {
		$this->coreconf = xml::getcoreconfig();

		if (!$this->coreconf[core][data][dsn]) { return; }

		$dsn = explode(",",str_replace("{NEXUS}",NEXUS,$this->coreconf[core][data][dsn]));

		try {
			$this->pdo = new PDO($dsn[0],$dsn[1],$dsn[2]);
		} catch (PDOException $e) {
			echo "Invalid data DSN: " . $e->getMessage();
		}
	}
			
	
	
}


?>
