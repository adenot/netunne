<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 13/07/2007					*
	*																*
	****************************************************************/

	require_once ("../common.php");

	$page = new Page (_("Proxy HTTP"));
	$page->open();

	$conf = new Conf("proxy");

	$memcache 	= intval($conf->get("proxy/memcache"));
	$cache 		= intval($conf->get("proxy/cache"));
	$maxobject 	= intval($conf->get("proxy/object"));
	$isnew 		= intval($conf->get("proxy/new"));

	if ($isnew) {
		// proxy ainda nao instalado
		$frame = new Framebutton ("installproxy");
		$frame->title = _("Install Proxy Service");
		$frame->help = _("Click to install required packages");
		$frame->logtitle = _("Installing Proxy");
		$frame->draw();
		$page->close();
		exit();
	}
	
	$frame = new Frame("proxy");
	$frame->title = _("Proxy Settings");
	
		$form = new Form ("proxy");
		
		$form->itype="textbox";
		$form->iname="memcache";
		$form->ilabel=_("Memory Cache (MB)");
		$form->ihelp=_("Size of cache allocated in RAM (per link)");
		$form->ivalue=$memcache;
		$form->nextitem();

		$form->itype="textbox";
		$form->iname="cache";
		$form->ilabel=_("Disk Cache (MB) (max 1000MB)");
		$form->ihelp=_("Size of disk cache (per link)");
		$form->ivalue=$cache;
		$form->nextitem();
		
		$form->itype="textbox";
		$form->iname="object";
		$form->ilabel=_("Max Object Size (MB)");
		$form->ihelp=_("Don`t cache objects larger than this size");
		$form->ivalue=$maxobject;
		$form->nextitem();

	$frame->draw($form);
	
	$frame = new Framebutton ("cleanproxy");
	$frame->title = _("Clean Cache");
	$frame->help = _("Click to clean the proxy cache");
	$frame->logtitle = _("Cleaning Cache");
	$frame->draw();
	
	$page->close();

	

?>
