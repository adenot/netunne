<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 26/05/2006					*
	*																*
	****************************************************************/
	
	

//if (!$GLOBALS['INCORE']==1) {

$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');



define ("NEXUS",	$GLOBALS[CONF]['NEXUS']);
define ("CORE",		NEXUS."/core/");
define ("DIRWWW",	NEXUS."/interface/www/");
define ("DIRHTML",	NEXUS."/interface/nlib/");
define ("DIRNLIB",	NEXUS."/interface/nlib/");
define ("DIRSET",	NEXUS."/interface/conf/");
define ("DIRSETPROXY",DIRSET."/proxy/");
define ("DIRAUTH",  NEXUS."/interface/userauth/");
define ("DIRDATA",	CORE."/data/");
define ("DIRGRAPH", DIRDATA."/graph/");
define ("DIRWALL",	DIRDATA."log/wall/");
define ("DIRLOG",	DIRDATA."log/");
define ("DIRDB",	DIRDATA."/db/");
define ("DIRUSERTHEMES",	DIRDATA."/userthemes/");
define ("DIRTHEMES",		DIRAUTH."/theme/");
define ("DIRLOCALE",		CORE."/locale/");
define ("DIRLOCALEINTERFACE",	NEXUS."/interface/locale/");

define ("DIRTPL",NEXUS."/core/tpl/");

define ("WWWGRAPH",	"/graph/");


define ("CORENLIB",	CORE."/nlib/");

define ("DIRTMP",	"/tmp/");

// nao eh erro de digitacao, eh in-interface mesmo
define ("ININTERFACE",1);
$GLOBALS['ININTERFACE'] = 1;

set_include_path(get_include_path() . PATH_SEPARATOR . DIRNLIB );


require_once DIRWWW."common.php";

// vou puxar do core
define ("DIRCONF",NEXUS."/core/conf/");

define ("XMLLIST","core;dhcp;forward;info;listener;network;objects-static;objects;proxy");

function __autoload($class_name0) {
/*
	$class_name = "cls_".strtolower($class_name0);
	$file[]=DIRNLIB.$class_name.".php";
	$file[]=DIRNLIB.$class_name.".nx";
	$file[]=CORENLIB.$class_name.".php";
	$file[]=CORENLIB.$class_name.".nx";
	$file[]=CORENLIB."cls.nx";
*/

	// casos especiais
	if ($class_name0=="datalog") {
		$class_name0="pdata";
	}


	$class_name = strtolower($class_name0);
	$file[]=CORENLIB."cls_".$class_name.".php";
	$file[]=CORENLIB."cls_".$class_name.".nx";
	$file[]=DIRNLIB."cls_".$class_name.".php";
	$file[]=CORENLIB."cls_".$class_name.".nx";
	$file[]=CORENLIB.$class_name.".nx";
	$file[]=CORENLIB.$class_name.".php";
	$file[]=CORENLIB."cls.nx";	
	

	foreach ($file as $v) {
		if (file_exists($v)) {
			require_once ($v);
			return;
		}
	}
	die ("CLASS NAME: '$class_name' NOT FOUND");
}

require_once CORENLIB."core_common.nx";


function reorganize($data, $col = 0, $order = "ASC") {
	for ($i = 0; $i < count($data); $i ++) {
		$tmp[$i] = $data[$i][$col];
	}
	if ($order == "ASC") {
		asort($tmp);
	} else {
		arsort($tmp);
	}
	//$newdata[0] = $data[0];
	foreach ($tmp as $k => $v) {
		$newdata[] = $data[$k];
		//echo $k;
	}
	return ($newdata);
}

function croptext ($text,$length) {
	if (strlen($text)>$length) 
		return substr($text,0,$length)."...";
	return $text;
}

/***
 * get_licensevar (var)
 * 	eh soh um wrapper pra classe checklicense
 *  quando for 100% cliente servidor, vai ter q ser diferente
 */
function get_licensevar($var) {
	$lic = new Checklicense();
	return $lic->checkout($var);
}

function append_url ($url,$vars) {
	$tmp = explode("?",$url);
	if (count($tmp)==1) {
		return $url."?".$vars;
	} else {
		return $url."&".$vars;
	}	
}
function clean_url ($url) {
	$tmp = explode("?",$url);
	return $tmp[0];
}

class conv 		extends master_conv {
	function include_all_fnc() {
		include_once DIRWWW."network/fnc_network.php";
		include_once DIRWWW."setup/fnc_server.php";
		include_once DIRWWW."setup/fnc_update.php";
		include_once DIRWWW."control/fnc_plan.php";
		include_once DIRWWW."control/fnc_user.php";
		include_once DIRWWW."setup/fnc_backup.php";
		include_once DIRWWW."custom/fnc_custom.php";
	}
		
	function id_browser() {

		$browser=$GLOBALS['__SERVER']['HTTP_USER_AGENT'];

		if(ereg('Opera(/| )([0-9].[0-9]{1,2})', $browser)) {
			return 'OPERA';
		} else if(ereg('MSIE ([0-9].[0-9]{1,2})', $browser)) {
			return 'IE';
		} else if(ereg('OmniWeb/([0-9].[0-9]{1,2})', $browser)) {
			return 'OMNIWEB';
		} else if(ereg('(Konqueror/)(.*)', $browser)) {
			return 'KONQUEROR';
		} else if(ereg('Mozilla/([0-9].[0-9]{1,2})', $browser)) {
			return 'MOZILLA';
		} else {
			return 'OTHER';
		}

	}
	
	function download ($file,$newfile="") {
		$browser	= conv::id_browser();

		$abs_item = $file;
	
		if ($newfile=="")
			$item = basename($file);
		else
			$item = $newfile;
	
		
		header('Content-Type: '.(($browser=='IE' || $browser=='OPERA')?'application/octetstream':'application/octet-stream'));
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($abs_item));

		
		if($browser=='IE' || $browser=='OTHER') {
			header('Content-Disposition: attachment; filename="'.$item.'"');
			header('Accept-Charset: UTF-8');
			header('Cache-Control:');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="'.$item.'"');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
		}

		readfile($abs_item);
		
	}
		
}

class message 	extends master_message {
	
}

class record 	extends master_record {
	/********
	 * log de acoes realizadas ateh o ultimo merge
	 * o usuario ver� antes de aplicar as configs
	 */
	function act_log ($msg="") {
		if ($msg=="")
			return @file_get_contents(DIRLOG."act.log");
		
		$date = date("M d H:i:s");
		//shell_exec("echo \"$date $domain $msg\" >> ".DIRLOG."actions.log");	
		file_put_contents(DIRLOG."act.log","$msg\n",FILE_APPEND);
	}
	/********
	 * limpa os acts, sempre antes de um merge
	 */
	function clean_act_log () {
		file_put_contents(DIRLOG."act.log","");
	}
}

class sysinfo 	extends master_sysinfo {}

class xml 		extends master_xml {}

//} // end if INCORE



$conf = new Conf("info");
$lang = $conf->get("info/lang");

if ($lang=="pt_BR") {
	$language = 'pt_BR';
	putenv("LANG=$language");
	setlocale(LC_ALL, $language);
	$domain = 'messages';
	bindtextdomain($domain, DIRLOCALE); 
	textdomain($domain);
}

?>
