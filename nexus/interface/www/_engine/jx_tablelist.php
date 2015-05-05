<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 6, 2006					*
	*																*
	****************************************************************/

include "../common.php";

$name	= $_POST["name"];

$autorefresh = $_SESSION["table_$name"][autorefresh];

if ($_SESSION["table_$name"][datafrom]) {
	$data = $_SESSION["table_$name"][datafrom];
	
	conv::include_all_fnc();
	eval("\$data = $data();");
} else {
	$data = $_SESSION["table_$name"][data];
}

header('Content-Type: text/html; charset=ISO-8859-1');

include DIRHTML."html_tablelist_content.php";



?>
