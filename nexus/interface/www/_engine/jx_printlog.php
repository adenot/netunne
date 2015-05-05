<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 28/06/2006					*
	*																*
	****************************************************************/

include "../common.php";

//$logtext = nl2br(file_get_contents($_POST[logfile]));
//echo $_POST[action];

$logtext0 = record::wall($_POST[wall]);

$logtext = nl2br($logtext0);

if (ereg("/EOF/.*\$",$logtext)) {
	// processa o resultado (opcional)
	//$logret = Act::process($_POST[action],$logtext0);
	
	$logtext  = str_replace("\n/EOF/","",$logtext);
	
	//$logtext .= nl2br("\n".$logret);
	
	$logtext .= "<HR size=1 border=1>";
	$logtext .= "<div class='logadvice' onclick='parent.unblackout();parent.location.href=\"?\";'>".
		_("This action has finish, click here to return.").
		"</div>";
	

	
} 

header('Content-Type: text/html; charset=ISO-8859-1');

echo wordwrap($logtext,60,"\n",1);
?>
