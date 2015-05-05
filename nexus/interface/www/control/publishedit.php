<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/08/2006					*
	*																*
	****************************************************************/
	
	/**
	 * prefixo: publishedit_
	 */
	 
	 	
	$dport = $_GET[editid];
	 
	require_once ("../common.php");
	
	$page = new Page(_("Publish Server"));
	$page->open();
	
	$conf = new Conf("forward");
	# Lista dos guests
	$pubs = xml::normalizeseq($conf->get("forward/publishs/publish"));
	
	if ($dport)
		foreach ($pubs as $pub)
			if ($pub[dport]==$dport)
				break;
			
	//print_r($pub);
	$b = new Frameback("/control/publish.php");
	$b->draw();


	# Frame 3
	$frame3 = new Frame ("publishedit");
	if ($dport) {
		$frame3->title=_("Edit Publishing");
	} else {
		$frame3->title=_("New Publishing");
	}
	$frame3->startminimized="no";
	
	$form2 = new Form ("publishedit");
	# Campo 1
	if ($dport) {
		$form2->itype="hidden";
		$form2->iname="editpublish";
		$form2->ivalue="yes";
		$form2->nextitem();
		
		$form2->itype="label";
	} else {
		$form2->itype="hidden";
		$form2->iname="newpublish";
		$form2->ivalue="yes";
		$form2->nextitem();
		
		$form2->itype="textbox";
	}
	$form2->iname="dport";
	$form2->ilabel=sprintf(_("%s Port"),PRODNAME);
	$form2->ihelp=sprintf(_("The port listened by %s"),PRODNAME);
	$form2->ivalue=$pub[dport];
	$form2->nextitem();	
	
	$form2->itype="textbox";
	$form2->iname="description";
	$form2->ilabel=_("Description");
	$form2->ihelp=_("Identifier for this publishing");
	$form2->ivalue=$pub[description];
	$form2->nextitem();
	
	$form2->iname="proto";
	$form2->itype="list";
	$form2->ivalues[tcp]="TCP";
	$form2->ivalues[udp]="UDP";
	$form2->ivalues[tcpudp]="TCP+UDP";
	$form2->ivalue=$pub[proto];
	$form2->ilabel=_("Protocol");
	$form2->ihelp=_("Network Protocol");
	$form2->nextitem();
	
	if (conv::startwith("`HOST.USER",$pub[newip])) {
		$tmp = explode(".",str_replace("`","",$pub[newip]));
		$newip=$tmp[2];
	} else {
		$newip=$pub[newip];
	}
	
	$form2->iname="newip";
	$form2->itype="textbox";
	$form2->ilabel=_("Destination Server");
	$form2->ivalue=$newip;
	$form2->ihelp=_("Type a customer login or IP address");
	$form2->nextitem();
	
	$form2->iname="newdport";
	$form2->itype="textbox";
	$form2->ilabel=_("Destination Port");
	$form2->ivalue=$pub[newdport];
	$form2->ihelp=_("Open port in destination server");
	$form2->nextitem();
	
	$frame3->draw($form2);
	

	$page->close();
	
?>
