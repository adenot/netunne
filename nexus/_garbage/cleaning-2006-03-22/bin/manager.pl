#!/usr/bin/perl 
# -------------------------------------------------------------------------------------
# Nexus Core
# M�dulo Manager - Respons�vel por gerenciar a execu��o dos outros m�dulos do sistema
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: manager.pl - 10/10/2005 - vers�o 0.1
# -------------------------------------------------------------------------------------
    use strict;
	use warnings;
	require "$ENV{'NEXUS'}/core/lib/Manager.pm";
	my $man = new Manager;
	print $man->teste();
