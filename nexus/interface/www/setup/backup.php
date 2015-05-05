<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 30/08/2006					*
	*																*
	****************************************************************/
	
	require_once ("../common.php");

	
	$page = new Page (_("Backup and Data"));
	$page->open();
	
	/*
	 * funcionamento:
	 * - a pasta core/data eh um link pra particao data entao o data eh gravado
	 *   diretamente na particao de dados
	 * - - se nao for, preciso criar (quem faz isso eh o nexus.init)
	 * - a cada merge, um tgz eh gerado com os XML e copiados para data (nexus.sh)
	 * 
	 * aqui vou precisar:
	 * - deixar o usuario baixar e limpar os dados
	 * - deixar o ususario fazer backup/restore dos confs
	 * - visualizar os backupconf e restaurar
	 * 
	 * funcoes do core:
	 * - backup(saida)
	 * - restore(entrada)
	 * - backupdata(saida)
	 * - deletedata(vamos-vamos-vamos)
	 * 
	 */
	 
	/*
	 * Tipos de dados q sao armazenados:
	 * - backupconf: esse cresce bastante, precisa de um rotate (30/50mb/mes)
	 * - graph: nao cresce mto, fixo por num de usuarios (100kb/usuario)
	 * - rrd: nao cresce mto, fixo por num de usuarios (200kb/usuario)
	 * - netphoto: cresce (50-200mb/mes)
	 * - log: nao cresce mto - talvez um rotate no futuro
	 * - user: nao tem problema, cresce mto pouco
	 */
	
	
	conv::include_all_fnc();

	$conf = new Conf("info");
	$confold = intval($conf->get("info/confold"));
	if ($confold==0) { 
		$confold=3; // em task_data.php
	}
	
	$frame = new Frame("confold");
	$frame->title = _("Clean old backups automatically");

		$form = new Form ("confold");
		
		$form->itype="textbox";
		$form->iname="confold";
		$form->ilabel=_("Delete backups older than (in months)");
		$form->ihelp="";
		$form->ivalue=$confold;
	
		$form->nextitem();
		
	$frame->draw($form);	
	
	$frame = new Frame ("backupconf");
	$frame->title=_("Auto-Generated Backups");

		$table = new Table ("backupconf");
		$table->data = backup::getbackupconf();
		
		$table->size=array(100);
		$table->linkid=0;
		$table->orderby="DESC 0";
		
		$actions[]=array("download",_("Download"),"backup_do_download");
		$actions[]=array("restore",_("Restore"),"backup_do_restore");
		$table->actions=$actions;
		
		//$table->multiactions=array(1);

	$frame->draw($table);
	
	$frestore = new Frame ("manualrestore");
	$frestore->title=_("Manual restore");
	$frestore->buttontext=_("Restore");
		$formrestore = new Form("backup_manualrestore");
		$formrestore->itype="filebox";
		$formrestore->iname="file";
		$formrestore->ilabel=_("Upload from file");
		$formrestore->ihelp=sprintf(_("Select %s backup file to restore"),PRODNAME);
		$formrestore->nextitem();
	
	$frestore->draw($formrestore);
	
	
	$page->close();
?>