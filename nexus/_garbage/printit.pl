#!/usr/bin/perl -w
use strict;
# use blib;
use XML::Xerces;
use Getopt::Long;
use vars qw();

#
# Read and validate command line args
#

my $USAGE = <<EOU;
USAGE: $0 [-v=xxx][-n] file
Options:
    -v=xxx      Validation scheme [always | never | auto*]
    -n          Enable namespace processing. Defaults to off.
    -s          Enable schema processing. Defaults to off.

  * = Default if not provided explicitly

EOU
my %OPTIONS;
my $rc = GetOptions(\%OPTIONS,
		    'v=s',
		    'n',
		    's');

die $USAGE unless $rc;

die $USAGE unless scalar @ARGV;

my $file = $ARGV[0];
-f $file or die "File '$file' does not exist!\n";

my $namespace = $OPTIONS{n} || 0;
my $schema = $OPTIONS{s} || 0;
my $validate = $OPTIONS{v} || 'auto';

if (uc($validate) eq 'ALWAYS') {
  $validate = $XML::Xerces::AbstractDOMParser::Val_Always;
} elsif (uc($validate) eq 'NEVER') {
  $validate = $XML::Xerces::AbstractDOMParser::Val_Never;
} elsif (uc($validate) eq 'AUTO') {
  $validate = $XML::Xerces::AbstractDOMParser::Val_Auto;
} else {
  die("Unknown value for -v: $validate\n$USAGE");
}

#
# Parse and print
#

my $parser = XML::Xerces::XercesDOMParser->new();
$parser->setValidationScheme ($validate);
$parser->setDoNamespaces ($namespace);
$parser->setCreateEntityReferenceNodes(1);
$parser->setDoSchema ($schema);

my $ERROR_HANDLER = XML::Xerces::PerlErrorHandler->new();
$parser->setErrorHandler($ERROR_HANDLER);
eval {$parser->parse ($file)};
XML::Xerces::error($@) if ($@);

my $doc = $parser->getDocument();
my $impl = XML::Xerces::DOMImplementationRegistry::getDOMImplementation('LS');
my $writer = $impl->createDOMWriter();
if ($writer->canSetFeature("$XML::Xerces::XMLUni::fgDOMWRTFormatPrettyPrint",1)) {
  $writer->setFeature("$XML::Xerces::XMLUni::fgDOMWRTFormatPrettyPrint",1);
}
my $target = XML::Xerces::StdOutFormatTarget->new();
$writer->writeNode($target,$doc);
exit(0);</EOU>
