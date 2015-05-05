<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 20/06/2006					*
	*																*
	****************************************************************/

require_once ("../common.php");

$conf = new Conf("info");

$username = $conf->get("info/user");
$userkey  = $conf->get("info/userkey");

$page = new Page(_("Server Registry"));
$page->open();

if (file_exists("/root/demo")) {
	$frame = new Framebutton ();
	$frame->title = _("This page is disabled in Demo mode");
	$frame->icon = "info";
	$frame->draw();
	
	$page->close();
	exit();
}

$frame = new Frame ("licenseinfo");
$frame->title=_("Server Registry Info");

$form = new Form ("licenseinfo");

$form->itype="textbox";
$form->imask="alpha";
$form->iname="user";
$form->ilabel=_("ID");
$form->ihelp="";
$form->ivalue=$username;
$form->nextitem();

$form->itype="textbox";
$form->iname="userkey";
$form->ilabel=_("Key");
$form->ihelp="";
$form->ivalue=$userkey;
$form->nextitem();

$frame->draw($form);

$frame2 = new Framebutton ("requestlicense");
$frame2->title = _("Activate Registry");
$frame2->help = _("Force a registry request now");
$frame2->logtitle = _("Request registry Log");
$frame2->action = "requestlicense";
$frame2->draw();

/*
$frame3 = new Framebutton ("checklicense");
$frame3->title = _("Check License");
$frame3->help = _("Test if your license is valid");
$frame3->logtitle = _("Check License Log");
$frame3->action = "checklicense";
$frame3->draw();
*/

$desc0=get_licensevar("desc");
$alert0=get_licensevar("alert");
$startdate0=get_licensevar("startdate");
$nick0=get_licensevar("nick");

if (is_string($alert0)) {
	$alert[0][title]=_("Message from Console");
	$alert[0][desc]=urldecode(html_entity_decode($alert0));
	$f = new Framelist("licalert");
	$f->open=1;
	$f->title = _("Alerts");
	$f->data = $alert;
	$f->draw();
}

$i=0;
$desc=array();

//echo "STD:$startdate0";

if (is_string($desc0)) {
	$desc[$i][title]=_("Plan Description");
	$desc[$i][desc]=urldecode(html_entity_decode($desc0));
	$i++;
}
if (intval($startdate0)>1) {
	$desc[$i][title]=_("Created In");
	$desc[$i][desc]=conv::formatdate($startdate0);
	$i++;
}
if (is_string($nick0)) {
	$desc[$i][title]=_("Nickname");
	$desc[$i][desc]=urldecode(html_entity_decode($nick0));
	$i++;
}

if (count($desc)>0) {
	$f = new Framelist("licdesc");
	$f->open=1;
	$f->title = _("Registry Info");

	$f->data = $desc;
	$f->draw();
}



$page->close();

?>
