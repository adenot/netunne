<?php

//require_once 'SOAP/Value.php';
//require_once 'SOAP/Fault.php';


//require_once 'example_types.php';

class SOAP_Command {

    var $__dispatch_map = array();

    function SOAP_Command(){

		$this->__dispatch_map['fatorial'] =	array(
				'in' => array('valor' => 'int'),
				'out' => array('outputInt' => 'int'),
	      );
    }

    
    function __dispatch($methodname){
        if (isset($this->__dispatch_map[$methodname]))
            return $this->__dispatch_map[$methodname];
        return NULL;
    }

    function fatorial($valor){
    	return $valor*2;
    }

}

?>