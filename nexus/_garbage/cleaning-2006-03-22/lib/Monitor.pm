#-------------------------------------------------------------------------------------
# Nexus Core
# Classe monitorar arquivos e saídas de comandos do sistema
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: Monitor.pm - 02/11/2005 - versão 0.1
# -------------------------------------------------------------------------------------
package Monitor; 
use lib "$ENV{'NEXUS'}/core/lib";
use Conf;
use warnings;
use strict;

sub new {
	my ($pkg, $file) = @_;
	return bless {
		"monitor" => $file
	},$pkg;
}
#--------------------------------------
# teste
sub teste {
	return "teste da classe Monitor funcionou!\n";
}
1;

#fazer metodo para monitorar arquivo , quando passar aquelas funções da common pro manager oque tiver
#que escutar arquivos vai vir para qui e o manager faz referencia pra ca
