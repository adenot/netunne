<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 05/10/2006					*
	*																*
	****************************************************************/


	require_once ("../common.php");
	
	conv::include_all_fnc();
	
	$page = new Page(_("Plans"));
	$page->open();
	
	$fplan = new Frame ("newplan");
	$fplan->title=_("New Plan");
	$fplan->buttontext=_("Continue");
	
		$formplan = new Form("editplan");
		$formplan->itype="textbox";
		$formplan->iname="name";
		$formplan->ilabel=_("Plan Name");
		$formplan->ihelp=_("Enter a name for a new plan");
		$formplan->nextitem();

	$fplan->draw($formplan);
	

	$frame = new Frame ("planlist");
	$frame->title=_("Plans");

		$table = new Table ("planlist");
		$table->data = plan::getplans();
	
		$table->size[0]=20;
		$table->size[1]=80;

		$table->linkid=0;

		$actions[]=array("edit",_("Edit"),"plan_do_edit");
		$actions[]=array("remove",_("Remove"),"plan_do_remove");
		$table->actions=$actions;
	
		$table->multiactions=array(1);

	$frame->draw($table);
	
	 
	
	
	$page->close();
?>
