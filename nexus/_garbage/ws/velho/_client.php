<?php

	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda			*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 12/07/2007					*
	*																*
	****************************************************************/

require 'SOAP/Client.php';

$client = new SOAP_Client('http://localhost:443/ws/server.php');

$ret = $client->call('fatorial',
						$params = array('valor' => 7),
                        $options);

print_r($ret);

if(PEAR::isError($ret)) {
    echo 'Error: ' . $ret->getMessage() . "<br>\n";
} else {
    echo 'Quotient is ' . $ret . "<br>\n";
}

?>