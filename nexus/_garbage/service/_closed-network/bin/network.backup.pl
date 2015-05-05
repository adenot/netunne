#!/usr/bin/perl
use strict;
use warnings;
#processa o que tem que processar e retorna mensagem OK ou fail do out.pl
#require "$ENV{'NEXUS'}/core/include/out.pl";
require "$ENV{'NEXUS'}/core/lib/Conf.pm";
require "$ENV{'NEXUS'}/core/lib/Log.pm";
require "$ENV{'NEXUS'}/core/lib/Xml.pm";
require "$ENV{'NEXUS'}/core/bin/common.pl";

#instanciando objetos e variáveis -----------------------------------------------------------------
my $log = new Log('corelog','service/network',$$);
my $conf = new Conf('core'); #$conf->source("core");
my $xml = new Xml($ENV{'NEXUS'}.$conf->get('service/path')."/network/conf/network.xml","noscheme");
my $tplfile = $ENV{'NEXUS'}.$conf->get('service/path')."/network/tpl/interfaces.tpl";

#verificando se arquivos xml são válidos ----------------------------------------------------------
if($xml->valid()){
	print "\n".$xml->file."\nxml válido\n";

} 
else {
	print "\n".$xml->file."\nxml inválido\n";
}

#verificando se arquivos tpl são válidos ----------------------------------------------------------
	if(-e $tplfile){
		print "\n$tplfile\ntpl válido\n";
	}# template inválido
	else {
		print "\n$tplfile\ntpl inválido\n";
	}

