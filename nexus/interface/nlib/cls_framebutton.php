<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/

require_once "lib_common.php";

class Framebutton {
	public $title;
	public $name;
	public $act;
	public $buttontext;
	public $help;
	public $logtitle;
	public $icon;
	public $animate;
	//public $logfile;
	
	function __construct ($act="") {
		$this->act = $act;
		$name = conv::randkey();
		$this->name = $name;
	}
	
	public function draw() {

		$title = $this->title;
		$name = $this->name;
		$buttontext = $this->buttontext;
		$action = $this->act;
		$help = $this->help;
		$logtitle = $this->logtitle;
		$icon = $this->icon;
		$animate = $this->animate;
		//$logfile = $this->logfile;
		
		if (trim($buttontext)=="") 
			$buttontext = _("Start");
		
		if (trim($logtitle)=="")
			$act_link=1;
		
		include DIRHTML."html_framebutton.php";


	}
	
}


?>
