<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 23/06/2006					*
	*																*
	****************************************************************/


/*
 * 
 * 
 *  SOH ENTRA AQUI NO CORE, A INTERFACE NAO VE ISSO
 * 
 *  PARA VER EM CORE/INTERFACE, USAR O CORE_COMMON.PHP
 */

set_time_limit(0);
ignore_user_abort(true);


$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');


define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);
define ("DIROUT",NEXUS."/core/out/");
define ("DIRTPL",NEXUS."/core/tpl/");
define ("DIRRUN",NEXUS."/core/manager/run/");
//define ("DIRWALL",NEXUS."/core/manager/msg/");

define ("DIRDATA",NEXUS."/core/data/");
define ("DIRDB",DIRDATA."/db/");
define ("DIRLOG",DIRDATA."/log/");
define ("DIRWALL",DIRDATA."/log/wall/");

//define ("DIRLOGLISTENER",DIRLOG."/log/listener/");

define ("DIRBIN",NEXUS."/core/bin/");
define ("DIRNLIB",NEXUS."/core/nlib/");
define ("DIRCONF",NEXUS."/core/conf/");
define ("DIRSET",DIRCONF);
define ("DIRLOCALE",NEXUS."/core/locale/");

define ("INTERFACENLIB",NEXUS."/interface/nlib/");
define ("INTERFACECONF",NEXUS."/interface/conf/");

define ("DIRTMP",	"/tmp/");

// se precisar saber se estou no core ou nao...
define ("INCORE",1);
$GLOBALS['INCORE']=1;

set_include_path(get_include_path() . PATH_SEPARATOR . DIRNLIB );


include_once DIRNLIB."core_common.nx";

function getpidinfo($pid, $ps_opt=""){

   $ps=shell_exec("ps -".$ps_opt."p ".$pid);
   $ps=explode("\n", $ps);
  
   if(count($ps)<=2){
      //trigger_error("PID ".$pid." doesn't exists", E_USER_WARNING);
		return false;
   } else {
		return true;
   }

   foreach($ps as $key=>$val){
      $ps[$key]=explode(" ", ereg_replace(" +", " ", trim($ps[$key])));
   }

   foreach($ps[0] as $key=>$val){
      $pidinfo[$val] = $ps[1][$key];
      unset($ps[1][$key]);
   }
  
   if(is_array($ps[1])){
      $pidinfo[$val].=" ".implode(" ", $ps[1]);
   }
   return $pidinfo;
}

/****************************************************************
 * __ (msg)
 * realiza a traducao de uma frase
 */
function __($msg) {
	return $msg;
}


function __autoload($class_name) {
	if (class_exists($class_name,false)) { return; }
	
	$class_name = strtolower($class_name);
	$file[]=DIRNLIB."cls_".$class_name.".php";
	$file[]=DIRNLIB."cls_".$class_name.".nx";
	$file[]=INTERFACENLIB."cls_".$class_name.".php";
	$file[]=DIRNLIB."cls_".$class_name.".nx";
	$file[]=DIRNLIB.$class_name.".nx";
	$file[]=DIRNLIB.$class_name.".php";
	$file[]=DIRNLIB."cls.nx";


	
	foreach ($file as $v) {
		if (file_exists($v)) {
			require_once ($v);
			return;
		}
	}
	die ("CLASS NAME: '$class_name' NOT FOUND");
}

/*
function __autoload2($class_name) {

	echo "autoload2..";

	// sempre tento carregar o .nx primeiro
	$class_file =DIRNLIB."cls_".strtolower($class_name).".php";
	$class_file2=DIRNLIB."cls_".strtolower($class_name).".nx";
	if (substr($class_name,0,3)=="cmd") {
		$class_file=DIRNLIB.strtolower($class_name).".php";
		$class_file2=DIRNLIB.strtolower($class_name).".nx";
	}
		
	if (file_exists($class_file2)) {
		require_once $class_file2;
	} else if (file_exists($class_file)) {
		require_once $class_file;
	} else {
		if (file_exists(DIRNLIB."cls.php")) {
			require_once DIRNLIB."cls.php";
		} else if (file_exists(DIRNLIB."cls.nx")) {
			require_once DIRNLIB."cls.nx";
		} else {
			die ("CLASS FILE: '$class_file' NAME: '$class_name' NOT FOUND");
		}
	}		
}
*/

// MD5 CRYPT
   function hex2bin ($str) {

     $len = strlen($str);
     $nstr = "";
     for ($i=0;$i<$len;$i+=2) {
       $num = sscanf(substr($str,$i,2), "%x");
       $nstr.=chr($num[0]);
     }
     return $nstr;
   }

   function to64 ($v, $n) {
   $ITOA64 = "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
     $ret = "";
     while (($n - 1) >= 0) {
       $n--;
       $ret .= $ITOA64[$v & 0x3f];
       $v = $v >> 6;
     }
     
     return $ret;

   }

   function md5crypt ($pw, $salt, $magic="") {

     $MAGICO = "$1$";
     if ($magic == "") $magic = $MAGICO;
     
     $slist = explode("$", $salt);
     if ($slist[0] == "1") $salt = $slist[1];
     $salt = substr($salt, 0, 8);
     
     $ctx = $pw . $magic . $salt;
     
     $final = hex2bin(md5($pw . $salt . $pw));
     
     for ($i=strlen($pw); $i>0; $i-=16) {
       if ($i > 16)
         $ctx .= substr($final,0,16);
       else
         $ctx .= substr($final,0,$i);
     }
     
     $i = strlen($pw);
     while ($i > 0) {
       if ($i & 1) $ctx .= chr(0);
       else $ctx .= $pw[0];
       $i = $i >> 1;
     }
     
     $final = hex2bin(md5($ctx));

//     # this is really stupid and takes too long

     for ($i=0;$i<1000;$i++) {
       $ctx1 = "";
       if ($i & 1) $ctx1 .= $pw;
       else $ctx1 .= substr($final,0,16);
       if ($i % 3) $ctx1 .= $salt;
       if ($i % 7) $ctx1 .= $pw;
       if ($i & 1) $ctx1 .= substr($final,0,16);
       else $ctx1 .= $pw;
       $final = hex2bin(md5($ctx1));
     }
     
     $passwdmd5 = "";
     
     $passwdmd5 .= to64( ( (ord($final[0]) << 16) | (ord($final[6]) << 8) | (ord($final[12])) ), 4);
     $passwdmd5 .= to64( ( (ord($final[1]) << 16) | (ord($final[7]) << 8) | (ord($final[13])) ), 4);
     $passwdmd5 .= to64( ( (ord($final[2]) << 16) | (ord($final[8]) << 8) | (ord($final[14])) ), 4);
     $passwdmd5 .= to64( ( (ord($final[3]) << 16) | (ord($final[9]) << 8) | (ord($final[15])) ), 4);
     $passwdmd5 .= to64( ( (ord($final[4]) << 16) | (ord($final[10]) << 8) | (ord($final[5])) ), 4);
     $passwdmd5 .= to64( ord($final[11]), 2);

     return "$magic$salt\$$passwdmd5";

   }



class conv 		extends master_conv {}
class message 	extends master_message {}
class record 	extends master_record {}
class sysinfo 	extends master_sysinfo {}
class xml 		extends master_xml {}

//} //end if defined(ININTERFACE)



$conf = new Conf("info");
$lang = $conf->get("info/lang");
unset($conf);
if ($lang=="pt_BR") {
	$language = 'pt_BR';
	putenv("LANG=$language");
	setlocale(LC_ALL, $language);
	$domain = 'messages';
	bindtextdomain($domain, DIRLOCALE); 
	textdomain($domain);
}

?>
