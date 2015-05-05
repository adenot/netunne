#!/usr/bin/perl 
# -------------------------------------------------------------------------------------
# Nexus Core
# Módulo Manager - Responsável por gerenciar a execução dos outros módulos do sistema
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: manager.pl - 10/10/2005 - versão 0.1
# -------------------------------------------------------------------------------------
    use strict;
	use warnings;
	require "$ENV{'NEXUS'}/core/lib/Manager.pm";
	my $man = new Manager;
	print $man->teste();
