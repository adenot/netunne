<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/

require_once "lib_common.php";

class Framereturn {
	public $title;
	public $name;
	public $act;
	public $buttontext;
	public $help;
	public $undo;

	
	function __construct ($name) {
		if ($name=="") {
			$name = conv::randkey();
		}
		$this->name = $name;
	}
	
	public function draw() {

		$title = $this->title;
		$name = $this->name;
		$buttontext = $this->buttontext;

		$undo = $this->undo;
		
		if (trim($buttontext)=="") 
			$buttontext = _("Undo");
		
		if (trim($logtitle)=="")
			$act_link=1;
		
		include DIRHTML."html_framereturn.php";


	}
	
}


?>
