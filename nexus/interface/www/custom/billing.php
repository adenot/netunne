<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 20, 2006					*
	*																*
	****************************************************************/
	
	require_once ("../common.php");
	
	$page = new Page(_("Limits"));
	$page->open();
	
	$conf = new Conf("forward");
	$billconf = $conf->get("forward/billing");
	
	for ($i=1;$i<=28;$i++)
		$days[$i]=$i;

	$f1 = new Frame("billingday");
	$f1->title = _("Montly Reset");
		$frm1 = new Form ("billingday");
		$frm1->itype="list";
		$frm1->iname="day";
		$frm1->ilabel=_("Day");
		$frm1->ivalue=$billconf[day];
		$frm1->ivalues=$days;
		$frm1->ihelp="";
		$frm1->nextitem();

	$f1->draw($frm1);
	
	
	
	
	
	
	
	
	$page->close();
?>
