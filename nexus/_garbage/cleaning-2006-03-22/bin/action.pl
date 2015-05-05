#!/usr/bin/perl
# -----------------------------------------------------------------------------------------------------------
# Nexus Core
# Filtro de serviços - Responsável por filtrar as requisições de ações recebidas e direcionar o tratamento
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: action.pl - 10/10/2005 - versão 0.2
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
