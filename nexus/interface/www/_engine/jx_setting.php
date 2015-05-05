<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 29/05/2006					*
	*																*
	****************************************************************/

include "../common.php";

if ($_POST[func]=="save") {
	if ($_POST[section]!=""&&$_POST[name]!="") {
		Setting::save($_POST[section],$_POST[name],$_POST[value]);
	}
}


?>
