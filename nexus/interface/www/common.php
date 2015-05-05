<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/

session_start();

$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');

// paths absolutos
define ("NEXUS",$GLOBALS[CONF]['NEXUS']);
define ("DIRNLIB",NEXUS."/interface/nlib/");
define ("DIRWWW",NEXUS."/interface/www/");
define ("DIRJS2",DIRWWW."/_js/");

// paths relativos
define ("DIRIMG","/_images/");
define ("DIRJS","/_js/");
define ("DIRCSS","/_css/");


include_once DIRNLIB."lib_common.php";



?>
