#!/usr/bin/perl 
# -------------------------------------------------------------------------------------
# Nexus Core
# Módulo Monitor - Responsável monitorar arquivos e saída de comandos
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: monitor.pl - 10/10/2005 - versão 0.1
# -------------------------------------------------------------------------------------
    use strict;
	use warnings;
	require "$ENV{'NEXUS'}/core/lib/Monitor.pm";
	my $mon = new Monitor;
	print $mon->teste();
