#!/usr/bin/perl 
# -------------------------------------------------------------------------------------
# Nexus Core
# M�dulo Monitor - Respons�vel monitorar arquivos e sa�da de comandos
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: monitor.pl - 10/10/2005 - vers�o 0.1
# -------------------------------------------------------------------------------------
    use strict;
	use warnings;
	require "$ENV{'NEXUS'}/core/lib/Monitor.pm";
	my $mon = new Monitor;
	print $mon->teste();
