#-------------------------------------------------------------------------------------
# Nexus Core
# Classe monitorar arquivos e sa�das de comandos do sistema
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: Monitor.pm - 02/11/2005 - vers�o 0.1
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

#fazer metodo para monitorar arquivo , quando passar aquelas fun��es da common pro manager oque tiver
#que escutar arquivos vai vir para qui e o manager faz referencia pra ca
