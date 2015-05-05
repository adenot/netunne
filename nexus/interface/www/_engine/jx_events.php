<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 22/09/2006					*
	*																*
	****************************************************************/

	include "../common.php";
	conv::include_all_fnc();
	
	$events = server::getevents(1);
	
	
	header('Content-Type: text/html; charset=ISO-8859-1');

	echo nl2br(htmlentities($events));
?>
