<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 01/08/2006					*
	*																*
	****************************************************************/

	include "../common.php";
	
	$keysize = Setting::load("guest","keysize");
	if (is_bool($keysize)&&$keysize==false) {
		$keysize=6;
	}
	$randkey = strval("000000");
	while (substr($randkey,0,1)=="0") {
		$randkey = conv::randkey($keysize);
	}
	echo $randkey;


?>
