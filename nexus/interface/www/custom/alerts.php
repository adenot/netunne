<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 20, 2006					*
	*																*
	****************************************************************/
	
	require_once ("../common.php");
	
	$page = new Page(_("Customer Alerts"));
	$page->open();
	
	$custom = @parse_ini_file(DIRSET."settings.ini",1);
	$custom = $custom[custom];
	if (is_array($custom)) {
		foreach ($custom as $k=>$v) {
			$custom[$k]=stripslashes($v);
		}
	}
	
	
	$fr = new Frame ("customalerts");
	$fr->title=_("Login Page Alerts");
	
	$fm = new Form ("customlogin");
	
	if (trim($custom[limit])!="") {
		$fm->itype="textarea";
		$fm->iname="limit";
		$fm->ilabel=_("Quota Limit Reached");
		$fm->ihelp=_("Alert when customer reach <BR>his plan quota limit");
		$fm->ivalue=$custom[limit];
		$fm->nextitem();
	}

/*
	$fm->itype="textarea";
	$fm->iname="dispayment";
	$fm->ilabel=_("Disabled: payment");
	$fm->ihelp=_("Alert when customer is disabled<BR>due lack of payment");
	$fm->ivalue=$custom[dispayment];
	$fm->nextitem();
	*/
	
	$fm->itype="textarea";
	$fm->iname="proxydenied";
	$fm->ilabel=_("Site blocked message");
	$fm->ihelp=_("Message displayed when a site is blocked to the user");
	$fm->ivalue=$custom[proxydenied];
	$fm->nextitem();
	
	$fm->itype="textarea";
	$fm->iname="discustom1";
	$fm->ilabel=_("Disabled: custom 1");
	$fm->ihelp=_("Customized Alert #1");
	$fm->ivalue=$custom[discustom1];
	$fm->nextitem();
	
	$fm->itype="textarea";
	$fm->iname="discustom2";
	$fm->ilabel=_("Disabled: custom 2");
	$fm->ihelp=_("Customized Alert #2");
	$fm->ivalue=$custom[discustom2];
	$fm->nextitem();

	$fm->itype="textarea";
	$fm->iname="discustom3";
	$fm->ilabel=_("Disabled: custom 3");
	$fm->ihelp=_("Customized Alert #3.");
	$fm->ivalue=$custom[discustom3];
	$fm->nextitem();


	$fm->itype="textarea";
	$fm->iname="changemac";
	$fm->ilabel=_("MAC Changed");
	$fm->ihelp=_("User try to login with different MAC and his plan forbids");
	$fm->ivalue=$custom[changemac];
	$fm->nextitem();

	$fr->draw($fm);
	
	
	$page->close();
?>
