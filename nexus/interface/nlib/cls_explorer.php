<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Dez 21, 2006					*
	*																*
	****************************************************************/

require_once "lib_common.php";

class Explorer {
	public $title;
	public $name;
	public $type="table";
	public $menu;
	public $submenu;
	

	function __construct ($act="") {
		$this->act = $act;
		$name = conv::randkey();
		$this->name = $name;
	}
	
	
	public function draw() {

		$title = $this->title;
		$name = $this->name;
		$action = $this->act;

		$engine = "jx_$name.php";

		include DIRHTML."html_explorer.php";


	}
}


?>
