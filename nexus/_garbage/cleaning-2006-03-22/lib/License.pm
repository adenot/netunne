#-------------------------------------------------------------------------------------
# Nexus Core
# Classe para validar e gerenciar a licensa de utilização do produto
# Console Soluções Tecnológicas - Desenvolvedor: Fabiano Louback
# Arquivo: License.pm - 02/11/2005 - versão 0.1
# -------------------------------------------------------------------------------------
package License;
use XML::Mini::Document;
use warnings;
use strict;

sub new {
	my $pkg = shift;
	return bless {
			'version' => "1.0"
	},$pkg;
}

#------------------------------------------------------------------------------

sub teste {
	my $ins = shift;
	return "teste funcionou! \$version='".$ins->{'version'}."'\n";
}

#------------------------------------------------------------------------------

sub version {
	my ($ins) = @_;
	return $ins->{'version'};
}

#------------------------------------------------------------------------------

sub createMd5 {
	my $command; my $ret;
	$concat=`cat /proc/devices /proc/iomem /proc/ioports /proc/pci`;
	$md5=md5($concat);
	return $md5;
}

sub writeMd5 {
	my $file="$ENV{'NEXUS'}/core/conf/lic.ctf";
	print "\n\$file=$file\n";
	open(FILE,">$file");
	print FILE &createMd5();
	close(FILE);
}

sub validate {
	shift;
	my $param = shift;
	if($param eq &createMd5()){
		return 1;
	}
	else {return 0;}
}


1;

