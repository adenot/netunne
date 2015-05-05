<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 07/06/2006					*
	*																*
	****************************************************************/

include "../common.php";

//print_r($_GET);exit();

$login = new Login();
if (!$login->autoauth())
	exit();

$act = new Act();
if ($_GET["id"])
	$act->input[id]=$_GET["id"];

$act->refer=$_GET["refer"];

$act->execute($_GET["action"]);

?>
