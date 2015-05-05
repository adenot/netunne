<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 4, 2006					*
	*																*
	****************************************************************/
	
	require_once ("../common.php");


	$page = new Page (_("Administation"));
	$page->open();
	
	
	$conf = new Conf("info");
	$lang = $conf->get("info/lang");
	if (trim($lang)=="") { $lang = "en"; }
	
	$frame2 = new Frame("adminpass");
	$frame2->title = _("Administrator Password");
	
	$form = new Form ("adminpass");
	$form->itype="label";
	$form->iname="user";
	$form->ilabel=_("Login");
	$form->ihelp=_("Admin for web administration <BR>or root in text-mode");
	$form->ivalue=_("Admin / root");
	$form->nextitem();

	$form->itype="textbox";
	$form->iname="oldpassword";
	$form->ilabel=_("Old Password");
	$form->ihelp="";
	$form->nextitem();

	$form->itype="textbox";
	$form->iname="password";
	$form->ilabel=_("New Password");
	$form->ihelp="";
	$form->nextitem();
	
	$frame2->draw($form);
	
	$floc = new Frame("locale");
	$floc->title=_("System Language");
	
		$frloc = new Form ("locale");
		$frloc->iname = "lang";
		$frloc->itype="list";
		$frloc->ilabel=_("Language");
		$frloc->ivalue=$lang;
		$frloc->ivalues=array("pt_BR" => "Portugu&ecirc;s do Brasil", "en" => "English");
		$frloc->ihelp=_("Select system language");
		$frloc->nextitem();
		
	$floc->draw($frloc);
	
	$frame = new Framebutton ("shutdown");
	$frame->title = _("Shutdown");
	$frame->help = sprintf(_("Shutdown %s now"),PRODNAME);
	$frame->logtitle = _("Shutting down");
	$frame->buttontext=_("Execute");
	$frame->action = "shutdown";
	$frame->draw();
	
	$frame = new Framebutton ("restart");
	$frame->title = _("Restart");
	$frame->help = sprintf(_("Restart %s now"),PRODNAME);
	$frame->logtitle = _("Restarting");
	$frame->buttontext=_("Execute");
	$frame->action = "restart";
	$frame->draw();

	$page->close();
?>
