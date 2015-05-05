<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 02/07/2006					*
	*																*
	****************************************************************/

require_once ("../common.php");

include "fnc_update.php";

$page = new Page(_("Update Center"));
$page->open();

$version = Npak::getversion();

$frameversion = new Framebutton ();
$frameversion->title = sprintf(_("%s %s %s Update %s"),PRODNAME,PRODCLASS,VERSION,$version);
$frameversion->icon = "info";
$frameversion->draw();


$fupdate = new Frame ("manualupdate");
$fupdate->title=_("Manual Update");
$fupdate->buttontext=_("Install");
	$formupdate = new Form("manualupdate");
	$formupdate->itype="filebox";
	$formupdate->iname="file";
	$formupdate->ilabel=_("Upload from file");
	$formupdate->ihelp=_("Select .npak file to update");
	$formupdate->nextitem();

$fupdate->draw($formupdate);




$page->close();

?>
