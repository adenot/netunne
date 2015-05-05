#!/usr/bin/perl
# -----------------------------------------------------------------------------------------------------------
# Nexus Core
# Filtro de serviços - Responsável por filtrar as requisições de serviços recebidas e direcionar o tratamento
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: service.pl - 10/10/2005 - versão 0.1
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
