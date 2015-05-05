<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/07/2007					*
	*																*
	****************************************************************/
	
	require_once ("../common.php");
	
	$page = new Page(_("Customer Login (Advanced)"));
	$page->open();
	
	conv::include_all_fnc();
	
	$frame2 = new Framebutton ("changecustomlogin");
	$frame2->title = _("Change to Basic Mode");
	$frame2->help = _("");
	$frame2->draw();

	$frestore = new Frame ("uploadpage");
	$frestore->title=_("Upload Page");
	$frestore->buttontext=_("Restore");
		$formrestore = new Form("uploadpage");
		$formrestore->itype="filebox";
		$formrestore->iname="logo";
		$formrestore->ilabel=_("Upload from file");
		$formrestore->ihelp=_("Select file to upload (jpg, gif or html)");
		$formrestore->nextitem();
	
	$frestore->draw($formrestore);


	$frame = new Frame ("filelist");
	$frame->title=_("Uploaded Files");

		$table = new Table ("filelist");
		$table->data = customlogin::getuploaded();
		
		$table->size=array(100);
		$table->linkid=0;
		$table->orderby="ASC 0";
		
		$actions[]=array("download",_("Download"),"custom_do_download");
		$actions[]=array("remove",_("Remove"),"custom_do_remove");
		$table->actions=$actions;
		
		//$table->multiactions=array(1);

	$frame->draw($table);
	


	
	
	$page->close();

?>
