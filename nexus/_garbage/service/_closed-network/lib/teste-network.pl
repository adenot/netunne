#!/usr/bin/perl
use Data::Dumper;
require "$ENV{'NEXUS'}/core/service/network/lib/Network.pm";
$net = new Network();

print "\n--------------------------------------\n";

print $net->teste();


print "\n--------------------------------------\n";

if($net->validfiles) 
	{print "\nnet válido (existência)";} 
else 
	{print "\nnet inválido (existência)";}

=cut
print "\n--------------------------------------\n";
$routes = $net->getRoutes();
if(scalar(@$routes)==0)
	{print "$routes\n";}
else{
	foreach $route(@$routes) {
		print $$route{'name'}."\n";
	}
}
=cut

print "\n--------------------------------------\n";

=cut
    $ifaces = $ins->getInterfaces();
    if(substr(scalar($ifaces),0,5) ne "ARRAY")
        {print $$ifaces{'device'}."\n";}
    else{
        foreach $iface(@$ifaces) {
            print $$iface{'device'}."\n";
        }
    }
=cut

#$net->printNetGroup('interfaces');
#$net->printNetGroup("interfaces",\*STDOUT);
#$net->printNetGroup("dnsservers");
#$net->printNetGroup("dnssearches",\*STDOUT);

print "\n--------------------------------------\n";

=cut 
$dnsServers = $net->getDnsServers();
if(scalar(@$dnsServers)==0)
	{print "$dnsServers\n";}
else {
	foreach $dnsServer(@$dnsServers) {
		print $dnsServer."\n";	
	}
}


print "\n--------------------------------------\n";
$dnsSearches = $net->getDnsSearches();
if(scalar(@$dnsSearches)==0)
	{print "$dnsSearches\n";} 
else{
	foreach $dnsSearch(@$dnsSearches) {
		print $dnsSearch."\n";	
	}
}
=cut
$net->merge();
#$net->mergeInterfaces();
#$net->mergeDns();
#$net->getFromTo('interfaces.tpl');

