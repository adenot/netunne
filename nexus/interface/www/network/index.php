<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 26/05/2006					*
	*																*
	****************************************************************/
	 
	/***** PREFIXO DE ACTIONS:
	 * networksetup_*
	 */

	require_once ("../common.php");
	
	conv::include_all_fnc();

	$ns = new networksetup();

	$page = new Page(_("Network Setup"));
	$page->open();

	$conf = new Conf("network");
	//print_r($conf->printconf());
	
	$hwcards = $ns->gethardware();
	if ($hwcards) 
		$hwdata0 = $ns->gethardware_noconfig();
	
	$dns = $ns->getdns();


	$fdns = new Frame ("dns");
	$fdns->title=_("DNS");
	$formdns = new Form("dns");
	$formdns->itype="textbox";
	$formdns->iname="dns";
	$formdns->ilabel=_("DNS Server");
	$formdns->ihelp=_("DNS server IP address");
	$formdns->ivalue=$dns;
	$formdns->nextitem();


	//$fdns->draw($formdns);
	
	

	$frame = new Frame ("networkcard");
	$frame->title=_("Network Cards");
	
		$table = new Table("networkcard");
			$carddata[]=array(_("Interface"),"Type",_("IP Address"),_("Hardware"),_("Status"));
	
			$cards = $ns->getcarddata();
			$carddata = conv::arraymerge($carddata,$cards,$hwdata0);
			$table->orderby = "ASC 0";
			$table->data = $carddata;
			$table->size = array(15,15,20,45,5);
			$table->linkid = 0;
			$table->actions[0]=array("graph",_("View Graphs"),"networksetup_do_graph");
			$table->actions[1]=array("edit",_("Edit"),"networksetup_do_edit");

	$frame->draw($table);
	//unset($frame);unset($table);
	
	$primary = $ns->getprimary();
	
	if ($primary!="") {
		$fprimary = new Frame ("primary");
		$fprimary->title=_("Primary Interface");
		$formprimary = new Form("primary");
			$formprimary->itype="list";
			$formprimary->iname="primary";
			$formprimary->ilabel=_("Default Gateway");
			$formprimary->ihelp=_("Define the main gateway for the system");
			$formprimary->ivalue=$primary;
			$formprimary->ivalues=$ns->getgateways();
			$formprimary->nextitem();
	
		$fprimary->draw($formprimary);
	}

/*
	if ($hwcards&&$hwdata0) {
		$frame3 = new Frame ("cardhw");
		$frame3->title=_("Hardware Found");
			$table2 = new Table();
				$hwdata[]=array("Interface","Hardware");
				$table2->data = conv::arraymerge($hwdata,$hwdata0);
				//print_r($table2->data);
				$table2->size = array(20,80);
				$table2->linkid = 0;
				$table2->actions[0]=array("edit",_("edit"),"networksetup_do_new");
	
		$frame3->draw($table2);
	}
*/

	$page->close();

?>