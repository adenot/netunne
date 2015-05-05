#!/usr/bin/perl
use strict;
use warnings;
#processa o que tem que processar e retorna mensagem OK ou fail do out.pl
#require "$ENV{'NEXUS'}/core/include/out.pl";
require "$ENV{'NEXUS'}/core/lib/Conf.pm";
require "$ENV{'NEXUS'}/core/lib/Log.pm";
require "$ENV{'NEXUS'}/core/lib/Xml.pm";
require "$ENV{'NEXUS'}/core/bin/common.pl";

#instanciando objetos e vari�veis -----------------------------------------------------------------
my $log = new Log('corelog','service/network',$$);
my $conf = new Conf('core'); #$conf->source("core");
my $xml = new Xml($ENV{'NEXUS'}.$conf->get('service/path')."/network/conf/network.xml","noscheme");
my $tplfile = $ENV{'NEXUS'}.$conf->get('service/path')."/network/tpl/interfaces.tpl";

#verificando se arquivos xml s�o v�lidos ----------------------------------------------------------
if($xml->valid()){
	print "\n".$xml->file."\nxml v�lido\n";

} 
else {
	print "\n".$xml->file."\nxml inv�lido\n";
}

#verificando se arquivos tpl s�o v�lidos ----------------------------------------------------------
	if(-e $tplfile){
		print "\n$tplfile\ntpl v�lido\n";
	}# template inv�lido
	else {
		print "\n$tplfile\ntpl inv�lido\n";
	}

