<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 6, 2006					*
	*																*
	****************************************************************/

	require_once ("../common.php");

	include "fnc_server.php";

	$page = new Page (_("Server Info"));
	$page->open();
		
	$err = server::getevents();
	
	if (is_bool($err)&&$err==false) {

		$f = new Framebutton ();
		$f->title = _("No alerts");
		$f->icon = "ok";
		$f->draw();
		
	} else {
		
		$fb = new Framebutton("cleanalerts");
		$fb->title=_("Clean Alerts");
		$fb->help = _("Delete all alerts above.");
		$fb->action = "cleanalerts";
		$fb->buttontext = _("Clean");
		$fb->draw();
		
		
		//print_r($err3);
		$f = new Framelist("alerts");
		$f->name = "alerts";
		$f->title = _("Last Alerts");
		$f->autorefresh = 1;
		$f->datafrom = "server::getevents";
		$f->data = $err;
		$f->draw();
		

	}
	
	$page->close();

?>
