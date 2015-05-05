#!/usr/bin/perl
require "$ENV{'NEXUS'}/core/lib/Conf.pm";
$conf = new Conf;
print "obtendo source='".$conf->source()."'\n";
print "definindo source='".$conf->source("Listener")."'\n"; 
print $conf->teste();
print "definindo source='".$conf->source("Core")."'\n"; 
print "run/path='".$conf->get("run/path")."'\n";
#use lib "/home/kurumin/nexus/core/lib";
#use Configuration;
#$conf = new Configuration;
#print $conf->get();
