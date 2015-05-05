<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 07/06/2006					*
	*																*
	****************************************************************/

include_once "lib_common.php";

class Table {
	var $action;	// quem processa as acoes *NOT IN USE*
	var $actions; 	// as acoes disponiveis array(array(icon,label,action))
	var $multiactions; // acoes multiplas (array de numeros das acoes acima, comecando em 0)
	var $data;		// data array(array(...))
	var $search;	// array(num_do_campo)
	var $perpage=16;	// num de resultados por pagina
	var $orderby;	// (num_do_campo) ou nada para a ordem natural
	var $size;		// tamanhos dos campos (array(size1,size2) em porcentagem)
	var $linkid; 	// qual num do campo vai ser passado como id no link do action
	
	var $type="table";
	var $name;
	
	function __construct ($name="") {
		if ($name=="") {
			$name = substr(md5(uniqid(rand(), true)),0,8);
		}
		$this->name = $name;
	}
	
	function draw() {
		
		$action = $this->action;
		$actions = $this->actions;
		$multiactions = $this->multiactions;
		$name = $this->name;
		$perpage = $this->perpage;
		
		$size=$this->size;
		
		// total eh 520 - (19*act)
		$sizetotal = 520;
		$sizetotal = $sizetotal - (19*count($this->actions));
		//echo $sizetotal;
		foreach ($size as $v) {
			$newsize[]=floor($sizetotal * ($v / 100));
		}
		
		if (count($this->actions)==1) {
			$nocheck=1;
		}
		if (!is_array($multiactions)) {
			$nocheck=2;
		}
		$_SESSION["table_$name"]["nocheck"] = $nocheck;
		$_SESSION["table_$name"]["data"] 	= $this->data;
		$_SESSION["table_$name"]["perpage"] = $this->perpage;
		$_SESSION["table_$name"]["actions"] = $this->actions;
		$_SESSION["table_$name"]["multiactions"] 	= $this->multiactions;
		if (!isset($this->linkid)) { $this->linkid="null"; }
		$_SESSION["table_$name"]["linkid"] 	= $this->linkid;
		$_SESSION["table_$name"]["size"] 	= $newsize;
		//$orderby = $this->orderby;
		
		# total of data
		$itemtotal = count($this->data)-1;
		$total = $itemtotal / $perpage;
		$total = ceil($total);
		

		include DIRHTML."html_table.php";
		
	}

}

?>
