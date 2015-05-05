#-------------------------------------------------------------------------------------
# Nexus Core
# Classe para manipular arquivos XML
# Console Soluções Tecnológicas - Analista e Programador: Fabiano Louback Gonçalves
# Arquivo: Xml.pm - 02/11/2005 - versão 0.1
# -------------------------------------------------------------------------------------
package Xml;
#use strict;
use warnings;
use XML::Mini::Document;
use XML::SAX::ParserFactory;
use XML::Validator::Schema;
use Data::Dumper;
require "$ENV{'NEXUS'}/core/lib/Conf.pm";
require "$ENV{'NEXUS'}/core/bin/common.pl";
my @DEF=('core','sys');

#----------------------------------------------------------------

sub new {
	my ($pkg, $file, $scheme) = @_;
	if((!$scheme) && ($file)){$scheme=substr($file,0,length($file)-3)."xsd";}
	return bless {
		'file' => $file,
		'scheme' => $scheme
	},$pkg;
}
#----------------------------------------------------------------
sub valid()
{
    my ($ins, $xmlfile) = shift;
	if($xmlfile){$ins->{'file'}=$xmlfile;}
    if(-e $ins->{'file'}){
		if($ins->{'scheme'} ne 'noscheme')
		{
        	$val = XML::Validator::Schema->new(file => $ins->{'scheme'});
        	$parser = XML::SAX::ParserFactory->parser(Handler => $val);
        	eval { $parser->parse_uri($ins->{'file'}) };
        	if($@)
            	{return 0;}
        	else
            	{return 1;}
		}
		else {return 1;}
    }
    else {return 0;}
}

#----------------------------------------------------------------
sub getHash() {
    my ($ins, $xmlfile) = shift;
	if($xmlfile){$ins->{'file'}=$xmlfile;} else {$xmlfile=$ins->{'file'};}
	my $xmldoc = XML::Mini::Document->new();
	$xmldoc->parse($xmlfile);
	return $xmldoc->toHash();
}
#----------------------------------------------------------------

sub testeHash() {
    my ($ins, $xmlfile) = shift;
	if($xmlfile){$ins->{'file'}=$xmlfile;} else {$xmlfile=$ins->{'file'};}
	my $xmldoc = XML::Mini::Document->new();
	$xmldoc->parse($xmlfile);
	$a = $xmldoc->toHash();
	#$b = $$a{'network'}{'routes'}{'route'}{'source'};
	#print "resultado = '$b'\n";
	
	$x=$$a{'network'}{'routes'}{'route'};
	print Dumper($x);
    print $$x[0]{'interface'}."\n\n\n";	
	foreach $y(@$x){
		print $$y{'name'}."\n";
	}
	return $a;
}

#----------------------------------------------------------------

sub file {
	my ($ins, $file) = @_;
	if(!$file) {return $ins->{'file'};}
	else {return $ins->{'file'}=$file;}
}

#-----------------------------------------------------------------

sub scheme {
	my ($ins, $scheme) = @_;
	if(!$scheme) {return $ins->{'scheme'};}
	else {return $ins->{'scheme'}=$scheme;}
}

#------------------------------------------------------------------
sub teste {
	my $ins = shift;
	return "teste funcionou! file='".$ins->{'file'}."\n";
}
1;

