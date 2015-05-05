<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 2, 2006					*
	*																*
	****************************************************************/

	include "common.nx";
	
	/*
	$pdata =new pdata("sqlite:/NEXUS/nexus/core/data/db/log.db");
	$a = new datalog($pdata);
	print_r($a->select());

	
	*/
	
	$datalog = new datalog();
	$datalog->insert("log_in","spike");

?>
