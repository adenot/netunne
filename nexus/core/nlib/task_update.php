<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 03/10/2006					*
	*																*
	****************************************************************/

$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

record::msg_log("Running task_update...","account");

$npak = new Npak();

$npak->getlist();
//print_r($npak->parselist());
$npak->autoinstall();

record::msg_log("task_update finish","account");

?>
