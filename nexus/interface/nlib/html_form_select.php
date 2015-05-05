<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 30/05/2006					*
	*																*
	****************************************************************/


$sel = new Select();
$sel->name=$name;
$sel->value=$value;
$sel->values=$values;
$sel->width="241";
$sel->formname=$formname;
$sel->itemcount=$itemcount;
//$sel->fontsize="";
$sel->draw();

?>
<?=$js?>
