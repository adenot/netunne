#!/usr/bin/perl
require "$ENV{'NEXUS'}/core/lib/License.pm";
$lic = new License;
#print "obtendo vers�o='".$lic->version()."'\n";
#print $lic->teste();
#print "md5='".$lic->createMd5()."'";
#$lic->writeMd5;
open(LIC,"$ENV{'NEXUS'}/core/conf/lic.ctf");
$licfile=<LIC>;
#print "\n\$licfile=$licfile\n";
#print "\ntestando licensa ....\n";
if($lic->validate($licfile)){
	print "\ncertificado de licensa v�lido...\n";
} 
else{
	print "\ncertificado de licensa inv�lido\n";
}
close(LIC);
