<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 27/05/2006					*
	*																*
	****************************************************************/

require_once "lib_common.php";

class Page {

	var $title;
	var $nostatus; // supre o box de status da abertura da pagina

	function page($title) {
		$this->title=$title;
	}

	public function open() {
		$title = $this->title;
		$nostatus = $this->nostatus;
		
		$login = new Login();
		if (!$login->autoauth()) 
			exit;
		
		require_once DIRHTML."html_page_open.php";
	}
	public function close () {
		require_once DIRHTML."html_page_close.php";
	}	
}


?>
