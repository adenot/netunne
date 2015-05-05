#!/usr/bin/perl
#-------------------------------------------------------------------------------------
# Nexus Core
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: Network.pm - 04/11/2005 - versão 0.1
# Script responsável por: 
# 	- executar o merge dos arquivos de configuração e template
# 	- executar instruções ACT contida no arquivo de configuração do serviço
# obs: este script utiliza a classe Network.pl contida na paste lib do serviço
# -------------------------------------------------------------------------------------
use strict;
use warnings;
require "$ENV{'NEXUS'}/core/lib/Conf.pm";
require "$ENV{'NEXUS'}/core/lib/Log.pm";
require "$ENV{'NEXUS'}/core/service/network/lib/Network.pm"; # Fornece recursos para manipular o serviço

#LEMBRAR...
#processa o que tem que processar e retorna mensagem OK ou fail do out.pl
#require "$ENV{'NEXUS'}/core/include/out.pl";

#instanciando objetos e variáveis -----------------------------------------------------------------
my $conf = new Conf('core'); 
my $log = new Log('corelog','service/network',$$);
my $net = new Network("$ENV{'NEXUS'}/core/service/network/conf/network.net","noscheme");

#verificando se arquivos são válidos
if($net->validfiles){
	print "\nvalido\n";		
} else {
	print "\ninvalido\n";		
}

