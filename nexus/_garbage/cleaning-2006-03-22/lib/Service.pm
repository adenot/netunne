#-------------------------------------------------------------------------------------
# Nexus Core
# Classe gerenciar os Servi�os do Core
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: Service.pm - 02/11/2005 - vers�o 0.1
# -------------------------------------------------------------------------------------
package Service;
use lib "$ENV{'NEXUS'}/core/lib";
use Conf;
use warnings;
use strict;

sub new {
	my ($pkg, $file) = @_;
	return bless {
		"service" => $file
	},$pkg;
}
#--------------------------------------

sub teste {
	return "teste da classe Service funcionou!\n";
}
1;

