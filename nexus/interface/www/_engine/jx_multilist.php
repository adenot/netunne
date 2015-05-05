<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 31/05/2006					*
	*																*
	****************************************************************/

include "../common.php";


header('Content-Type: text/html; charset=ISO-8859-1');


if ($_POST[func]=="add") {
	// variaveis recebidas:
	// name
	// value
	// label
	$name = $_POST[name];
	$value= $_POST[value];
	$label= $_POST[label];
	$mark = $_POST[mark];
	
	$showdivonly=1;
	include DIRHTML."html_form_multilist.php";
}


?>
