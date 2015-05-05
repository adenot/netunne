<?php
	session_start();
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/util.php");
	
	switch($_GET['pag']) {
		
		case "lang":
			$_SESSION['lang'] = $_POST['language'];
			redireciona("terms.php");
			break;

		case 1:
		
			$_SESSION['hdsys'] = $_POST['sys'];
			//echo "<script> alert(\"Sys-> ".$_SESSION['sys']."\\n Data-> ".$_SESSION['data']."\\n Ip-> ".$_SESSION['ip']."\\n Mask-> ".$_SESSION['mask']."\");</script>";
			redireciona("installb.php");
			//echo "<script>location.href=\"\"</script>";
		break;
		
		case 2:
		
			$_SESSION['ip'] = $_POST['ip'];
			$_SESSION['mask'] = $_POST['mask'];
			$_SESSION['password'] = $_POST['manager_password'];
			
			//echo "<script> alert(\"Sys-> ".$_SESSION['sys']."\\n Data-> ".$_SESSION['data']."\\n Ip-> ".$_SESSION['ip']."\\n Mask-> ".$_SESSION['mask']."\");</script>";
			//die();
			if ($_POST['backbutton'])
				redireciona("installa.php");
			else
				redireciona("confirm.php");
		
		break;

		
	}

?>
