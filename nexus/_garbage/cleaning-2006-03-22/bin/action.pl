#!/usr/bin/perl
# -----------------------------------------------------------------------------------------------------------
# Nexus Core
# Filtro de servi�os - Respons�vel por filtrar as requisi��es de a��es recebidas e direcionar o tratamento
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: action.pl - 10/10/2005 - vers�o 0.2
# -----------------------------------------------------------------------------------------------------------
use Switch;

sub act_process()
{
	my $param = shift;
	my $ret;
	switch($param)
	{
		case('network.xml'){
			
			return $ret;
		}
		case('squid.xml'){
		}		
	}
}
1;
