#-------------------------------------------------------------------------------------
# Nexus Core
# Classe provedora de recursos para manipular tarefas do serviço Network
# o scipt network.pl utiliza os recursos desta classe
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: Network.pm - 04/11/2005 - versão 0.1
# -------------------------------------------------------------------------------------
package Network;
#use strict;
use warnings;
use XML::Mini::Document;
use Data::Dumper;
require "$ENV{'NEXUS'}/core/lib/Xml.pm";
require "$ENV{'NEXUS'}/core/lib/Conf.pm";
require "$ENV{'NEXUS'}/core/bin/common.pl";

#----------------------------------------------------------------

sub new {
	my ($pkg, $action) = @_;

	return bless {
		'action' => $action,
		'xmlfile' => '',
		'conf' => new Conf,
		'xml' => new Xml
	},$pkg;
}

#----------------------------------------------------------------

sub validfiles()
{
	my $ins = shift;
	my $ret=1;
	my $conf = $ins->{'conf'};
	my $xml = $ins->{'xml'};
	my $tplfile = $ENV{'NEXUS'}.$conf->get('service/path')."/network/tpl/interfaces.tpl";

	$conf->source('core');
	$xml->scheme("noscheme");#acrescentar o eschema depois
	#MOD SPIKE
	$xml->file($ENV{'NEXUS'}."/conf/network.xml");

	#verificando se arquivos xml é válido
	if(!$xml->valid){$ret=0;}
	
	#verificando se arquivo(s) tpl (é/são) válido(s) 
    if(!(-e $tplfile)){$ret=0;}
	
	return $ret;
}

#--------------------------------------------------------

sub getNetHash()
{
	my $ins = shift;
	my $file = "network.xml";
    $ins->{'conf'}->source('core');
    $ins->{'xml'}->scheme("noscheme");
    $ins->{'xml'}->file($ENV{'NEXUS'}."/conf/".$file);
	return($ins->{'xml'}->getHash());
}

#--------------------------------------------------------

sub getRoutes()
{
	my $ins = shift;
	my $h = $ins->getNetHash();
    return $$h{'network'}{'routes'}{'route'};
}

#----------------------------------------------------------------

sub getInterfaces()
{
	my $ins = shift;
	my $h = $ins->getNetHash();
    return($$h{'network'}{'interfaces'}{'interface'});
}

#----------------------------------------------------------------

sub printNetGroup()
{
	my ($ins, $type, $out) = @_;
	my $ifaces; my $iface;

	if(!$out){$out = \*STDOUT;}
	if(lc($type) eq "interfaces")
		{$ifaces = $ins->getInterfaces();}
	elsif(lc($type) eq "routes")
		{$ifaces = $ins->getRoutes();}
	elsif(lc($type) eq "dnsservers")
		{$ifaces = $ins->getDnsServers();}
	elsif(lc($type) eq "dnssearches")
		{$ifaces = $ins->getDnsSearches();}
	else {return 0;}

	if(scalar(@$ifaces)==0)
		{print $out "$ifaces\n";}
	else{
    	foreach $iface(@$ifaces) {
        	print $out $$iface{'device'}."\n";
    	}
	}
}

#----------------------------------------------------------------

sub getDnsServers()
{
	my $ins = shift;
	my $h = $ins->getNetHash();
    return($$h{'network'}{'dns'}{'nameserver'});
}

#----------------------------------------------------------------

sub getDnsSearches()
{
	my $ins = shift;
	my $h = $ins->getNetHash();
    return($$h{'network'}{'dns'}{'search'});
}

#----------------------------------------------------------------#----------------------------------------------------------------

sub getMaps()
{
	my $ins = shift;
	my $h = $ins->getNetHash();
    return($$h{'network'}{'maps'}{'map'});
}

#------------------------------------------------------------------

sub merge
{
	my $ins = shift;
	$ins->mergeInterfaces();
	$ins->mergeDns();	
}

#------------------------------------------------------------------

sub mergeDns 
{
	my $ins = shift;
	my $tofile; my $routes; 
	my $toFile = $ins->getFromTo('dns.tpl');
	my $dnsServers; my $dnsSearches;
    print "\n\narquivo de saida = '$toFile'\n\n";	
	#Obtendo arquivos para entrada e saida do processamento
	open(FROM_FILE,"$ENV{'NEXUS'}/core/service/network/tpl/dns.tpl");
	open(TO_FILE,">$toFile");

	#lendo arquivos
	@fromFile = <FROM_FILE>; $fromFile = join("",@fromFile);
	
	#obtendo dns resolvers
	$dnsServers = $ins->getDnsServers();
	$dnsSearches = $ins->getDnsSearches();

	#processando a tag nameserver
	if(scalar(@$dnsServers)==0){		
		$_=$fromFile;
		s/{nameserver}/nameserver $dnsServers/g;
		$fromFile =~ s/{nameserver}/$_\n\n{nameserver}/
	}
	else{
		foreach $dnsServer(@$dnsServers)
        	{$fromFile =~ s/{nameserver}/nameserver $dnsServer\n{nameserver}/;}
	}		
		
	#processando a tag search
    if(scalar(@$dnsSearches)==0){
	
	}
	else{
		foreach $dnsSearch(@$dnsSearches)
        	{$fromFile =~ s/{search}/search $dnsSearch\n{search}/;}
	}

	$fromFile =~ s/{nameserver}\n//;
	$fromFile =~ s/{search}\n//;
	#print "\n\nimprimindo dns \n----------------------------------- \n";
	#print "$fromFile\n";
	print TO_FILE $fromFile;
	close(FROM_FILE); close(TO_FILE);
}

#------------------------------------------------------------------
#export globals = $fromGeneral, $fromStatic, $fromDynamic, $iface

sub mergeInterfaces 
{
	my $ins = shift;
	my $tpl; my $tofile; my $ifaces; 
	my $toFile = $ins->getFromTo('interfaces.tpl');
	
	#Obtendo arquivos para entrada e saida do processamento
	open(FROM_GENERAL,"$ENV{'NEXUS'}/core/service/network/tpl/interfaces.tpl");
	open(FROM_STATIC,"$ENV{'NEXUS'}/core/service/network/tpl/interfaces.static.tpl");
	open(FROM_DYNAMIC,"$ENV{'NEXUS'}/core/service/network/tpl/interfaces.dynamic.tpl");
	open(TO_FILE,">$toFile");

	#lendo arquivos
	@fromGeneral = <FROM_GENERAL>; $fromGeneral = join("",@fromGeneral);
	@fromStatic  = <FROM_STATIC>;  $fromStatic = join("",@fromStatic);
	@fromDynamic = <FROM_DYNAMIC>; $fromDynamic = join("",@fromDynamic);
	
	#obtendo interfaces
	$ifaces = $ins->getInterfaces();

	#verificando se possui mais de uma interface
	if(substr(scalar($ifaces),0,5) ne "ARRAY")
	{
		$iface=$ifaces;
		#verificando atributo assignment da interface 
		if($$ifaces{'assignment'} eq "static")
			{$ins->replaceStaticTags;}
		else
			{$ins->replaceDynamicTags;}
	}
	else
	{
   		foreach $iface(@$ifaces){
			#verificando atributo assignment da interface 
			if($$iface{'assignment'} eq "static")
				{$ins->replaceStaticTags;}
			else
				{$ins->replaceDynamicTags;}
   		}
	}
	$fromGeneral =~ s/{iface}//;
	#print "\n\nimprimindo iface \n----------------------------------- \n";
	#print "$fromGeneral\n";
	print TO_FILE $fromGeneral;
	close(FROM_GENERAL); close(FROM_STATIC); close(FROM_DYNAMIC); close(TO_FILE);
}

#---------------------------------------------------------------

sub replaceStaticTags 
{
	my $i; my $keyword;
	my @tags=('device','address','netmask','network','broadcast','gateway');
	$_=$fromStatic;	
	s/{$tags[0]}/$$iface{$tags[0]}/g;
	#troca cada tag por keyword e valor
	for($i=1;$i<=5;$i++)
	{	if($$iface{$tags[$i]}) {$keyword="\t".$tags[$i]." ".$$iface{$tags[$i]}."\n";}
		s/\t{$tags[$i]}.*\n/$keyword/g;
		$keyword="";
	}
	$fromGeneral =~ s/{iface}/$_\n\n{iface}/;
}

#----------------------------------------------------------------

sub replaceDynamicTags {
 	my $aux;	 
	$_=$fromDynamic;
	s/{device}/$$iface{'device'}/g;
	$fromGeneral =~ s/{iface}/$_\n\n{iface}/
}

#---------------------------------------------------------------

sub mergeStaticTeste {
	print "sub static \n";
	print $$iface{'device'}."\n\n";
}

#----------------------------------------------------------------

sub mergeDynamicTeste {	
	print "sub dynamic \n";
	print $$iface{'device'}."\n\n";
}

#----------------------------------------------------------------
sub getFromTo {
	my ($ins, $from) = @_;
	$maps = $ins->getMaps();
	if(scalar(@$maps)==0){
		if($maps{'from'} eq $from)
   			{return $maps{'to'};}
	}
	else{
   		foreach $maps(@$maps){
			if($$maps{'from'} eq $from)
      			{return $$maps{'to'};}
   		}
	}
} 

#----------------------------------------------------------------

sub action {
	my ($ins, $action) = @_;
	if(!$action) {return $ins->{'action'};}
	else {return $ins->{'action'}=$action;}
}

#-----------------------------------------------------------------

sub teste {
	my $ins = shift;
	return "teste funcionou! action='".$ins->{'action'}."'\n";
}
1;

