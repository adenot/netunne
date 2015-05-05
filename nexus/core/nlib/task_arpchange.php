<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 29/05/2007					*
	*																*
	****************************************************************/


// executado apos o merge do forward para otimizar a aplicacao das regras de firewall
// soh vai aplicar o q for necessario
$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

record::msg_log("Running task_arpchange...","account");

/*
IP address       HW type     Flags       HW address            Mask     Device
192.168.100.1    0x1         0x2         00:08:54:32:66:89     *        eth0
192.168.100.2    0x1         0x2         00:13:D4:98:6B:E7     *        eth0
*/

if (!file_exists(DIRTMP."nx_arp.dat")) {
	shell_exec("touch ".DIRTMP."nx_arp.dat");
	clearstatcache();
}

$tmparp=file(DIRTMP."nx_arp.tmp"); // novo
$arpdat=file(DIRTMP."nx_arp.dat"); // antigo

$diff = array_diff($tmparp,$arpdat);

foreach ($diff as $dif) {
	list($tmpip,$tmphw,$tmpflags,$tmpmac) = sscanf($dif,"%s%s%s%s%s%s%s%s");
	shell_exec("echo \"FASTAUTH($tmpip,$tmpmac)\" >> ".DIRTMP."nx_arpdiff.log");
	shell_exec("/etc/nexus/bin/nexus.sh \"FASTAUTH($tmpip,$tmpmac)\"");
}

shell_exec("cp -af ".DIRTMP."nx_arp.tmp ".DIRTMP."nx_arp.dat");

record::msg_log("task_arpchange finish","account");

?>
