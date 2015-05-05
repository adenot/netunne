<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 20/06/2006					*
	*																*
	****************************************************************/
	
	/******
	 * prefixo de actions: useredit_*
	 */
	
	require_once ("../common.php");

		
	conv::include_all_fnc();
	
	$page = new Page(_("User Management"));
	$page->open();

	
	$fncuser = new user();

	$users = $fncuser->users;
	$plans = $fncuser->plans;
	


	$frame2 = new Framebutton ("useredit_do_new");
	$frame2->title = _("Create New User");
	$frame2->help = _("Go to new user form");
	//$frame2->logtitle = _("Request License Log");
	$frame2->action = "useredit_do_new";
	$frame2->buttontext = _("Create");
	$frame2->draw();
	
	$data = $fncuser->getusertable();

	# Frame 1
	$frame = new Frame ("userlist");
	$frame->title=_("User List");
	# Tabela
	$table = new Table ("userlist");
	$table->data = $data;
	$table->orderby = "ASC 1";
	# Tamanhos das colunas
	$table->size[0]=1;
	$table->size[1]=19;
	$table->size[2]=42;
	$table->size[3]=18;
	$table->size[4]=20;
	$table->linkid=1;
	
	# A��es dos bot�es
	$actions[]=array("graph",_("View Graphs"),"useredit_do_graph");
	$actions[]=array("edit",_("Edit"),"useredit_do_edit");
	$actions[]=array("disconnect",_("Disconnect"),"useredit_do_disconnect");
	$actions[]=array("remove",_("Remove"),"useredit_do_remove");
	$actions[]=array("enable",_("Enable"),"useredit_do_enable",1);
	$table->actions=$actions;
	
	$table->multiactions=array(2,3,4);
	
	# Monta
	$frame->draw($table);
	
	/* JAH TAH OK, FALTA SOH O IMPORTAR
	$frame2 = new Framebutton ("user_export");
	$frame2->title = _("Export Client List");
	$frame2->help = _("Export in CSV (comma-separated values) format");
	$frame2->action = "user_export";
	$frame2->buttontext = _("Export CSV");
	$frame2->draw();
	*/
	

	$page->close();
	
?>
