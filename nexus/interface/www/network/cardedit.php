<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 14/02/2007					*
	*																*
	****************************************************************/


	/******
	 * prefixo de actions: guestedit_*
	 */

	
	require_once ("../common.php");

	conv::include_all_fnc();
	
	$page = new Page(_("Network Card Edit"));
	$page->open();
	

	$ns = new networksetup();
	
	$hwcards = $ns->gethardware();
	if ($hwcards) 
		$hwdata0 = $ns->gethardware_noconfig();
	
	if ($_GET[editid]) {
		$card = $ns->getcard($_GET[editid]);
	
		if (!$card) {
			if ($hwcards[all][$_GET[editid]]) {
				$card[device]=$_GET[editid];
				$newcard=1;
			}
			if ($_GET[force]==1) {
				$card[device]=$_GET[editid];
				$newcard=1;
			}
		}
	}
	
	$b = new Frameback("/network/index.php");
	$b->draw();
	
	
	$frame2 = new Frame ("cardedit");
	$frame2->startminimized="no";
	if ($newcard==1) {
		$frame2->title=_("New Network Card");
	} else {
		$frame2->title=_("Network Card Edit");
	}
		$form = new Form("networksetup_cardedit");
		//$form->action="networksetup_cardedit";
		
		$form->itype="label";
		$form->iname="device";
		$form->ilabel=_("Device");
		$form->ihelp=_("Linux Ethernet Device Id"); 
		$form->ivalue=xml::getxmlval("device",$card);
		$form->nextitem();

		if ($card[disabled]==1)	$status = "disable"; else $status = "enable";
		$form->itype="list";
		$form->iname="status";
		$form->ilabel=_("Status");
		$form->ihelp=_("Disable or Enable this network card");
		$form->ivalue=$status;
		$form->ivalues["enable"]=_("Enabled");
		$form->ivalues["disable"]=_("Disabled");
		$form->nextitem();

		if (!$card[type]) { $card[type]="external"; }
		$form->itype="list";
		$form->iname="type";
		$form->ilabel=_("Type");
		$form->ihelp=_("Internal: Connected to users\nExternal: Connected to Internet");
		$form->ivalue=$card[type];
		$form->ivalues["external"]=_("External");
		$form->ivalues["internal"]=_("Internal");
		$form->nextitem();
		

		if (!$card[assignment]) { $card[assignment]="static"; }
		$form->itype="list";
		$form->iname="assignment";
		$form->ilabel=_("IP Assignment");
		$form->ihelp="";
		$form->ivalue=$card[assignment];
		$form->ivalues["static"]=_("Static");
		$form->ivalues["dynamic"]=_("Dynamic (DHCP)");
		$form->nextitem();
		

		$form->itype="textbox";
		$form->iname="address";
		$form->ilabel=_("IP Address");
		$form->ihelp=_("ex: 192.168.100.1");
		$form->ivalue=$card[address];
		$form->nextitem();
		
		$form->itype="list";
		$form->iname="netmask";
		$form->ilabel=_("Netmask");
		$form->ihelp=_("ex: 255.255.255.0");
		if (!$card[netmask]) { $card[netmask]="255.255.255.0"; }
		$form->ivalue=$card[netmask];
		$form->ivalues=networksetup::getnetmasks(1);
		$form->nextitem();

		$form->itype="textbox";
		$form->iname="gateway";
		$form->ilabel=_("Default Gateway");
		$form->ihelp=_("IP address of the router");
		$form->ivalue=$card[gateway];
		$form->nextitem();
		
		
		$form->itype="textbox";
		$form->iname="dns";
		$form->ilabel=_("Primary DNS");
		$form->ihelp=_("DNS server for this link");
		$form->ivalue=$card[dns];
		$form->nextitem();
		
		$form->itype="list";
		$form->iname="weight";
		$form->ilabel=_("Download Speed");
		$form->ihelp=_("Select the nearest speed of this link");
		$form->ivalue=$card[weight];
		$form->ivalues=networksetup::getspeeds();
		$form->nextitem();


		if ($card[firewall][ssh][_attributes][action]=="logallow" &&
			$card[firewall][webadm][_attributes][action]=="logallow") {
			$card[fwadmin]="logallow";
		} else {
			$card[fwadmin]="drop";
		} 


		if ($card[firewall][dhcp][_attributes][action]=="allow") 
			$card[fwdhcp]="allow";
		else 
			$card[fwdhcp]="drop";
		
		$form->itype="list";
		$form->iname="fwdhcp";
		$form->ilabel=_("Activate DHCP");
		$form->ihelp=_("Serve DHCP to this network?");
		$form->ivalue=$card[fwdhcp];
		$form->ivalues[allow]=_("Yes");
		$form->ivalues[drop]=_("No");
		$form->nextitem();
		
		$form->itype="textbox";
		$form->iname="firstdhcp";
		$form->ilabel=_("DHCP Start Address");
		$form->ihelp=_("First IP given in DHCP Server");
		$form->ivalue=$card[firstdhcp];
		$form->preprocess="firstdhcp";
		$form->nextitem();

		$form->itype="list";
		$form->iname="fwadmin";
		$form->ilabel=_("Allow remote Administration");
		$form->ihelp=_("Allow SSH and HTTPS access in this card?");
		$form->ivalue=$card[fwadmin];
		$form->ivalues[logallow]=_("Yes");
		$form->ivalues[drop]=_("No");
		$form->nextitem();
		
			
			
$form->sh[]="status=disable:type.hide;assignment.hide;address.hide;netmask.hide;gateway.hide;dns.hide;weight.hide;fwdhcp.hide;fwadmin.hide;firstdhcp.hide";
$form->sh[]="status=enable;type=external;assignment=static:type.show;assignment.show;address.show;netmask.show;gateway.show;dns.show;weight.show;fwdhcp.hide;fwadmin.show;firstdhcp.hide";
$form->sh[]="status=enable;type=external;assignment=dynamic:type.show;assignment.show;address.hide;netmask.hide;gateway.hide;dns.show;weight.show;fwdhcp.hide;fwadmin.show;firstdhcp.hide";
$form->sh[]="status=enable;type=internal;assignment=static;fwdhcp=allow:type.show;assignment.hide;address.show;netmask.show;gateway.hide;dns.hide;weight.hide;fwdhcp.show;fwadmin.show;firstdhcp.show";
$form->sh[]="status=enable;type=internal;assignment=static;fwdhcp=drop:type.show;assignment.hide;address.show;netmask.show;gateway.hide;dns.hide;weight.hide;fwdhcp.show;fwadmin.show;firstdhcp.hide";		

/*
			$form->itype="list";
			$form->iname="pppoe";
			$form->ilabel=_("PPPoE");
			$form->ihelp=_("When enabled, allows users to log in thru PPPoE connections");
			$form->ivalue=$card[pppoe];
			$form->ivalues[0]="Disabled";
			$form->ivalues[1]="Enabled";
			$form->nextitem();
*/	
		
		
	$frame2->draw($form);
	
	$page->close();
		

?>