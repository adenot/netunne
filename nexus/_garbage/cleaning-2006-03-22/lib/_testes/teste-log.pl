#!/usr/bin/perl
require "$ENV{'NEXUS'}/core/lib/Log.pm";
$log = new Log('corelog','listener',$$,'TESTANDO TESTANDO 123');
print "obtendo type='".$log->type()."'\n";
print "definindo type='".$log->type("syslog")."'\n"; 
print $log->teste();
print "definindo type='".$log->type("corelog")."'\n"; 
print "mostrando outros:\n"; 
print "		context='".$log->{'context'}."'\n";
print "		pid='".$log->{'pid'}."'\n";
print "		message='".$log->{'message'}."'\n";
print "\n\n";
$log->write();
$espera = <STDIN>;
