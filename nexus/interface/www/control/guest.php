<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 27/07/2006					*
	*																*
	****************************************************************/
	
	/******
	 * prefixo de actions: guest_* ou guestlist_*
	 * cheat: ?idle=1 / abre a edicao do tempo idle
	 */
	
	require_once ("../common.php");
	
	conv::include_all_fnc();
	
	$fncuser = new user();
	
	$page = new Page(_("Credit"));
	$page->open();

	$conf = new Conf("forward");
	# Lista dos guests
	$guests = xml::normalizeseq($conf->get("forward/guests/guest"));
	
	# Lista dos planos
	$plans = xml::normalizeseq($conf->get("forward/plans/plan"));

	for($i = 0; $i < count($plans); $i++){
		# Organiza os planos por ordem
		$plan[$plans[$i]['id']] = $plans[$i]['name'];
	}
	
	$guestconf = $conf->get("forward/guestconfig");
	
	$keysize = Setting::load("guest","keysize");
	if (is_bool($keysize)&&$keysize==false) {
		$keysize=6;
	}
	
	
	$f1 = new Frame("guestconfig");
	$f1->title = _("Settings");
		$frm1 = new Form ("guestconfig");
		$frm1->itype="list";
		$frm1->iname="plan";
		$frm1->ilabel=_("Default plan");
		$frm1->ivalue=$guestconf[plan];
		foreach($plans as $p)
			$frm1->ivalues[$p['id']]=$p['name'];
		$frm1->ihelp=_("Select the default Plan for new Credits");
		$frm1->nextitem();
		
		$frm1->itype="list";
		$frm1->iname="keysize";
		$frm1->ilabel=_("Key Size");
		$frm1->ivalue=$keysize;
		$frm1->ivalues[6]=_("6 digits");
		$frm1->ivalues[10]=_("10 digits");
		$frm1->ihelp=_("Key size in automatic key generation");
		$frm1->nextitem();
		
		if ($_GET[idle]==1) {
			$frm1->itype="textbox";
			$frm1->iname="idle";
			$frm1->ilabel="Idle Time";
			$frm1->ivalue=$guestconf[idle];
			$frm1->ihelp=_("Time in seconds");
			$frm1->nextitem();
		}
	$f1->draw($frm1);
	
	$frame2 = new Framebutton ("guest_do_new");
	$frame2->title = _("Create New Credit");
	//$frame2->help = _("Go to new form");
	$frame2->action = "guest_do_new";
	$frame2->buttontext = _("Create");
	$frame2->draw();

	# Organiza��o dos dados

	foreach ($guests as $guest) {
		if ($guest[timelimit]==""||$guest[timelimit]==0||!$guest[timelimit]) {
			$guestlimit=_("Unlimited");
		} else {
			$guestlimit = $guest[timelimit] / 60;
		}
		if ($guest[expire]!="") {
			$expire = conv::formatdate($guest[expire]);
		} else {
			$expire = _("No expiration");
		}
		
		$guesttotal = round(forward::guesttotal($guest[key]) / 60);
		
		//$guestlimit = $guest[timelimit];
		
		
		$status=_("valid");
		if ($guest[expire]!=0&&$guest[expire]<time()) {
			$status="";
			$expire = "<span style='color:red;'>".$expire."</span>";
		}
		if (is_int($guestlimit)) {
			if ($guesttotal!=0&&$guesttotal>=$guestlimit) {
				$status="";
				$guesttotal = "<span style='color:red;'>".$guesttotal."</span>";
			} 
			$guestlimit = $guestlimit." min";
		}
		if ($status=="")
			$status = "<span style='color:red;'>"._("expired")."</span>";

		$data[] = array($guest[key],$guestlimit,$guesttotal." min",$expire,$status);
	}

	# Frame 1
	$frame = new Frame ("guestlist");
	$frame->title=_("Credit List");
	# Tabela
	$table = new Table ("guestlist");
	$table->data = $fncuser->getguesttable();
	
	# Tamanhos das colunas
	$table->size[0]=1;
	$table->size[1]=19;
	$table->size[2]=35;
	$table->size[3]=10;
	$table->size[4]=10;
	$table->size[5]=25;
	$table->linkid=1;
	
	# A��es dos bot�es
	$actions[]=array("edit",_("Edit"),"guest_do_edit");
	$actions[]=array("remove",_("Remove"),"guest_do_remove");
	$table->actions=$actions;
	
	$table->multiactions=array(1);
	# Monta
	$frame->draw($table);

	//print_r($guests);

	$page->close();
?>
