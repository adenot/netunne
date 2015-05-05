<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/

require_once "lib_common.php";

class Frameback {
	public $name;
	public $link;
	public $buttontext;

	function __construct ($link) {
		$this->name = conv::randkey();
		$this->link = $link;
	}
	
	public function draw() {

		$link = $this->link;
		$name = $this->name;
		$buttontext = $this->buttontext;

		//$logfile = $this->logfile;
		
		if (trim($buttontext)=="") 
			$buttontext = _("Back");
		
		if (trim($logtitle)=="")
			$act_link=1;
		
		include DIRHTML."html_frameback.php";


	}
	
}


?>
