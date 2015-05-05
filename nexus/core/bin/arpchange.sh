#!/usr/bin/php5.0
<?php

function parsemac ($mac) {

        $mac = explode(":",$mac);
        for ($i=0;$i<count($mac);$i++) {
                $mac[$i]=sprintf("%02s",strtoupper($mac[$i]));
        }
        return implode(":",$mac);
}


/*
$text = "
To: root
Subject: new station eth1

hostname: <unknown>
ip address: 10.0.0.107
interface: eth1
ethernet address: 0:c:29:49:81:4e
ethernet vendor: Vmware, Inc.
timestamp: Saturday, May 26, 2007 13:09:45 -0300


";

*/


$line = "";
do {
        $lastline = $line;
        $line = fgets(STDIN);
        if (trim($line)=="" && trim($lastline)=="") { break; }
//      shell_exec("echo ".trim($line)." >> /tmp/arp.log");
        $text .= trim($line)."\n";
} while (true);


$lines = explode("\n",$text);
$newtext = array();

foreach ($lines as $line) {
        if (trim($line)=="") { continue; }

        $pos = strpos($line,":");
        $line[$pos]="#";
        $chunks = explode("#",$line);
        $newtext[trim($chunks[0])]=trim($chunks[1]);

}

if (substr($newtext["Subject"],0,11)=="new station") {
        $newtext["ethernet address"]=parsemac($newtext["ethernet address"]);
        shell_exec("echo \"FASTAUTH(".$newtext["ip address"].",".$newtext["ethernet address"].")\" >> /tmp/arp.log");
        shell_exec("/etc/nexus/bin/nexus.sh \"FASTAUTH(".$newtext["ip address"].",".$newtext["ethernet address"].")\"");
}

?>

