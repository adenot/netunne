#!/usr/bin/perl
require "$ENV{'NEXUS'}/core/lib/Xml.pm";
$xml = new Xml("$ENV{'NEXUS'}/core/service/network/conf/network.xml","noscheme");
print "\nteste=".$xml->teste()."\n";
print "\nscheme='".$xml->scheme()."'";
if($xml->valid) {print "\nxml v�lido (exist�ncia)";} else {print "\nxml inv�lido (exist�ncia)";}
$espera = <STDIN>;
