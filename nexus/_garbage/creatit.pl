#!/usr/bin/perl -w
use strict;
# use blib;
use XML::Xerces;

#
# create a document
#

my $impl = XML::Xerces::DOMImplementationRegistry::getDOMImplementation('LS');
my $dt = eval{$impl->createDocumentType('contributors', '', 'contributors.dtd')};
XML::Xerces::error($@) if $@;
my $doc = eval{$impl->createDocument('contributors', 'contributors',$dt)};
XML::Xerces::error($@) if $@;

my $root = $doc->getDocumentElement();

$root->appendChild(CreatePerson(	
	$doc,
	'Mike Pogue',
	'manager',
	'mpogue@us.ibm.com'
));

$root->appendChild(CreatePerson(
	$doc,
	'Tom Watson',
	'developer',
	'rtwatson@us.ibm.com'
));

$root->appendChild(CreatePerson(
	$doc,
	'Susan Hardenbrook',
	'tech writer',
	'susanhar@us.ibm.com'
));

my $writer = $impl->createDOMWriter();
if ($writer->canSetFeature('format-pretty-print',1)) {
  $writer->setFeature('format-pretty-print',1);
}
my $target = XML::Xerces::StdOutFormatTarget->new();
$writer->writeNode($target,$doc);


#################################################################
# routines to create the document
# no magic here ... they just organize many DOM calls
#################################################################


sub CreatePerson {
  my ($doc, $name, $role, $email) = @_;
  my $person = $doc->createElement ("person");
  &SetName ($doc, $person, $name);
  &SetEmail ($doc, $person, $email);
  $person->setAttribute ("Role", $role);
  return $person;
}


sub SetName {
  my ($doc, $person, $nameText) = @_;
  my $nameNode = $doc->createElement ("name");
  my $nameTextNode = $doc->createTextNode ($nameText);
  $nameNode->appendChild ($nameTextNode);
  $person->appendChild ($nameNode);
}


sub SetEmail {
  my ($doc, $person, $emailText) = @_;
  my $emailNode = $doc->createElement ("email");
  my $emailTextNode = $doc->createTextNode ($emailText);
  $emailNode->appendChild ($emailTextNode);
  $person->appendChild ($emailNode);
}
