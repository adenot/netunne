<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 03/07/2006					*
	*																*
	****************************************************************/

include "../common.php";


$logtext = record::wall($_POST[wall]);


$ret = Userlog::parse($logtext,$_POST[action]);
if ($ret==0) { exit(); }

if (trim($ret[0])==""||!$ret[0]) {
	$ret[0]="info";
}


header('Content-Type: text/html; charset=ISO-8859-1');

echo "userlog_".$ret[0].".gif"."#####".nl2br($ret[1])."#####".$ret[2];

?>
