<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 23/11/2006					*
	*																*
	****************************************************************/

$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

record::msg_log("Running task_data...","account");

/* TAREFAS
 * - rotate do backupconf (maiores que X meses)
 * - alertar caso o tamanho do netphoto estiver mto grande
 */

//////////////////////////////////////////////////////////////////
// ROTATE DO BACKUPCONF
/////////////////////////////////////////////////////////////////
$conf = new Conf("info");
$confold = $conf->get("info/confold");

if (intval($confold)==0) {
	$confold = 3; // padrao 3 meses
}

$time = time();
$offset= $confold * 31 * 24 * 60 * 60;

if ($handle = opendir(NEXUS."/core/data/backupconf/")) {

   while (false !== ($file = readdir($handle))) {
       if ($file != "." && $file != "..") {
			$tmp = intval(str_replace(array("conf-",".tgz"),"",$file));	
			
			if ($tmp==0) { continue; }
			
			if (($time - $tmp) > $offset)
				$toremove[]=$file;

       }
   }
   closedir($handle);
}

if  (is_array($toremove)) {
	foreach ($toremove as $file) {
		record::msg_log("Removing {$file}","data_rotate");
		unlink (NEXUS."/core/data/backupconf/".$file);
	}
}

//////////////////////////////////////////////////////////////////
// ALERTA DO NETPHOTO - vou alerar o tamanho total das particoes
/////////////////////////////////////////////////////////////////

$datause = sysinfo::datadiskuse();
$sysuse  = sysinfo::sysdiskuse();


if ($datause >= 70)
	record::dmesg_log("Data partition ".$datause."% full");


// aproveito e vejo o tamanho da particao base

if ($sysuse > 85)
	record::dmesg_log("System partition full");


/////////////////////////////////////////////////////////////////
// apagando o task.log - jah eh feito no task.log
/////////////////////////////////////////////////////////////////

record::msg_log("task_data finish","account");

?>
