<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 06/10/2006					*
	*																*
	****************************************************************/

	require_once ("../common.php");

	conv::include_all_fnc();
	
	$planname = strtoupper(urldecode($_GET[editplan]));
	if ($_GET[editid]!="new") {
		$aclid = $_GET[editid];
	}

	$func = $_GET[func];
	 
	$page = new Page(_("Edit Plan Rule"));
	$page->open();
	
	$b = new Frameback("/control/planedit.php?editid=".urlencode($planname));
	$b->draw();
	
	$clsplan = new plan();
	$acl = $clsplan->getacl($aclid);

	
	$rules = $clsplan->getrules();
	$services = $clsplan->getservices();
	
	$facl = new Frame ("acledit");
	$facl->title=_("Rule Properties");
	
	$formacl = new Form("acledit");
		if ($aclid) {
			$formacl->itype="hidden";
			$formacl->iname="editid";
			$formacl->ivalue=$aclid;
			$formacl->nextitem();
		}
		
		$formacl->itype="hidden";
		$formacl->iname="planname";
		$formacl->ivalue=$planname;
		$formacl->nextitem();
		
		
		$formacl->itype="list";
		$formacl->iname="servicetype";
		$formacl->ilabel=_("Service Filter");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[servicetype];
		$formacl->ivalues[all]=_("All services");
		$formacl->ivalues["list"]=_("From List (layer7 protocols)...");
		$formacl->ivalues[custom]=_("TCP/UDP Custom Service...");
		if ($func=="block") {
			if (constant("PROXY")=="SQUID") {
				$formacl->ivalues[proxy]=_("URL or Site IP");
				$formacl->ivalues[rule]=_("Pre-defined Rules");
			}
		}
		$formacl->nextitem();
		
		$formacl->itype="list";
		$formacl->iname="rule";
		$formacl->ilabel=_("Rule List");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[rule];
		$formacl->ivalues=$rules;
		$formacl->nextitem();
		
		$formacl->itype="textbox";
		$formacl->iname="sitelistname";
		$formacl->ilabel=_("Site list name");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[sitelistname];
		$formacl->nextitem();
		
		$urlblock_value=$acl[urlblock];
		
		$formacl->itype="allowdenytext";
		$formacl->iname="urlblock";
		$formacl->ilabel=_("List of site URLs");
		$formacl->ilabel_allow=_("Exception list");
		$formacl->ilabel_deny=_("Block these URLs/sites (one per line)");
		$formacl->ihelp="";
		$formacl->ivalue=$urlblock_value;
		$formacl->nextitem();

		$ipblock_value=$acl[ipblock];

		$formacl->itype="allowdenytext";
		$formacl->iname="ipblock";
		$formacl->ilabel=_("List of site IPs");
		$formacl->ilabel_allow=_("Exception list");
		$formacl->ilabel_deny=_("Block these IPs/networks (one per line)");
		$formacl->ihelp="";
		$formacl->ivalue=$ipblock_value;
		$formacl->nextitem();

		$formacl->itype="list";
		$formacl->iname="service";
		$formacl->ilabel=_("Service List");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[service];
		$formacl->ivalues=$services;
		$formacl->nextitem();
	
		$formacl->itype="textbox";
		$formacl->iname="serviceport";
		$formacl->ilabel=_("Service Custom Port");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[serviceport];
		$formacl->nextitem();
		
		$formacl->itype="list";
		$formacl->iname="serviceproto";
		$formacl->ilabel=_("Service Custom Protocol");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[serviceproto];
		$formacl->ivalues[tcp]="TCP";
		$formacl->ivalues[udp]="UDP";
		$formacl->nextitem();
		
		$formacl->itype="list";
		$formacl->iname="dsttype";
		$formacl->ilabel=_("Destination");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[dsttype];
		$formacl->ivalues[all]="All destinations";
		$formacl->ivalues[custom]=_("Custom...");
		$formacl->nextitem();
		
		$formacl->itype="textbox";
		$formacl->iname="dstip";
		$formacl->ilabel=_("Destination IP");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[dstip];
		$formacl->nextitem();
		
		$formacl->itype="list";
		$formacl->iname="dstmask";
		$formacl->ilabel=_("Destination Netmask");
		$formacl->ihelp=_("ex: 255.255.255.0");
		$formacl->ivalue=$acl[dstmask];
		$formacl->ivalues=networksetup::getnetmasks();
		$formacl->nextitem();
		
		$formacl->itype="list";
		$formacl->iname="timetype";
		$formacl->ilabel=_("Validity");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[timetype];
		$formacl->ivalues[all]=_("Always");
		$formacl->ivalues[custom]=_("Custom...");
		$formacl->nextitem();
		
		$formacl->itype="time";
		$formacl->iname="timestart";
		$formacl->ilabel=_("From Time");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[timestart];
		$formacl->nextitem();
		
		$formacl->itype="time";
		$formacl->iname="timestop";
		$formacl->ilabel=_("To Time");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[timestop];
		$formacl->nextitem();

		$formacl->itype="week";
		$formacl->iname="days";
		$formacl->ilabel=_("Week Days");
		$formacl->ihelp="";
		$formacl->ivalue=$acl[days];
		$formacl->nextitem();

		$formacl->itype="hidden";
		$formacl->iname="func";
		$formacl->ivalue=$func;
		$formacl->nextitem();
		

		if ($func=="band") {
			$formacl->itype="textbox";
			$formacl->iname="download";
			$formacl->ilabel=_("Max Download Speed (Kbps)");
			$formacl->ihelp="";
			$formacl->ivalue=$acl[download];
			$formacl->nextitem();
			
			$formacl->itype="textbox";
			$formacl->iname="upload";
			$formacl->ilabel=_("Max Upload Speed (Kbps)");
			$formacl->ihelp="";
			$formacl->ivalue=$acl[upload];
			$formacl->nextitem();
		}
		
	$formacl->sh[]="servicetype=all:rule.hide;dsttype.show;sitelistname.hide;ipblock.hide;urlblock.hide;service.hide;serviceport.hide;serviceproto.hide";
	$formacl->sh[]="servicetype=custom:rule.hide;dsttype.show;sitelistname.hide;ipblock.hide;urlblock.hide;service.hide;serviceport.show;serviceproto.show";
	$formacl->sh[]="servicetype=list:rule.hide;dsttype.show;sitelistname.hide;ipblock.hide;urlblock.hide;service.show;serviceport.hide;serviceproto.hide";	

	$formacl->sh[]="servicetype=proxy:rule.hide;sitelistname.show;ipblock.show;urlblock.show;dsttype.hide;dstip.hide;dstmask.hide;service.hide;serviceport.hide;serviceproto.hide";	

	$formacl->sh[]="servicetype=rule:rule.show;dsttype.hide;sitelistname.hide;ipblock.hide;urlblock.hide;service.hide;serviceport.hide;serviceproto.hide";	


	$formacl->sh[]="dsttype=all:dstip.hide;dstmask.hide";
	$formacl->sh[]="dsttype=custom:dstip.show;dstmask.show";
	
	$formacl->sh[]="timetype=all:timestart.hide;timestop.hide;days.hide";
	$formacl->sh[]="timetype=custom:timestart.show;timestop.show;days.show";

	$facl->draw($formacl);
		
	
	$page->close();
	
?>
