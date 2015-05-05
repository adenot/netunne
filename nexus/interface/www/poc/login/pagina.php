<?
	require_once("config.inc.php");
	require_once("cls_login.php");
	
	$login = new login();
	$login->login 	 = $_SESSION['login']['login'];
	$login->password = $_SESSION['login']['password'];
	$login->ip		 = $_SERVER['REMOTE_ADDR'];
	$login->action	 = "auth";
	
	if(!$login->auth()){
		
		$login->showLoginError();
		
	}
	
	echo "hello world!!!"
	
?>