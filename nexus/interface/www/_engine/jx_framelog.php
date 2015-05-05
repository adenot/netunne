<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 28/06/2006					*
	*																*
	****************************************************************/

include "../common.php";

header('Content-Type: text/html; charset=ISO-8859-1');

$logtitle = $_POST["logtitle"];
$action = urlencode($_POST["action"]);
if ($_POST["details"]==1) {
	$details=1;
}

$a = new Act();
$a->execute($action);

$wall = $a->actident;
//echo $wall;


//header('Content-Type: text/html; charset=ISO-8859-1');


include_once DIRHTML."html_framelog.php";


?>
