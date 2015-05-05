#-------------------------------------------------------------------------------------
# Nexus Core
# Classe para manipular o reposit�rio de mensagens do Core
# Console Solu��es Tecnol�gicas - Analista e Programador: Fabiano Louback Gon�alves
# Arquivo: Message.pm  - 02/11/2005 - vers�o 0.1
# -------------------------------------------------------------------------------------
package Message;
use lib "$ENV{'NEXIS'}/core/lib";
use Config;
use warnings;
use strict;

sub new {
	my ($pkg, $file) = @_;
	return bless {
		"file" => $file
	},$pkg;
}
#--------------------------------------
# write a log to logfile
sub write {
	 
}
sub show {
	return "teste funcionou!\n";
}
1;

