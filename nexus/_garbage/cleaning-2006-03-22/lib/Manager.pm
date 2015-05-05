#-------------------------------------------------------------------------------------
# Nexus Core
# Classe gerenciar os processos e recursos do Core
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: Manager.pm - 02/11/2005 - versão 0.1
# -------------------------------------------------------------------------------------
package Manager;
use lib "$ENV{'NEXIS'}/core/lib";
use Conf;
use warnings;
use strict;

sub new {
	my ($pkg, $file) = @_;
	return bless {
		"file" => $file
	},$pkg;
}
#--------------------------------------

sub teste {
	return "teste da classe Service funcionou!\n";
}
1;
#fazer metodo para ler gravar e apagar o numero do pid no arquivo em run/.../$$.pid
