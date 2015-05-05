<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 23/06/2006					*
	*																*
	****************************************************************/


class act_licenseinfo {
	var $conf;
	var $actident;
	
	function __construct($actident) {
		$this->conf = new Conf("info");
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}
	
	function process($input) {
		// esperados: user/userkey
		
		if (trim($input[user])=="" || trim($input[userkey])=="") {
			return "NULLID";
		}
		
		do {
			
			$this->conf->set("info/user",$input[user]);
			$this->conf->set("info/userkey",$input[userkey]);
			$this->conf->write();

			unset($this->conf);
			clearstatcache();
			
			break; // resolvi escrevendo no TMP antes de copiar, no cls_conf.
			sleep(2);
			
			$this->conf = new Conf("info");
			
			$username = $this->conf->get("info/user");
			$userkey  = $this->conf->get("info/userkey");

		} while ($input[user]!=$username || $input[userkey]!=$userkey );
		
		
		//record::act_log(_("License Info changed"));
		
		// garante q o xml vira valido sem mergear
		// JAH FAZ ISSO O WRITE
		//$this->conf->copyxml("info");
		
		return true;
	}
	
}



class act_prefetchupdates {
	var $actident;
	
	function __construct($actident) {
		$this->actident = $actident;
	}	
	function process() {
		$url[] = "https://www.console.com.br/center/updates/npak.list";
		$url[] = "https://denotops.powweb.com/center/updates/npak.list";
		$url[] = "https://www2.console.com.br/center/updates/npak.list";
		$url[] = "https://www.neolinux.com.br/center/updates/npak.list";
		$url[] = "https://www2.neolinux.com.br/center/updates/npak.list";
		$url[] = "https://puc.console.com.br/center/updates/npak.list";
		$url[] = "https://puc2.console.com.br/center/updates/npak.list";
		foreach ($url as $u) {
			if (!file_get_contents($u))
				record::wall($this->actident,"error on $u");
		} 
				
		
	}
	
	
}

class act_datetime {
	var $conf;
	var $actident;
	
	function __construct($actident) {
		$this->actident = $actident;
	}
	
	function process($input) {
		// [MMDDhhmm[[CC]YY][.ss]]
		$date = sprintf("%02s",$input["date_month"]);
		$date.= sprintf("%02s",$input["date_day"]);
		$date.= sprintf("%02s",$input["time_hour"]);
		$date.= sprintf("%02s",$input["time_minute"]);
		$date.= $input["date_year"];
		
		
		$conn = new Conn();
		$conn->command(message::generate_function("DATE",$date),$this->actident);

		record::act_log(_("Date/Time changed"));
		return true;
	}
	
}

class act_locale {
	var $conf;
	var $actident;
	
	function __construct($actident) {
		$this->conf = new Conf("info");
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}
	
	function process($input) {
		$this->conf->set("info/lang",$input[lang]);
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		//$this->conf->copyxml("info");
		
		return true;
	}
	
}


class act_confold {
	var $conf;
	var $actident;
	
	function __construct($actident) {
		$this->conf = new Conf("info");
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}
	
	function process($input) {
		$this->conf->set("info/confold",$input[confold]);
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		return true;
	}
	
}

?>
