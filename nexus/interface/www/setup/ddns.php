<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 1, 2006					*
	*																*
	****************************************************************/

	require_once ("../common.php");

	$conf = new Conf("network");
	$login 		= trim($conf->get("network/ddclient/login"));
	$password  	= trim($conf->get("network/ddclient/password"));
	$domains  	= trim($conf->get("network/ddclient/domains"));
	

	
	$page = new Page (_("Dynamic DNS"));
	$page->open();

	if ($login && $password && $domains) {
		$frame2 = new Framebutton ("ddclientdisable");
		$frame2->title = _("Disable DDNS");
		$frame2->help = _("Click to disable the DDNS service");
		$frame2->action = "ddclientdisable";
		$frame2->buttontext = _("Disable");
		$frame2->draw();
	}
	
	$frame = new Frame ("ddclient");
	$frame->title = "DDNS Config";
	
	$form = new Form("ddclient");
	
	$form->itype="label";
	$form->ilabel=_("Service Provider");
	$form->ivalue="www.dyndns.org";
	$form->ihelp=_("Create a DynDNS account");
	$form->nextitem();
	
	$form->itype="textbox";
	$form->iname="login";
	$form->ilabel=_("User");
	$form->ihelp=_("Username of your DynDNS account");
	$form->ivalue=$login;
	$form->nextitem();
	
	$form->itype="textbox";
	$form->iname="password";
	$form->ilabel=_("Password");
	$form->ihelp=_("Password of your DynDNS account");
	$form->ivalue=$password;
	$form->nextitem();

	$form->itype="textbox";
	$form->iname="domains";
	$form->ilabel=_("Domain(s)");
	$form->ihelp=_("Enter one or more domains separated by comma");
	$form->ivalue=$domains;
	$form->nextitem();
	
	$frame->draw($form);
	
	$page->close();
	
?>
