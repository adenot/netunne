<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/

/****************************
*		JAVASCRIPT			*
****************************/
class javascript {
	
	var $group;
	var $tr_field;
	var $tr_field_sep;
	
	function javascriptfunction($type, $value=''){
	 
		switch($type){
			case("hidetr"):
				
				$arr_fld = "\n<script>var arr_fld_" . $this->group . " = new Array(" . implode(",",$this->tr_field) . ");</script>";
				$arr_sep = "\n<script>var arr_sep_" . $this->group . " = new Array(" . implode(",",$this->tr_field_sep) . ");</script>";
				return array($arr_fld, $arr_sep);
				
			break;

		}
		return $js;
	}
	
	function calljavascript($type, $v1='', $v2=''){
		
		switch($type){
			
			case("hidetr"):
			
				$tr_field = "arr_fld_" . $v1;
				$tr_field_sep = "arr_sep_" . $v1;
				return "hidetr(this.value, $tr_field, $tr_field_sep, $v2); ";
				
			break;
			case("showtr"):
			
				$tr_field = "arr_fld_" . $v1;
				$tr_field_sep = "arr_sep_" . $v1;
				return "showtr(this.value, $tr_field, $tr_field_sep, $v2); ";
				
			break;
			
		}
		
	}
	
	function runjavascript($command){
		
		echo "\n<script>\n\t$command\n</script>";
		
	}
}
?>