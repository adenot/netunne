<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 16/01/2006					*
	*																*
	****************************************************************/
$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

record::msg_log("Running task_graph...","account");

$user = new Forward();
$user->openforwardxml();
$users = xml::normalizeseq($user->conf[users][user]);

$rrddir = scandir(DIRDATA."/rrd/");

/* 
 * varre o diretorio data/rrd em busca de arquivos .rrd e 
 * chama o rrd_graph para cada um deles
 * recomenda-se q seja executado diariamente
 */

for ($i=0;$i<count($rrddir);$i++) {
	if ($rrddir[$i]=="."||$rrddir[$i]=="..") { continue; }
	$filename = ereg_replace("(.*)\\.rrd\$","\\1",$rrddir[$i]);
	echo shell_exec(NEXUS."/core/bin/scripts/rrd_graph.sh $filename");

}

record::msg_log("task_graph finish","account");

?>
