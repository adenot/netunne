#!/usr/bin/php5.0
<?php

$CONF = parse_ini_file('/etc/nexus/path');
include_once $CONF['NEXUS']."/core/nlib/common.nx";
set_include_path(get_include_path() . PATH_SEPARATOR . DIRNLIB . PATH_SEPARATOR . DIRBIN);

if ($argv[1]) {
	$one=1;
	$buf = trim($argv[1]);
}

if (file_exists(DIRBIN."nexus.php")) {
	include "nexus.php";
} else {
	include "nexus.nx";
}


?>
