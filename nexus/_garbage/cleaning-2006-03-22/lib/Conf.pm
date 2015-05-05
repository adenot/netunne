#-------------------------------------------------------------------------------------
# Nexus Core
# Classe para manipular arquivos de log - Fornece ferramentas em comum para o sistema
# Console Soluções Tecnológicas - Desenvolvedor: Fabiano Louback
# Arquivo: Conf.pm - 02/11/2005 - versão 0.1
# -------------------------------------------------------------------------------------
package Conf;
use XML::Mini::Document;
use warnings;
use strict;

sub new {
	my ($pkg, $source) = @_;
	if(!$source){$source="Core";}
	return bless {'source' => $source},$pkg;
}

#------------------------------------------------------------------------------

sub teste {
	my $ins = shift;
	return "teste funcionou! \$source='".$ins->{'source'}."'\n";
}

#------------------------------------------------------------------------------

sub get {
	my ($ins, $path) = @_;
	my $xmldoc = new XML::Mini::Document;
	my $xmlfile = "$ENV{'NEXUS'}/core/conf/".lc($ins->{'source'}).".xml";
	my $xsdfile = "$ENV{'NEXUS'}/core/conf/".lc($ins->{'source'}).".xsd";
	$xmldoc->parse("$xmlfile");
	my $conf = $xmldoc->getElementByPath($path) or die("ERROR(020105) cant get $path in $xmlfile");
	return $conf->text();
}

#------------------------------------------------------------------------------

sub source {
	my ($ins, $source) = @_;
	if(!$source) {return $ins->{'source'};}
	else {return $ins->{'source'}=$source;}
}
1;
