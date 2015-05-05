<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 4, 2006					*
	*																*
	****************************************************************/

	require_once ("../common.php");
	
	include "fnc_network.php";
	
	$page = new Page(_("Connection Check"));
	$page->open();
	
	$fping = new Frame ("ping");
	$fping->buttontext="Ping";
	$fping->title=_("Ping Host");
	$formping = new Form("doping");
	$formping->itype="textbox";
	$formping->iname="host";
	$formping->ilabel=_("Host or IP");
	$formping->ihelp=_("Ping IP address or hostname");	
	$formping->nextitem();
	$fping->draw($formping);

	$ftraceroute = new Frame ("traceroute");
	$ftraceroute->buttontext="Traceroute";
	$ftraceroute->title=_("Trace Route to Host");
	$formtraceroute = new Form("dotraceroute");
	$formtraceroute->itype="textbox";
	$formtraceroute->iname="host";
	$formtraceroute->ilabel=_("Host or IP");
	$formtraceroute->ihelp=_("Trace route to IP address or hostname");	
	$formtraceroute->nextitem();
	$ftraceroute->draw($formtraceroute);
	
	/*
	if (file_exists(DIRTMP."nx_off.tmp")) {
		$intoff = explode(",",trim(file_get_contents(DIRTMP."nx_off.tmp")));
		
		foreach ($intoff as $int) {
			$frame2 = new Framebutton ("enableinterface_".$int);
			$frame2->title = sprintf(_("Interface %s is disabled. Enable?"),$int);
			$frame2->help = _("Interface did not pass in connection check and has been disabled");
			$frame2->logtitle = _("Enabling Interface");
			$frame2->buttontext = _("Enable");
			$frame2->action = "enableinterface_".$int;
			$frame2->draw();
		}
	}
	*/
	
	$conf = new Conf("network");
	$conncheck = $conf->get("network/disable_conncheck");
	
	$frame = new Frame ("conncheck");
	$frame->title = _("Connection Check");
	
	$form = new Form("conncheck");
	
	$form->itype="list";
	$form->iname="conncheck";
	$form->ilabel=_("Disable link if check fails?");
	$form->ihelp=_("Should I disable the link if detected lost of internet connection?");
	$form->ivalue=$conncheck;
	$form->ivalues[0]=_("Yes");
	$form->ivalues[1]=_("No"); // disable_conncheck - desabilitar o conncheck, nao eh desabilitar a interface
	// nao desabilitar link significa desabilitar o conncheck
	// no = 1 
	// yes = 0
	$form->nextitem();
	
	
	$frame->draw($form);
	
	
	$data = networksetup::getconncheck();
	
	if (count($data)!=0) {
		$f = new Framelist();
		$f->name="connproblems";
		$f->title = _("Connection Problems");
		$f->data = $data;
	} else {
		$f = new Framebutton ();
		$f->name="connproblems";
		$f->title = _("No connection errors");
		$f->icon = "ok";
	}
	$f->draw();

	$page->close();

?>
