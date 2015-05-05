<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 28/06/2006					*
	*																*
	****************************************************************/

include "../common.php";

$actions = record::act_log();

if (trim($actions)=="")
	$actions=_("No changes made since last apply");
else {
	foreach (explode("\n",$actions) as $k=>$v) { 
		if (trim($v)=="") { continue; }
		$tmp[$k] = "- $v<BR>";
	}	
	$actions = implode("\n",$tmp);
}



header('Content-Type: text/html; charset=ISO-8859-1');

include_once DIRHTML."html_apply.php";


?>
