<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/07/2006					*
	*																*
	****************************************************************/

	/******
	 * prefixo de actions: useredit_*
	 */

	
	require_once ("../common.php");

	conv::include_all_fnc();
	
	$fncuser = new user();

	$users = $fncuser->users;
	$plans = $fncuser->plans;
	
	// checando se jah tem o num maximo de usuario
	if (!$_GET[editid]) {
		if ($maxusers=get_licensevar("maxusers")) {
			if (count($users)>=$maxusers) {
				Act::html_redirect("user.php?alertlic=1");
			}
		}
	}
	
	$page = new Page(_("Customer"));
	$page->open();
	
	for($i = 0; $i < count($users); $i++){
		if ($_GET[editid]==$users[$i][login]) {
			$edituser = $users[$i];
		}
	}
	
	$b = new Frameback("/control/user.php");
	$b->draw();
	
	if ($status = $fncuser->getstatus($edituser[login])) {
		$frame2 = new Framebutton ("DISCONNECTUSER_".$edituser[login]);
		$frame2->title = sprintf(_("Online now since %s on link %s"),conv::formatdate($status[time]),$status["int"]);
		$frame2->help = _("Click Disconnect to force user to disconnect and prevent his future login");
		$frame2->logtitle = _("Disconnecting User");
		$frame2->buttontext = _("Disconnect");
		$frame2->draw();
	}
	
	$help_details=_("Enter details like telefone number \nor contract number");
	$help_login=_("Username that allow user to login");
	$help_pass=_("Enter Password");
	$help_plan=_("Select Plan that affects this user");
	
	# Agora o e form Frame do Edit (vou aproveitar o do New acima)

	# Frame 3
	$frame3 = new Frame ("useredit");
	if ($edituser) {
		$frame3->title=_("Edit User");
	} else {
		$frame3->title=_("New User");
	}
	$frame3->startminimized="no";
	
	# Formul�rio
	$form2 = new Form ("useredit");
	# Campo 1
	if ($edituser) {
		$form2->itype="hidden";
		$form2->iname="edituser";
		$form2->ivalue="yes";
		$form2->nextitem();
		
		$form2->itype="label";
	} else {
		$form2->itype="hidden";
		$form2->iname="newuser";
		$form2->ivalue="yes";
		$form2->nextitem();
		
		$form2->itype="textbox";
	}
	$form2->iname="login";
	$form2->ilabel=_("Login");
	$form2->ihelp=$help_login;
	$form2->ivalue=$edituser[login];
	$form2->nextitem();
	
	//if ($edituser[disabled]==1)	$status = "disable"; else $status = "enable";
	$form2->itype="list";
	$form2->iname="status";
	$form2->ilabel=_("Status");
	$form2->ihelp=_("Disable or Enable this user");
	$form2->ivalue=$edituser[disabled];
	$form2->ivalues[0]=_("Enabled");
	$form2->ivalues[1]=_("Disabled");
	//$form2->ivalues[2]=_("Disabled: payment");
	$form2->ivalues[2]=_("Disabled: custom 1");
	$form2->ivalues[3]=_("Disabled: custom 2");
	$form2->ivalues[4]=_("Disabled: custom 3");
	$form2->nextitem();

	# Campo 2
	$form2->itype="textarea";
	$form2->iname="details";
	$form2->ilabel=_("Details");
	$form2->ihelp=$help_details;
	$form2->ivalue=$edituser[details];
	$form2->nextitem();
	# Campo 3
	$form2->itype="textbox";
	$form2->iname="pass";
	$form2->imaxlength=10;
	$form2->ilabel=_("Password");
	$form2->ihelp=$help_pass;
	$form2->ivalue=$edituser[pass];
	$form2->nextitem();
	# Campo 4
	$form2->itype="list";
	$form2->iname="plan";
	$form2->ilabel=_("Plan");
	$form2->ivalue=$edituser[plan];
	foreach($plans as $p)
		$form2->ivalues[$p['id']]=_($p['name']);
	$form2->ihelp=$help_plan;
	$form2->nextitem();

	$form2->itype="textarea";
	$form2->iname="msg";
	$form2->ilabel=_("Message to Client");
	$form2->ihelp=_("Message shown on next login");
	$form2->ivalue=$edituser[msg];
	$form2->nextitem();

	$form2->itype="textbox";
	$form2->iname="ip";
	$form2->ilabel=_("IP Address");
	$form2->ihelp=_("Obtained IP from the client first login");
	$form2->ivalue=$edituser[ip];
	$form2->nextitem();

	$form2->itype="textbox";
	$form2->iname="macs";
	$form2->ilabel=_("MAC Addresses");
	$form2->ihelp=_("Obtained MAC from the client first login");
	$form2->ivalue=$edituser[macs];
	$form2->nextitem();


		

	# Monta
	$frame3->draw($form2);
		
	
	$page->close();
	

?>
