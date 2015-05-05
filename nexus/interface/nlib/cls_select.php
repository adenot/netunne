<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 01/08/2006					*
	*																*
	****************************************************************/

class Select {
	
	var $name;
	var $value;
	var $values;
	//var $onchange;
	var $width;
	
	// nexus oddities:
	var $formname;
	var $itemcount;
	var $nobox;
	
	function draw() {
		$name=$this->name;
		$value=$this->value;
		$values=$this->values;
		$width=$this->width;
		$formname=$this->formname;
		$itemcount=$this->itemcount;
		$nobox=$this->nobox;
		include DIRHTML."html_cls_select.php";
		
	}
		
	
}


?>
