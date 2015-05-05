<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 01/08/2006					*
	*																*
	****************************************************************/

	$width=$width-2;

?>
<script language="JavaScript">
	var select_value_<?=$name?> = '<?=$value?>';
	var select_tid_<?=$name?>;
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='select';

	var select_selected_name_<?=$name?>="";
	var select_selected_value_<?=$name?>="";
	var select_selected_<?=$name?>=-1;
	var select_tid_<?=$name?>;
</script>
<script language="JavaScript">
		var select_names_<?=$name?>=new Array();
		var select_values_<?=$name?>=new Array();
		var select_original_<?=$name?>=new Array();
<?php
	$i=0;
	foreach ($values as $k=>$v) {
?>
		select_names_<?=$name?>[<?=$i?>]='<?=$k?>';
		select_values_<?=$name?>[<?=$i?>]='<?=$v?>';
		select_original_<?=$name?>[<?=$i?>]='<?=$k?>';

<?php
		$i++;
	}
?>
</script>
<input type="hidden" id="select_hidden_<?=$name?>" name="<?=$name?>" value="<?=$value?>">
<?php if ($nobox!=1) { ?>
<div id="selectdiv_<?=$name?>" style="width:<?=$width?>px;" class="selectdiv selectdiv_normal">
<? } ?>
<table cellspacing=0 cellpadding=0 border=0>
<tr>
	<td>
<div id="select_all_div_<?=$name?>" style="width:<?=$width-2?>px;" class="select_all_div" >
<table cellspacing=0 cellpadding=0 border=0>
	<tr><td>
		<input type="textbox" name="select_textbox_<?=$name?>" id="select_textbox_<?=$name?>"
		class="select_textbox"
		style="width:<?=$width-22?>px;"
		autocomplete="off"
		onfocus="select_textbox_focus('<?=$name?>');"
		onclick="select_showhide_list('<?=$name?>');"
		onblur="select_textbox_blur('<?=$name?>');"
		readonly
		value="<?=$values[$value]?>"
		></td>
		<td><img src="<?=DIRIMG?>multilist_list.gif" onclick="select_showhide_list('<?=$name?>');"></td>
	</tr>
</table>
<div id="select_all_<?=$name?>"
	style="	position:absolute;
			clear:left;
			font-family: Verdana;
			font-size: 8pt;
			width:<?=$width-3?>px;
			height:70px;
			background-color: white;
			border-right:1px solid black;
			border-bottom:1px solid black;
			border-top:1px solid #C0C0C0;
			overflow-y:auto;
			overflow-x:hidden;
			visibility:hidden;
			background-color: #FFF4BA;
		"
></div>

</div>
	</td>
</tr>
</table>
<?php if ($nobox!=1) { ?></div><? } ?>
<script language="JavaScript">
select_create_list(select_names_<?=$name?>,select_values_<?=$name?>,'<?=$name?>','<?=$formname?>');
select_hide_list('<?=$name?>');
</script>