<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 01/08/2006					*
	*																*
	****************************************************************/


	/******
	 * prefixo de actions: guestedit_*
	 */

	
	require_once ("../common.php");
	
	conv::include_all_fnc();
	
	$page = new Page(_("Credit"));
	$page->open();

	$fncuser = new user();

	$users = $fncuser->users;
	$plans = $fncuser->plans;
	$guests = $fncuser->guests;
	
	// checando se jah tem o num maximo de guests
	/* NAO CHECO AQUI, JAH Q O MAXGUESTS EH POR MAXIMO LOGADOS SIMULTANEOS
	if (!$_GET[editid]) {
		if ($maxguests=get_licensevar("maxguests")) {
			if (count($guests)>=$maxguests) {	
				Act::html_redirect("guest.php?alertlic=1");
			}
		}
	}
	*/
	
	for($i = 0; $i < count($guests); $i++){
		if ($_GET[editid]==$guests[$i][key]) {
			$editguest = $guests[$i];
		}
	}
	
	$b = new Frameback("/control/guest.php");
	$b->draw();

	# Frame 3
	$frame3 = new Frame ("guestedit");
	if ($editguest) {
		$frame3->title=_("Edit Credit");
	} else {
		$frame3->title=_("New Credit");
	}
	$frame3->startminimized="no";
	

	# Formul�rio
	$form2 = new Form ("guestedit");
	# Campo 1
	if ($editguest) {
		$form2->itype="hidden";
		$form2->iname="editguest";
		$form2->ivalue="yes";
		$form2->nextitem();
		
		$form2->itype="label";
	} else {
		$form2->itype="hidden";
		$form2->iname="newguest";
		$form2->ivalue="yes";
		$form2->nextitem();
		
		$form2->itype="keybox";
	}
	$form2->iname="key";
	$form2->ilabel=_("Key");
	$form2->ihelp=_("This key allows the guest to access the internet");
	$form2->ivalue=$editguest[key];
	$form2->nextitem();

	$form2->itype="textbox";
	$form2->iname="description";
	$form2->ilabel=_("Description");
	$form2->ihelp=_("Optional description for this key");
	$form2->ivalue=$editguest[description];
	$form2->nextitem();	
	
	$form2->itype="textbox";
	$form2->iname="timelimit";
	$form2->ilabel=_("Total Minutes");
	$form2->ihelp=_("Total minutes this guest can use\nLeave blank to unlimited");
	$form2->ivalue=round($editguest[timelimit] / 60);
	$form2->nextitem();	

	if ($editguest) {
		$form2->itype="label";
		$form2->ilabel=_("Expiration");
		$form2->iname="old_expire";
		if (trim($editguest[expire])!="") 
			$form2->ivalue=conv::formatdate($editguest[expire]);
		else 
			$form2->ivalue=_("No expiration");
		
		$form2->ihelp=_("Current expiration for this key");
		$form2->nextitem();	
	}
	$form2->itype="datefromnow";
	$form2->iname="expire";
	if ($editguest) {
		$form2->ilabel=_("New Expiration");
		$form2->ihelp=_("This key will work until this date\nLeave blank to no keep current expiration");
	} else {
		$form2->ilabel=_("Expiration");
		$form2->ihelp=_("This key will work until this date\nLeave blank to no expiration");
	}
	$form2->nextitem();	


	$form2->itype="list";
	$form2->iname="plan";
	$form2->ilabel=_("Plan");
	$form2->ivalue=$edituser[plan];
	foreach($plans as $p)
		$form2->ivalues[$p['id']]=_($p['name']);
	$form2->ihelp=_("Select Plan that affects this credit");;
	$form2->nextitem();
	

	# Monta
	$frame3->draw($form2);
	
	$page->close();
		

?>
