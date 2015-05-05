<?php

function redireciona($url, $blank=false){
		
	if(empty($blank) || $blank==false){
		
		@header("Location: $url");
		echo "<script>location.href='$url'</script>";
		
	} else {
		
		echo "<script>window.open('$url','windowRedir','')</script>";
		
	}
	
	return true;
 }
 
function montaSelect($nome,$lista,$campo1,$campo2,$tamanho=200,$codigo=0, $adicional="",$return=false, $extraopt="",$traco="") {
    $campo = "<select name=\"$nome\" style=\"width:{$tamanho}\" {$adicional} id=".$nome.">\n";
    if(!$traco)
    	$campo .="<option value=''>-</option>";
    if($extraopt)
    	$campo .="<option value='ext'". iif($codigo=='ext', "selected", "").">{$extraopt}</option>";
    if(!empty($lista)){
	    foreach($lista as $item){
	        if($item[$campo1] == $codigo) $selected = "selected";
	        else $selected = "";
	        $campo .= "\t<option {$selected} value=\"{$item[$campo1]}\">".htmlentities($item[$campo2])."</option>\n";
	    }
    }
    $campo .= "</select>";
    if(!$return) 
    	echo $campo;
    else 
    	return $campo;
 }
  function iif($Condicao, $RetornoA, $RetornoB)
  {
 	return ($Condicao ? $RetornoA : $RetornoB);	
  }
?>