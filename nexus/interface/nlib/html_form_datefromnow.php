<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 29/05/2006					*
	*																*
	****************************************************************/

	// existe um processador desse valor em:
	// Form::fromnow(txt,list)

	$listvalues[minutes]=	_("minute(s)");
	$listvalues[hours]=		_("hour(s)");
	$listvalues[days]=		_("day(s)");
	$listvalues[months]=	_("month(s)");
	$listvalues[weeks]=		_("week(s)");
	$listvalues[years]=		_("year(s)");

?>
<script language="JavaScript">
	var textbox_value_<?=$name?> = '<?=$value?>';
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='textbox';
</script>
<div id="listdiv_<?=$name?>" class="fromnowdiv fromnowdiv_normal">
<input type="text" class="txtfromnow"
	maxlength="3"
	name="<?=$name?>_txt" id="textbox_<?=$name?>" value="<?=$txtvalue?>" 
	><select class="listfromnow"
	name="<?=$name?>_list" id="list_<?=$name?>" value="<?=$listvalue?>"
	>
<?php
	foreach ($listvalues as $k=>$v) {
		if ($k==$listvalue) { $s="selected"; } else { $s=""; }
?>
    <option <?=$s?> value="<?=$k?>"><?=$v?></option>
<?php
	}
?>
	</select>
<span style="vertical-align:middle;padding-left:3px;font-family:Verdana;font-size:10px;color:white"><?=_("from now")?></span>
	</div>
	
<?php
/*
$sel = new Select();
$sel->name=$name;
$sel->formname=$formname;
$sel->itemcount=$itemcount;
$sel->value=$listvalue;
$sel->values=$listvalues;
$sel->width=140;
//$sel->nobox=1;
$sel->draw();
*/
?>