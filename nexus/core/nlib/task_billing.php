<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 09/10/2006					*
	*																*
	****************************************************************/


$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";


record::msg_log("Running task_billing...","account");

$conf = new Conf("forward");
$day = $conf->get("forward/billing/day");
if (!$day) { $day=1; }

$today = date("j");

$file = date("Ym").".oldtotals";

if ($day==$today) {
	shell_exec("mv ".DIRDATA."/user/user.totals ".DIRDATA."/user/$file");
}

record::msg_log("task_billing finish","account");

?>
