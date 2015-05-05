#!/usr/bin/perl -w
use XML::Xerces qw(error);
use Getopt::Long;
use Data::Dumper;
use strict;
my %OPTIONS;
my $rc = GetOptions(\%OPTIONS,
		    'help'
		   );

my $USAGE = <<"EOU";
usage: $0 xml_file
EOU

die "Bad option: $rc\n$USAGE" unless $rc;
die $USAGE if exists $OPTIONS{help};

die "Must specify input files\n$USAGE" unless scalar @ARGV;

package MyNodeFilter;
use strict;
use vars qw(@ISA);
@ISA = qw(XML::Xerces::PerlNodeFilter);
sub acceptNode {
  my ($self,$node) = @_;
  return $XML::Xerces::DOMNodeFilter::FILTER_ACCEPT;
}

package main;

my $DOM = XML::Xerces::XercesDOMParser->new();
my $ERROR_HANDLER = XML::Xerces::PerlErrorHandler->new();
$DOM->setErrorHandler($ERROR_HANDLER);
eval{$DOM->parse($ARGV[0])};
error($@,"Couldn't parse file: $ARGV[0]")
  if $@;

my $doc = $DOM->getDocument();
my $root = $doc->getDocumentElement();

my $hash = node2hash($root);
print STDOUT Data::Dumper->Dump([$hash]);
exit(0);

sub node2hash {
  my $node = shift;
  my $return = {};

  # build a hasref that represents this element
  $return->{node_name} = $node->getNodeName();
  if ($node->hasAttributes()) {
    my %attrs = $node->getAttributes();
    $return->{attributes} = \%attrs;
  }

  # insert code to handle children
  if ($node->hasChildNodes()) {
    my $text;
    foreach my $child ($node->getChildNodes) {
      push(@{$return->{children}},node2hash($child))
	if $child->isa('XML::Xerces::DOMElement');
      $text .= $child->getNodeValue()
	if $child->isa('XML::Xerces::DOMText');
    }
    $return->{text} = $text
      if $text !~ /^\s*$/;
  }
  return $return;
}
