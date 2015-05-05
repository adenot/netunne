<?php

require_once 'SOAP/Server.php';

$server = new SOAP_Server;

$server->_auto_translation = true;

require_once '_command.php';
$command = new SOAP_Command();

$server->addObjectMap($command,'urn:SOAP_Command');

if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD']=='POST') {
    $server->service($HTTP_RAW_POST_DATA);
} else {
    require_once 'SOAP/Disco.php';
    $disco = new SOAP_DISCO_Server($server,'SOAP_Command');
    header("Content-type: text/xml");
    if (isset($_SERVER['QUERY_STRING']) &&
       strcasecmp($_SERVER['QUERY_STRING'],'wsdl')==0) {
        echo $disco->getWSDL();
    } else {
        echo $disco->getDISCO();
    }
    exit;
}

?>