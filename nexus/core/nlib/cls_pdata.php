<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 17/01/2006					*
	*																*
	****************************************************************/

class pdata { 
	
	var $coreconf;
	var $pdo;
	function __construct($dsn="") {
		$this->coreconf = xml::getcoreconfig();

		if (!$this->coreconf[core][data][dsn]&&$dsn=="") { return; }

		if ($dsn=="") {
			$dsn = explode(",",str_replace("{NEXUS}",NEXUS,$this->coreconf[core][data][dsn]));
		} else {
			$dsn = explode(",",$dsn);
		}
			
		try {
			$this->pdo = new PDO($dsn[0],$dsn[1],$dsn[2]);
		} catch (PDOException $e) {
			echo "Invalid data DSN: " . $e->getMessage();
		}
	}
}

/*
 * CREATE TABLE log (id integer NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,time integer,action text,owner text DEFAULT system)
 */
 

class datalog {
	var $pdata;
	
	function datalog($pdata=0) {
		if ($pdata==0) {
			$pdata = new pdata("sqlite:".DIRDB."/log.db");
		}
		$this->pdata = $pdata;
	}
	
	function insert ($action,$owner) {
		/*
		 * possiveis actions:
		 * log_in,usuario/guest.credito				usuario logou
		 * log_out,usuario/guest.credito			usuario deslogou
		 * changepassword,usuario					usuario alterou senha
		 * log_disabled,usuario/guest.credito		usuario tentou logar mas estah desabilitado
		 * log_limit,usuario/guest.credito			usuario tentou logar mas seu limite jah foi alcancado
		 * log_changemac,usuario					usuario tentou logar com mac diferente
		 * 
		 */
		
		
		shell_exec("echo $action $owner >> /tmp/log.db.log");
		
		try {
			$time = time();
			$pres = $this->pdata->pdo->query("INSERT INTO log (time,action,owner) VALUES ($time,'$action','$owner');");
		} catch (PDOException $e) {
			echo $e->getMessage();
		}

	}
	function select ($action="",$owner="",$time="") {
		if ($action!="")
			$sql[] = "action = '$action'";
		if ($owner!="") 
			$sql[] = "owner = '$owner'";
		if ($time!="")
			$sql[] = "time > $time";
			
		if (is_array($sql)) {
			$where = implode(" AND ",$sql);
			$where = "WHERE $where";
		}
		
		try {
			$pres = $this->pdata->pdo->query("SELECT * FROM log $where;");
			$res = $pres->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}	
		
		return $res;
	}
	function delete ($action="",$owner="",$time="") {
		if ($action!="")
			$sql[] = "action = '$action'";
		if ($owner!="") 
			$sql[] = "owner = '$owner'";
		if ($time!="")
			$sql[] = "time > $time";
			
		if (is_array($sql)) {
			$where = implode(" AND ",$sql);
			$where = "WHERE $where";
		}
		
		try {
			$pres = $this->pdata->pdo->query("DELETE FROM log $where;");
		} catch (PDOException $e) {
			echo $e->getMessage();
		}	
		
	}
	
	
}

?>
