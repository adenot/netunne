<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 6, 2006					*
	*																*
	****************************************************************/

require_once "lib_common.php";

class Framelist {
	public $title;
	public $name;
	public $act;
	public $data;
	public $datafrom;
	public $autorefresh=0;
	public $open=0;
	
	function __construct ($act="") {
		$this->act = $act;
		$this->name = $act;
	}
	
	public function draw() {

		$title = $this->title;
		$name = $this->name;
		$action = $this->act;
		
		/*
		for ($i=0;$i<count($this->data);$i++) {
			$this->data[$i][short] = croptext($this->data[$i][desc],50);
		}
		*/
		
		if ($this->datafrom) {
			$_SESSION["table_$name"][datafrom] = $this->datafrom;
		} else {
			$_SESSION["table_$name"][data] = $this->data;
		}

		$data = $this->data;
		
		if ($this->autorefresh==1) {
			$autorefresh="yes";
			$_SESSION["table_$name"][autorefresh] = $autorefresh;
		}
		if ($this->open==1) {
			$_SESSION["table_$name"][open] = 1;
		}	
		
		include DIRHTML."html_frame_open.php";
		if (!$data || count($data)==0) {
			echo _("no data");
		} else {
			include DIRHTML."html_tablelist.php";
		}
		include DIRHTML."html_frame_close.php";


	}
	
}
?>
