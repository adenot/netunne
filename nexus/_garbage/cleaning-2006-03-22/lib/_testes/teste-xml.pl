#!/usr/bin/perl
require "$ENV{'NEXUS'}/core/lib/Xml.pm";
$xml = new Xml("$ENV{'NEXUS'}/core/service/network/conf/network.xml","noscheme");
print "\nteste=".$xml->teste()."\n";
print "\nscheme='".$xml->scheme()."'";
if($xml->valid) {print "\nxml válido (existência)";} else {print "\nxml inválido (existência)";}
$espera = <STDIN>;
