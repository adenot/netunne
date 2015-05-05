<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 12, 2006					*
	*																*
	****************************************************************/

$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

record::msg_log("Running task_sysmonitor...","account");

function e ($line) {
	$args = func_get_args ();
	
	for ($i=1;$i<count($args);$i++) {
		if (substr_count(strtoupper($line),strtoupper($args[$i]))==0)
			return false;
	}

	return true;
}
	

$tmpfile = explode("\n",file_get_contents("/var/log/dmesg-tmp.log"));

$err = @unserialize(@file_get_contents(DIRDATA."events.ser"));

$grep[0][title]=_("HD Error");
$grep[0][desc]=_("Harddisk error. Backup and replace the disk.");
$grep[0][prio]=0;


$grep[1][title]=_("{1}: Cable disconnected");
$grep[1][desc]=_("Network cable disconnected on {1}");
$grep[1][prio]=1;

$grep[2][title]=_("{1}: Cable connected");
$grep[2][desc]=_("Network cable connected on {1}");
$grep[2][prio]=1;

$grep[3][title]=_("{1}: Internet check failed");
$grep[3][desc]=_("It seems like internet link on {1} is down");
$grep[3][prio]=1;
$grep[3][id]="conncheck";

$grep[4][title]=_("Invalid License");
$grep[4][desc]=_("Your license is expired/invalid.");
$grep[4][prio]=0;

// Limite de usuarios de cr�dito atingido
// Max simultaneous connections credit-users reached

$grep[5][title]=_("Max simultaneous connections creditusers reached");
$grep[5][desc]=_("Check your license.");
$grep[5][prio]=2;


$grep[6][title]=_("Data partition {1} full");
$grep[6][desc]=_("Your data partition is {1} full, download and clean Data in Configuration > Backup & Data");
$grep[6][prio]=1;

$grep[7][title]=_("System partition full");
$grep[7][desc]=_("Your system partition is full, restart server and if the problem persists, contact technical support");
$grep[7][prio]=2;


$grep[8][title]=_("Max user limit reached");
$grep[8][desc]=_("You have more registered clients than your license allows. Remove some clients or upgrade your license.");
$grep[8][prio]=1;

$grep[9][title]=_("Memory Error");
$grep[9][desc]=_("Memory error. Check your memory (with memtest) or replace.");
$grep[9][prio]=0;

/*
if (!$err) {
	for ($i=0;$i<count($grep);$i++) {
		$err[$i][count]=0;
	}
}
*/
for ($l=count($tmpfile)-1;$l>=0;$l--) {
	$line = $tmpfile[$l];

	/*
	 * ERROR NO HD
	 */
	if (e ($line,"drive","seek","error") ) {
		$tmp=$grep[0];
	}
	
	/*
	 * PLACA DE REDE DESCONECTADA
	 */
	if (ereg("(.*): link down",$line,$tmpreg)) {
		$tmp=$grep[1];
		$tmp[title]=str_replace("{1}",$tmpreg[1],$tmp[title]);
		$tmp[desc]=str_replace("{1}",$tmpreg[1],$tmp[desc]);
	}
	/* 
	 * PLACA DE REDE CONECTADA
	 */
	if (ereg("(.*): link up.*",$line,$tmpreg)) {
		$tmp=$grep[2];
		$tmp[title]=str_replace("{1}",$tmpreg[1],$tmp[title]);
		$tmp[desc]=str_replace("{1}",$tmpreg[1],$tmp[desc]);
	}

	/* 
	 * CONNCHECK FALHOU
	 */
	if (ereg("(.*): Conncheck failed",$line,$tmpreg)) {
		$tmp=$grep[3];
		$tmp[title]=str_replace("{1}",$tmpreg[1],$tmp[title]);
		$tmp[desc]=str_replace("{1}",$tmpreg[1],$tmp[desc]);
	}

	/*
	 * INVALID LICENSE
	 */
	if (e ($line,"Invalid","license") ) {
		$tmp=$grep[4];
	}

	/*
	 * MAXCONN GUESTS
	 */
	if (trim($line)=="Maxconn Guests") {
		$tmp=$grep[5];
	}
	
	/* 
	 * DATA PARTITION FULL
	 */
	if (ereg("Data partition (.*) full",$line,$tmpreg)) {
		$tmp=$grep[6];
		$tmp[title]=str_replace("{1}",$tmpreg[1],$tmp[title]);
		$tmp[desc]=str_replace("{1}",$tmpreg[1],$tmp[desc]);
	}

	/* 
	 * SYSTEM PARTITION FULL
	 */
	if (ereg("System partition full",$line,$tmpreg)) {
		$tmp=$grep[7];
	}

	/* 
	 * MAX USER LIMIT REACHED
	 */
	if (ereg($grep[8][title],$line,$tmpreg)) {
		$tmp=$grep[8];
	}

	/*
	 * ERROR NA MEMORIA
	 * "Unable to handle kernel paging request at virtual address"
	 */
	if (e ($line,"Unable","handle","kernel","paging","address") ) {
		$tmp=$grep[9];
	}

	/* 
	 * FAT ERROR
	 * - INSTANT REACTION !!!!
	 */
	if (e ("FAT","Filesystem","panic")) {
		$datadisk = trim(file_get_contents("/etc/nexusdatadisk"));
		shell_exec("umount /mnt/$datadisk;fsck.vfat -a /dev/$datadisk;sync;mount -o uid=33 /mnt/$datadisk");
	}
	
	
	if ($tmp) {
		$tmp[time]=time();
		$tmp[desc].="\n"._("Details: ").$line;
		$err[] = $tmp;
		unset($tmp);
	}
}

//print_r($err);

file_put_contents(DIRDATA."events.ser",serialize($err));

record::msg_log("task_sysmonitor finish","account");


?>
