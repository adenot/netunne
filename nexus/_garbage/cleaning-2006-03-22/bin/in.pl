#!/usr/bin/perl
# -----------------------------------------------------------------------------------------------
# Nexus Core
# Filtro de entrada - Respons�vel por filtrar as requisi��es recebidas e direcionar o tratamento
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: in.pl - 10/10/2005 - vers�o 0.2
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
