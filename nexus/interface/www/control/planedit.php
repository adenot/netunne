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
	
	$planname = strtoupper($_GET[editid]);
	$urlplanname = urlencode($planname);
	 
	$page = new Page(_("Edit Plan"));
	$page->open();
	
	$b = new Frameback("/control/plan.php");
	$b->draw();
	

	
	$plan = new plan();
	$data = $plan->getplan($planname);
	
	$fplan = new Frame ("editplan");
	$fplan->title=_("Plan Properties");
	
		$formplan = new Form("editplan");
		
		$formplan->itype="hidden";
		$formplan->iname="editid";
		$formplan->ivalue=$data[name];
		$formplan->nextitem();
		
		$formplan->itype="textbox";
		$formplan->iname="name";
		$formplan->ilabel=_("Plan Name");
		$formplan->ihelp=_("Enter a name for this plan");
		$formplan->ivalue=$data[name];
		$formplan->nextitem();
		
			$formplan->itype="textbox";
		$formplan->iname="description";
		$formplan->ilabel=_("Description");
		$formplan->ihelp="";
		$formplan->ivalue=$data[description];
		$formplan->nextitem();
		
		$formplan->itype="textbox";
		$formplan->iname="download";
		$formplan->ilabel=_("Max Download Speed (Kbps)");
		$formplan->ihelp=_("Leave blank to unlimited speed");
		$formplan->ivalue=$data[download];
		$formplan->nextitem();
		
		$formplan->itype="textbox";
		$formplan->iname="upload";
		$formplan->ilabel=_("Max Upload Speed (Kbps)");
		$formplan->ihelp=_("Leave blank to unlimited speed");
		$formplan->ivalue=$data[upload];
		$formplan->nextitem();
		
		// se jah tinha colocado, vou deixar aparcer
		if (intval($data[downlimit])!=0) {
			$formplan->itype="textbox";
			$formplan->iname="downlimit";
			$formplan->ilabel=_("Monthly Download Quota (MB)");
			$formplan->ihelp=_("Leave blank or zero to unlimited");
			$formplan->ivalue=$data[downlimit];
			$formplan->nextitem();
		}
		
		if (intval($data[uplimit])!=0) {
			$formplan->itype="textbox";
			$formplan->iname="uplimit";
			$formplan->ilabel=_("Monthly Upload Quota (MB)");
			$formplan->ihelp=_("Leave blank or zero to unlimited");
			$formplan->ivalue=$data[uplimit];
			$formplan->nextitem();
		}
		
		$formplan->itype="list";
		$formplan->iname="pppoe";
		$formplan->ilabel=_("Connection Type");
		$formplan->ihelp="";
		$formplan->ivalue=$data[pppoe];
		$formplan->ivalues[0]=_("Normal (dhcp or static ip)");
		$formplan->ivalues[1]=_("PPPoE");
		$formplan->nextitem();

		$formplan->itype="list";
		$formplan->iname="forceauth";
		$formplan->ilabel=_("Force authentication");
		$formplan->ihelp=_("Yes to login every time and <BR>No to login only when IP or MAC has changed");
		$formplan->ivalue=$data[forceauth];
		$formplan->ivalues[1]=_("Yes: login every time");
		$formplan->ivalues[0]=_("No: Only on change of IP or MAC");
		$formplan->nextitem();


		$proxyconf 	= new Conf("proxy");
		$isnew 	 	= intval($proxyconf->get("proxy/new"));
		
		if (!$isnew) {
			$formplan->itype="list";
			$formplan->iname="proxy";
			$formplan->ilabel=_("Use Proxy");
			$formplan->ihelp=_("Yes to force a transparent proxy on port 80 to the user");
			$formplan->ivalue=$data[proxy];
			$formplan->ivalues[1]=_("Yes");
			$formplan->ivalues[0]=_("No");
			$formplan->nextitem();
		}
		unset($proxyconf);

		$formplan->itype="list";
		$formplan->iname="fixmac";
		$formplan->ilabel=_("Allow MAC/IP Change");
		$formplan->ihelp=_("Yes to disallow user to login from <BR>a different MAC than initially fixed");
		$formplan->ivalue=$data[fixmac];
		$formplan->ivalues[0]=_("Yes: User can change MAC");
		$formplan->ivalues[1]=_("No: User cannot change MAC");
		$formplan->ivalues[2]=_("No: User cannot change MAC neither IP");
		$formplan->nextitem();

			$ns = new networksetup();
			$externals1 = array();
			$externals1 = $ns->getgateways();
			$externals0[auto] = _("Automatically Chosen");
			$externals = array_merge($externals0,$externals1);

		if (!$data[link]) 
			$data[link] = "auto";

		$formplan->itype="list";
		$formplan->iname="link";
		$formplan->ilabel=_("Force link");
		$formplan->ihelp=_("Always use this link");
		$formplan->ivalue=$data[link];
		$formplan->ivalues=$externals;
		$formplan->nextitem();

		$formplan->itype="list";
		$formplan->iname="linkfail";
		$formplan->ilabel=_("On link failure");
		$formplan->ihelp=_("If the chosen link fails, reconnect users <BR>in another link or keep them disconnected?");
		$formplan->ivalue=$data[linkfail];
		$formplan->ivalues[0]=_("Connect users in other active link");
		$formplan->ivalues[1]=_("Disconnect users");
		$formplan->nextitem();

		$formplan->sh[]="link=auto:linkfail.hide";
		$formplan->sh[]="link=!auto:linkfail.show";
		
		$formplan->sh[]="pppoe=1:forceauth.hide;fixmac.hide";
		$formplan->sh[]="pppoe=0:forceauth.show;fixmac.show";

	$fplan->draw($formplan);

	
	$frame = new Frame ("acldroplist");
	$frame->title=_("Block Rules");
	$frame->action="aclblock_do_edit_".$urlplanname;
	$frame->buttontext = _("New");

		$table = new Table ("acldroplist");
		$table->data = $data[dropdata];
	
		$table->size[0]=20;
		$table->size[1]=40;
		$table->size[2]=40;
		$table->linkid=0;

		$actions[0]=array("edit",_("Edit"),"aclblock_do_edit_".$urlplanname);
		$actions[1]=array("remove",_("Remove"),"acl_remove_".$urlplanname);
		$table->actions=$actions;
	
		$table->multiactions=array(1);

	$frame->draw($table);

	$frame = new Frame ("aclbandlist");
	$frame->title=_("Speed Limit Rules");
	$frame->action="aclband_do_edit_".$urlplanname;
	$frame->buttontext = _("New");

		$table = new Table ("aclbandlist");
		$table->data = $data[banddata];
	
		$table->size[0]=20;
		$table->size[1]=40;
		$table->size[2]=40;
		$table->linkid=0;

		$actions[0]=array("edit",_("Edit"),"aclband_do_edit_".$urlplanname);
		$actions[1]=array("remove",_("Remove"),"acl_remove_".$urlplanname);
		$table->actions=$actions;
	
		$table->multiactions=array(1);

	$frame->draw($table);


	$page->close();
?>
