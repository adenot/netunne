<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 29/05/2006					*
	*																*
	****************************************************************/
require_once "lib_common.php";

class Setting {
	function save($section,$name,$value="") {
		$arr = parse_ini_file(DIRSET."settings.ini",1);
		$arr[$section][$name]=$value;
		clearstatcache();
		return write_ini_file(DIRSET."settings.ini",$arr);
	}
	function load($section,$name) {
		if (!file_exists(DIRSET."settings.ini"))
			@file_put_contents(DIRSET."settings.ini","");
		
		$arr = @parse_ini_file(DIRSET."settings.ini",1);
		
		//print_r($arr);
		if ($value=$arr[$section][$name])
			return $value;
		else 
			return false;
	}
}

?>
