<?php

$langfile = $_GET[langfile];
$terms = file_get_contents("/etc/nexus/$langfile");

if (strstr($langfile,"txt")) {
	$terms = nl2br($terms);	
}

echo $terms;


?>
