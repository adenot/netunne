<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Dez 21, 2006					*
	*																*
	****************************************************************/

	require_once ("../common.php");

	conv::include_all_fnc();
	

	$page = new Page (_("Connection Tracking"));
	$page->open();

	$usersonline = user::getonline();
	
	
	
	
	$page->close();

	
?>