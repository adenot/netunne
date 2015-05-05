#!/usr/bin/perl
# -----------------------------------------------------------------------------------------------
# Nexus Core
# Filtro de entrada - Responsável por filtrar as requisições recebidas e direcionar o tratamento
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: in.pl - 10/10/2005 - versão 0.2
# -----------------------------------------------------------------------------------------------
use Switch;

require "$ENV{'NEXUS'}/core/bin/service.pl";
require "$ENV{'NEXUS'}/core/bin/action.pl";

sub in_process()
{
	my $param = shift;
	switch($param)
	{
		case('network.xml'){
			return &srv_process('network');
		}
		case('bandwidth.xml'){
			print $client "$$: processamento bandwidth.xml em desenvolvimento\n";
		}
	}
}
1;
