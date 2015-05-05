use strict;
use XML::Xerces qw(error);
use Getopt::Long;
use Benchmark;
use vars qw(%OPTIONS);

#
# Read and validate command line args
#

my $USAGE = <<EOU;
USAGE: $0 file
EOU

my $rc = GetOptions(\%OPTIONS,
		    'help');

die $USAGE if exists $OPTIONS{help};
die $USAGE unless scalar @ARGV;

my $file = $ARGV[0];
-f $file or die "File '$file' does not exist!\n";

my $val_to_use = XML::Xerces::SchemaValidator->new();
my $parser = XML::Xerces::SAXParser->new($val_to_use);
$parser->setValidationScheme ($XML::Xerces::AbstractDOMParser::Val_Auto);
$parser->setErrorHandler(XML::Xerces::PerlErrorHandler->new());
$parser->setDoNamespaces(1);
$parser->setDoSchema(1);

my $t0 = new Benchmark;
eval {$parser->parse ($file)};
error($@) if $@;

my $count = $parser->getErrorCount();
if ($count == 0) {
  my $grammar = $val_to_use->getGrammar();
  printf STDOUT "Found Grammar: %s\n", $grammar;
  my $iterator = $grammar->getElemEnumerator();
  if ($iterator->hasMoreElements()) {
    printf STDOUT "Found Elements\n";
    while ($iterator->hasMoreElements()) {
      my $elem = $iterator->nextElement();
      printf STDOUT "Element Name: %s, Content Model: %s\n",
	$elem->getFullName(),
	$elem->getFormattedContentModel();
      if ($elem->hasAttDefs()) {
	my $attr_list = $elem->getAttDefList();
	while ($attr_list->hasMoreElements()) {
	  my $attr = $attr_list->nextElement();
	  my $type = $attr->getType();
	  my $type_name;
	  if ($type == $XML::Xerces::XMLAttDef::CData) {
	    $type_name = 'CDATA';
	  } elsif ($type == $XML::Xerces::XMLAttDef::ID) {
	    $type_name = 'ID';
	  } elsif ($type == $XML::Xerces::XMLAttDef::Notation) {
	    $type_name = 'NOTATION';
	  } elsif ($type == $XML::Xerces::XMLAttDef::Enumeration) {
	    $type_name = 'ENUMERATION';
	  } elsif ($type == $XML::Xerces::XMLAttDef::Nmtoken
		   or $type == $XML::Xerces::XMLAttDef::Nmtokens
		  ) {
	    $type_name = 'NMTOKEN(S)';
	  } elsif ($type == $XML::Xerces::XMLAttDef::IDRef
		   or $type == $XML::Xerces::XMLAttDef::IDRefs
		  ) {
	    $type_name = 'IDREF(S)';
	  } elsif ($type == $XML::Xerces::XMLAttDef::Entity
		   or $type == $XML::Xerces::XMLAttDef::Entities
		  ) {
	    $type_name = 'ENTITY(IES)';
	  } elsif ($type == $XML::Xerces::XMLAttDef::NmToken
		   or $type == $XML::Xerces::XMLAttDef::NmTokens
		  ) {
	    $type_name = 'NMTOKEN(S)';
	  }
	  printf STDOUT "\tattribute Name: %s, Type: %s\n",
	    $attr->getFullName(),
	      $type_name;
	}
      }
    }
  }
} else {
  print STDERR "Errors occurred, no output available\n";
}
my $t1 = new Benchmark;
my $td = timediff($t1, $t0);

print STDOUT "$file: duration: ", timestr($td), "\n";
exit(0);</EOU>
