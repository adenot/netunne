<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 20/02/2008					*
	*																*
	****************************************************************/

ignore_user_abort(true);
set_time_limit(30);

// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

include "../nlib/lib_common.php";
$custom = @parse_ini_file(DIRSET."settings.ini",1);
$custom=$custom[custom];

$proxydenied = trim($custom[proxydenied]);

if (!$proxydenied) {
	$proxydenied = _("Access Denied");
}

if (file_exists("theme/$theme/redirect.htm"))
	$text = file_get_contents("theme/$theme/redirect.htm");
else 
	$text = file_get_contents("theme/$themedefault/redirect.htm");


if ($_GET[msg]=="E_A_D") { // ERR_ACCESS_DENIED
	$msg = $proxydenied;
}

$msg = htmlspecialchars_decode($msg);

$text = str_replace("{msg}",$msg,$text);
$text = str_replace("{theme}",$theme, $text);

$text = str_replace("{referer}",$_SERVER['HTTP_REFERER'],$text);

echo $text;

?>