#-------------------------------------------------------------------------------------
# Nexus Core
# Classe gerenciar o socket do Core
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: Listener.pm - 02/11/2005 - vers�o 0.1
# -------------------------------------------------------------------------------------
package Listener;
use lib "$ENV{'NEXUS'}/core/lib";
use Conf;
use IO::Socket;
use warnings;
use strict;

sub new {
	my ($pkg, $file) = @_;
	return bless {
		"listener" => $file
	},$pkg;
}
#--------------------------------------
# teste
sub teste {
	return "teste da classe Listener funcionou!\n";
}
1;

