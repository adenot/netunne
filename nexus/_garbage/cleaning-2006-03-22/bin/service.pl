#!/usr/bin/perl
# -----------------------------------------------------------------------------------------------------------
# Nexus Core
# Filtro de servi�os - Respons�vel por filtrar as requisi��es de servi�os recebidas e direcionar o tratamento
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: service.pl - 10/10/2005 - vers�o 0.1
# -----------------------------------------------------------------------------------------------------------
use Switch;
require "$ENV{'NEXUS'}/core/lib/Network.pm";
require "$ENV{'NEXUS'}/core/bin/out.pl";

sub srv_process()
{
	my $param = shift;
	my $ret;
	switch($param)
	{
		case('network'){
			my $obj = new Network();
		}
		case('bandwidth'){
		}		
	}
	if(!$obj->merge()) {return &out_fail();}
	else {return &out_ok();}

}
1;
