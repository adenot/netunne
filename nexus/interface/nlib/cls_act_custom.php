<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 20, 2006					*
	*																*
	****************************************************************/

class act_customsetup {
	var $actident;
	
	function __construct($actident) {
		$this->actident = $actident;
	}
	
	function process($input) {
		$custom = parse_ini_file(DIRSET."settings.ini",1);
		
		print_r($input);return;
		
		foreach ($input as $k => $v) {
			if ($k=="logo") { continue; }
			$custom[custom][$k]=htmlentities($v);
		}
		unset($custom[custom][theme]);
		unset($custom[custom][advanced]);
		
		if ($input[logo]) {
			//print_r($input[logo]);
			if (!($input[logo][type]=="image/pjpeg" || $input[logo][type]=="image/jpeg" || $input[logo][type]=="image/gif")) {
				echo "<!-- ".$input[logo][type]." -->";
				return "INVALIDIMAGE";
			}
			
			shell_exec("rm -fr ".DIRAUTH."/logo/logo*");
			
			$file = $input[logo][name];
			$logo = explode("/",$file);
			$logo = $logo[count($logo)-1];
			$logo = explode(".",$logo);
			$logo = "logo.".$logo[count($logo)-1];
	
			$tmpfile = $input[logo][tmp_name];
			$file = DIRAUTH."/logo/$logo";
			
			shell_exec("cp -af $tmpfile $file");
			
		}
		if  ($input[forceurl]!="" && 
			(!(conv::startwith("http://",$input[forceurl]) || conv::startwith("https://",$input[forceurl]) )))
			return "INVALIDFORCEURL";
		
		
		write_ini_file(DIRSET."settings.ini",$custom);
		clearstatcache();
		
		return true;
	}
	
	function processbill($input) {
		$conf = new Conf("forward");
		$conf->set("forward/billing/day",$input[day]);
		
		if (!$conf->write()) {
			return record::wall($this->actident);
		}
		//$conf->copyxml("forward");
		
		//record::act_log(_("Billing day changed"));
		return true;
		
	}
	
	function restore($lang) {
		$msg = parse_ini_file(DIRLOCALEINTERFACE."/custom.ini",1);
		
		if (!$msg[$lang]) { return false; }
		
		$input = $msg[$lang];
		
		$custom = parse_ini_file(DIRSET."settings.ini",1);
		
		foreach ($input as $k => $v) {
			if ($k=="logo") { continue; }
			$custom[custom][$k]=$v;
		}
		
		write_ini_file(DIRSET."settings.ini",$custom);
		clearstatcache();
		
		return true;
		
	}
	
	function changecustomlogin() {
		
		if (!file_exists(NEXUS."/core/data/userthemes/custom/") || !file_exists(NEXUS."/interface/userauth/theme/custom/")) { 
			shell_exec("mkdir -p ".NEXUS."/core/data/userthemes/custom/");
			shell_exec("ln -s ".NEXUS."/core/data/userthemes/custom/ ".NEXUS."/interface/userauth/theme/custom/");
		}
		// inverte
		$custom = parse_ini_file(DIRSET."settings.ini",1);
		if ($custom[custom][advanced]==1) {
			unset ($custom[custom][advanced]);
			unset ($custom[custom][theme]);
		} else {
			$custom[custom][advanced] = 1;
			$custom[custom][theme] = "custom";
		}
		write_ini_file(DIRSET."settings.ini",$custom);
	}
	
	function changetheme ($input) {
		$custom = parse_ini_file(DIRSET."settings.ini",1);
		
		if (trim($input[theme])=="") { $input[theme] = "default"; }
		
		$custom[custom][theme]=$input[theme];
		
		return write_ini_file(DIRSET."settings.ini",$custom);
		
	}

	function uploadpage($input) {
		$custom = parse_ini_file(DIRSET."settings.ini",1);
		
		foreach ($input as $k => $v) {
			if ($k=="logo") { continue; }
			$custom[custom][$k]=htmlentities($v);
		}
		$custom[custom][theme]="custom";
		$custom[custom][advanced]=1;
		
		if ($input[logo]) {
			//print_r($input[logo]);
			if (!($input[logo][type]=="image/pjpeg" || $input[logo][type]=="image/jpeg" || $input[logo][type]=="image/gif")) {
				echo "<!-- ".$input[logo][type]." -->";
				return "INVALIDFILE";
			}
			
			$file = $input[logo][name];
			$tmp = explode("/",$file);
			$filename = $tmp[count($tmp)-1];
			
			$dest = DIRUSERTHEMES."custom/";
	
			$tmpfile = strtolower($input[logo][tmp_name]);
			shell_exec("cp -af $tmpfile $dest");
			
		}

		
		
		write_ini_file(DIRSET."settings.ini",$custom);
		clearstatcache();
		
		return true;
	}
	
}
?>
