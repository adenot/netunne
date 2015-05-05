<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 03/07/2006					*
	*																*
	****************************************************************/


include "../common.php";

//$logtext = nl2br(file_get_contents($_GET[logfile]));

$wall=$_GET[wall];
$action=$_GET[action];

//echo $wall;

include_once DIRHTML."html_if_userlog.php";


?>
