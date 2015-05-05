<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/

require_once "lib_common.php";

class Frame {
	public $title;
	public $name;
	public $startminimized=false;
	public $buttontext;
	public $action;
	
	function __construct ($name) {
		// nao posso deixar ele sem nome pq senao o recurso de minimizar nao vai funcionar
		$this->name = $name;
	}
	
	public function draw($object) {

		// se nao foi setado, entao pega no conf
		if ($this->startminimized==false)
			$this->startminimized=Setting::load("startminimized",$this->name);
		
		
		$startminimized=$this->startminimized;
		$title = $this->title;
		$name = $this->name;
		if (!$this->buttontext) {
			$buttontext = _("Save");
		} else { 
			$buttontext = $this->buttontext;
		}
		include DIRHTML."html_frame_open.php";
		
		//var_dump($object);
	
		
		$object->draw();
	

		if ($object->type=="form") {
			$formname=$object->name;	
			include DIRHTML."html_frame_form_close.php";
		} else if ($object->type=="table") {
			if ($this->action) {
				$action = $this->action;
				include DIRHTML."html_frame_action_close.php";
			} else {
				include DIRHTML."html_frame_close.php";
			}		
		}	

	}
	
}


?>
