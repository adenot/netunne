<?php
if (!file_exists("/tmp/listhdds.tmp")) {
	$a = shell_exec("sudo listhdds 2>&1 > /tmp/listhdds.tmp");
//	echo $a;
}

/*
$stringHD="hda VMware_Virtual_IDE_Hard_Drive 1048576
sda VMware,_VMware_Virtual_S 2097152
:
";
*/
$stringHD = file_get_contents("/tmp/listhdds.tmp");


$stringHD=explode(":",$stringHD);
$arrayHDFinal = array();
$arrayListaHD = explode("\n", $stringHD[0]);
for ($i=0;$i<count($arrayListaHD);$i++) {
	$hd=$arrayListaHD[$i];
	if (trim($hd)=="") { continue; }

	$arrayHD = explode(" ", $hd);	
	
	
	$arrayHD[1] = str_replace("_", " ", $arrayHD[1]);
	$arrayHD[2] /= 1048576;

	if (intval(floor($arrayHD[2]))==0) { continue; }

	$arrayHD[2] = sprintf("%.1f", $arrayHD[2]);
	array_push($arrayHDFinal, $arrayHD);
}
$arrayFoundData0 = explode("\n",$stringHD[1]);
for ($i=0;$i<count($arrayFoundData0);$i++) {
	if (trim($arrayFoundData0[$i])!="") {
		$arrayFoundData[]=$arrayFoundData0[$i];
	}
}



?>
