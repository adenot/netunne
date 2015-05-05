<?

class WS_Server {
	
	var $list = array();
	
	function WS_Server($auth=null){
		$user = base64_decode($_POST['USER']);
		$pswd = base64_decode($_POST['PSWD']);
		
		if(!is_null($auth)){
			if(!$auth($user,$pswd)){
				unset($this);
			}
		}
	}

	function func($name){
		return array_push($this->list,$name);
	}
	
	function exec(){
		$arg = unserialize(base64_decode($_POST['EXEC']));
		if(in_array($arg[0],$this->list)){
			ob_start();
			$ret = call_user_func_array($arg[0], array_slice($arg, 1));
			ob_clean();
			echo base64_encode(serialize($ret));
			return true;
		}
		return false;
	}
}

function testa_login($u,$p){
	if($u == 'abc')
		if($p == '123')
			return true;
	return false;
}

function testa_comando($n){
	return $_SERVER;
}

$ws = new WS_Server('testa_login');
$ws->func('testa_comando');
$ws->exec();

?>