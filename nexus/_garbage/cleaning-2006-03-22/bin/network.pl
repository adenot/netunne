#!/usr/bin/perl
#-------------------------------------------------------------------------------------
# Nexus Core
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: Network.pm - 04/11/2005 - vers�o 0.1
# Script respons�vel por: 
# 	- executar o merge dos arquivos de configura��o e template
# 	- executar instru��es ACT contida no arquivo de configura��o do servi�o
# obs: este script utiliza a classe Network.pl contida na paste lib do servi�o
# -------------------------------------------------------------------------------------
use strict;
use warnings;
require "$ENV{'NEXUS'}/core/lib/Conf.pm";
require "$ENV{'NEXUS'}/core/lib/Log.pm";
require "$ENV{'NEXUS'}/core/service/network/lib/Network.pm"; # Fornece recursos para manipular o servi�o

#LEMBRAR...
#processa o que tem que processar e retorna mensagem OK ou fail do out.pl
#require "$ENV{'NEXUS'}/core/include/out.pl";

#instanciando objetos e vari�veis -----------------------------------------------------------------
my $conf = new Conf('core'); 
my $log = new Log('corelog','service/network',$$);
my $net = new Network("$ENV{'NEXUS'}/core/service/network/conf/network.net","noscheme");

#verificando se arquivos s�o v�lidos
if($net->validfiles){
	print "\nvalido\n";		
} else {
	print "\ninvalido\n";		
}

