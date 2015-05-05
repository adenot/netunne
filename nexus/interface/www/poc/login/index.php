<?
	#echo time();
	# 1155914175 
	#1155914957 
	#echo  1155914175 - 1155914957;
?>
<html>

<head>


</head>

<body>

<form name="frm_login" action="login.php" method="post">

<? if($_GET['error']== "have_auth") { ?>
	Outro cara está logado com o ip <?=rawurldecode($_GET['ip'])?>.<br>
	Clique em OK para prosseguir
<? } ?>
<br>
<input type="hidden" name="action" value="<?=($_GET['error']=="have_auth") ? $_GET['error'] : "login"?>">
Login: <input type="text" name="login" <?= ($_GET['error']=="have_auth") ? "value=\"".rawurldecode($_GET['l'])."\" readonly" : "" ?>>
<br>
Senha: <input type="password" name="password" <?= ($_GET['error']=="have_auth") ? "value=\"".rawurldecode($_GET['p'])."\" readonly" : "" ?>>
<br>
<input type="button" value="Ok" onClick="document.frm_login.submit()">

</form>

</body>