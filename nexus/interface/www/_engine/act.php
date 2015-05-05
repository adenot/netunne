<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informaחדo Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 29/05/2006					*
	*																*
	****************************************************************/

include "../common.php";

ob_start();
print_r($_POST);
$c = ob_get_contents();
ob_end_clean();
system ("echo \"$c\" > /tmp/act.log");


function get_post($act) {
	global $_POST;
	$ret = array();
	foreach ($_POST as $k=>$v) {
		
		if (substr($k, 0, strlen($act))==$act) {
			$tmp = str_replace($act."_","",$k);
			$ret[$tmp]=$v;
		}
	}
	return $ret;
}


$formname 	= $_POST["formname"];
$act 		= strtoupper($_GET["act"]);
$input 		= get_post($_GET["act"]);

$login = new Login();
if (!$login->autoauth())
	exit();


$a = new Act();

if (!$_GET["act"]) {
	$a->html_status("wait",_("Saving config..."));
	return;
}

$a->refer = $_SERVER["HTTP_REFERER"];
$a->formname = $formname;
$a->input	 = $input;

$a->execute($act);

?>