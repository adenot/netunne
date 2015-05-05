<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 17/10/2006					*
	*																*
	****************************************************************/


require_once "lib_common.php";

class Frameclock {
	public $name;
	public $text;

	function __construct () {
		$this->name = conv::randkey();
	}
	
	public function draw() {
		$text = $this->text;
		$name = $this->name;
		
		include DIRHTML."html_frameclock.php";


	}
	
}