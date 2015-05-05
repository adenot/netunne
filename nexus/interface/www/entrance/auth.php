<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 5, 2006					*
	*																*
	****************************************************************/
	session_start();
	include "../common.php";
	
	//print_r($_POST);
	
	$login = new Login();
	if ($_GET[action]=="logout") {
		$login->logout();
		echo "<script>window.location = '/entrance/index.php'</script>";
		exit();
	} 
	
	$login->login 	 = trim(($_POST['action'] == "have_auth") ? $_SESSION['have_auth']['login'] : $_POST['user']);
	$login->password = $_POST['pass'];
	$login->ip		 = $_SERVER['REMOTE_ADDR'];
	$login->action	 = "login";
	
	
	if ($_POST[action]=="have_auth") {
		$login->cleanAuth();
	}
	
	if(!$login->auth()){ 
		$login->showLoginError();
		
	} else {
		echo "<script>window.location = '/setup/server.php'</script>";
		
	}
?>
