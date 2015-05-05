#-------------------------------------------------------------------------------------
# Nexus Core
# Classe gerenciar o socket do Core
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: CoreLocale.pm - 02/11/2005 - versão 0.1
# -------------------------------------------------------------------------------------
package CoreLocale;
use lib "$ENV{'NEXUS'}/core/lib";
use Conf;
use Locale::gettext;
use warnings;
use strict;

sub new {
	my ($pkg, $ctx) = @_;
	my $conf = new Conf;
	my $this = {
		"context" => $ctx,
		"domain" =>  Locale::gettext->domain($ctx),
		"dir" => "$ENV{'NEXUS'}".$conf->get("/locale/path")
		};
		$this{'domain'}->dir($this{'dir'});	
		return bless CoreLocale::$this,$pkg;
}
#--------------------------------------
# teste
sub teste {
	return "teste da classe CoreLocale funcionou!\n";
}
1;

