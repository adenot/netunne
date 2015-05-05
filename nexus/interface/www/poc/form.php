<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 20/06/2006					*
	*																*
	****************************************************************/

require_once ("../common.php");

$conf = new Conf("info");
$username = $conf->get("info/user");
$userkey  = $conf->get("info/userkey");


$page = new Page(_("Form"));
$page->open();

$frame = new Frame ("form");
$frame->title=_("%s New form");

$form = new Form ("form");

$form->itype="textbox";
$form->imask="alpha";
$form->iname="user";
$form->ilabel=_("Username");
$form->ihelp="";
$form->ivalue=$username;
$form->nextitem();

$form->itype="filebox";
$form->imask="file";
$form->iname="file";
$form->ilabel=_("File");
$form->ihelp=_("aaaa");
$form->ivalue=$file;
$form->nextitem();

$form->itype="list";
$form->iname="userplan";
$form->ilabel=_("user Plan");
$form->ivalues[1]=1;
$form->ivalues[2]=2;
$form->ihelp=_("Select user plan");
$form->javascript = javascript::calljavascript("hidetr","net",1);
$form->javascript .= javascript::calljavascript("showtr","ip",1);
$form->javascript .= javascript::calljavascript("showtr","net",2);
$form->javascript .= javascript::calljavascript("hidetr","ip",2);
$form->nextitem();

# Grupo ip
$form->opengroup("ip");
$form->itype="textbox";
$form->imask="ip";
$form->iname="ip1";
$form->ilabel=_("IP 1");
$form->ihelp="";
$form->ivalue=$ip;
$form->nextitem();

$form->itype="textbox";
$form->imask="ip";
$form->iname="ip2";
$form->ilabel=_("IP 2");
$form->ihelp="";
$form->ivalue=$ip;
$form->nextitem();
$form->closegroup();

# Grupo net
$form->opengroup("net");
$form->itype="textbox";
$form->imask="Net";
$form->iname="net1";
$form->ilabel=_("NET 1");
$form->ihelp="";
$form->ivalue=$ip;
$form->nextitem();

$form->itype="textbox";
$form->imask="net";
$form->iname="net2";
$form->ilabel=_("NET 2");
$form->ihelp="";
$form->ivalue=$ip;
$form->nextitem();
$form->closegroup();

//$form->hide[] = "ip";
//$form->hide[] = "net";

$frame->draw($form);

javascript::runjavascript("hidetr(1, arr_fld_ip, arr_sep_ip, 1)");
javascript::runjavascript("hidetr(1, arr_fld_net, arr_sep_net, 1)");
$page->close();

?>
