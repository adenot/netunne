<?
	require_once("config.inc.php");
	require_once("cls_login.php");
	
	$login = new login();
	$login->login 	 = trim($_POST['login']);
	$login->password = trim($_POST['password']);
	$login->ip		 = $_SERVER['REMOTE_ADDR'];
	$login->action	 = $_POST['action'];
	
	if(!$login->auth()){
		
		$login->showLoginError();
		
	} else {
		
		echo "<script>window.location = 'pagina.php'</script>";
		
	}
	
	
	
	
?>