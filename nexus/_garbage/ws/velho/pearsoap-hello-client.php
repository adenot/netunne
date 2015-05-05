<?
    /* Include PEAR::SOAP's SOAP_Client class: */
    require_once('SOAP/Client.php');

    /* Create a new SOAP client using PEAR::SOAP's SOAP_Client-class: */
    $client = new SOAP_Client('http://localhost:443/ws/pearsoap-hello-server.php');

    /* Define the parameters we want to send to the server's helloWorld-function.
       Note that these arguments should be sent as an array: */
    $params = array('inmessage'=>'World');

    /* Send a request to the server, and store its response in $response: */
	$response = $client->call('helloWorld',$params,array('namespace'=> 'urn:helloworld'));


    /* Print the server-response: */
    var_dump($response);
?>