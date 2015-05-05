<?php

include('HTTP/Request.php');

class WS_Client {
	
	var $request = null;
	var $username = null;
	var $password = null;
	
	function WS_Client($link, $user=null, $pswd=null){
		
		$this->username = base64_encode($user);
		$this->password = base64_encode($pswd);
		
	    $this->request = new HTTP_Request($link);
	    $this->request->setMethod(HTTP_REQUEST_METHOD_POST);
	}

	function call(){
		$argv = func_get_args();
		$argc = func_num_args();	

		if($argc){
	    	$this->request->clearPostData();
	    	
		    if(!is_null($this->username))
		   		$this->request->addPostData('USER', $this->username);

		    if(!is_null($this->password))
		    	$this->request->addPostData('PSWD', $this->password);

		    $this->request->addPostData('EXEC', base64_encode(serialize($argv)));
		   
		    $this->request->sendRequest();
	
		    return unserialize(base64_decode($this->request->getResponseBody()));

		}else{
			return null;
		}
	}
}

$ws = new WS_Client("https://192.168.100.31/ws/server.php",'abc','123');
var_dump($ws->call('testa_comando',5));
?>