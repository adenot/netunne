<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/08/2006					*
	*																*
	****************************************************************/
	

	require_once ("../common.php");
	
	$page = new Page(_("Publish Server"));
	$page->open();
	
	$conf = new Conf("forward");
	# Lista dos guests
	$pubs = xml::normalizeseq($conf->get("forward/publishs/publish"));
	
	$data[] = array(_("Description"),_("Server"),_("Server Port"),sprintf(_("%s Port"),PRODNAME));
	
	foreach ($pubs as $pub) {
		if (conv::startwith("`HOST.USER",$pub[newip])) {
			$tmp = explode(".",str_replace("`","",$pub[newip]));
			$pub[newip]=sprintf(_("customer: %s"),$tmp[2]);
		}
		if (trim($pub[description])=="") {
			$pub[description]=_("(no description)");
		}
		if ($pub[proto]=="tcpudp") { $pub[proto] = "tcp+udp"; }
		
		$data[] = array($pub[description],$pub[newip],$pub[proto]."/".$pub[newdport],$pub[dport]);
	}
	
	
	$frame2 = new Framebutton ("publish_do_edit");
	$frame2->title = _("Publish new server");
	//$frame2->help = _("Go to new form");
	$frame2->action = "publish_do_edit";
	$frame2->buttontext = _("Create");
	$frame2->draw();
	
	# Frame 1
	$frame = new Frame ("publist");
	$frame->title=_("Published Servers");
	# Tabela
	$table = new Table ("publist");
	$table->data = $data;
	
	# Tamanhos das colunas
	$table->size[0]=45;
	$table->size[1]=25;
	$table->size[2]=15;
	$table->size[3]=15;
	$table->linkid=3;
	
	# A��es dos bot�es
	$actions[]=array("edit",_("Edit"),"publish_do_edit");
	$actions[]=array("remove",_("Remove"),"publish_do_remove");
	$table->actions=$actions;
	
	$table->multiactions=array(1);
	# Monta
	$frame->draw($table);
	
	
	$page->close();
	

?>
