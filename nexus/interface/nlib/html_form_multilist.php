<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 30/05/2006					*
	*																*
	****************************************************************/

if ($showdivonly) {
?>
	<!-- DIV:<?=$name?> -->
	<div id="multibox_item_<?=$name?>" 
		style="
		width:100%;
		height:20px;
		padding: 0px 5px 0px 10px;
		background-color: #ffffff;
		font-family: Verdana;
		font-size: 8pt;
		cursor: default;
		<? if ($mark==1) { ?>background-color: #D6DBE6;<? } ?>
		"
		onmouseover="this.style.backgroundColor='#A4A7AE';this.childNodes[1].style.visibility='visible';"
		onmouseout="this.style.backgroundColor='#ffffff';this.childNodes[1].style.visibility='hidden';"
		><div style="position:absolute; padding:2px;0px;2px;0px;"><?=htmlentities($label)?>
		</div><img onclick="multilist_remove('<?=$name?>','<?=$value?>','<?=htmlentities($label)?>',this.parentNode.parentNode.id,this.parentNode);" 
			style="cursor:pointer;position:relative;left:194px;top:4px;visibility:hidden"
			 width=12 height=12 src="<?=DIRIMG?>multilist_remove.gif">
		</div>
	<!-- ENDDIV -->
<?php
	exit();
}
?>
<script language="JavaScript">
	var multilist_value_<?=$name?> = '<?=$value?>';
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='multilist';
</script>
<input type="hidden" id="multilist_hidden_<?=$name?>" name="<?=$name?>" value="">
<div id="multilistdiv_<?=$name?>" class="multilistdiv multilistdiv_normal">
<table cellspacing=0 cellpadding=0 border=0>
<tr>
	<td>
	<script language="JavaScript">
		var multilist_names_<?=$name?>=new Array();
		var multilist_values_<?=$name?>=new Array();
		var multilist_original_<?=$name?>=new Array();
<?php
	$i=0;
	foreach ($values as $k=>$v) {
?>
		multilist_names_<?=$name?>[<?=$i?>]='<?=$k?>';
		multilist_values_<?=$name?>[<?=$i?>]='<?=$v?>';
		multilist_original_<?=$name?>[<?=$i?>]='<?=$k?>';
		var multilist_selected_name_<?=$name?>="";
		var multilist_selected_value_<?=$name?>="";
		var multilist_selected_<?=$name?>=-1;
		var multilist_tid_<?=$name?>;
<?php
		$i++;
	}
?>
	</script>
<div id="multilist_all_div_<?=$name?>" class="multilist_all_div" >
<table cellspacing=0 cellpadding=0 border=0>
	<tr><td>
		<input type="textbox" name="multilist_textbox_<?=$name?>" id="multilist_textbox_<?=$name?>"
		class="multilist_textbox"
		autocomplete="off"
		onfocus="multilist_textbox_focus('<?=$name?>');"
		onclick="multilist_showhide_list('<?=$name?>');"
		onblur="multilist_textbox_blur('<?=$name?>');"
		></td>
		<td><img src="<?=DIRIMG?>multilist_list.gif" onclick="multilist_showhide_list('<?=$name?>');"></td>
	</tr>
</table>
<div id="multilist_all_<?=$name?>"
	style="	position:absolute;
			clear:left;
			font-family: Verdana;
			font-size: 8pt;
			width:236px;
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
<script language="JavaScript">
addEvent(document.getElementById("multilist_textbox_<?=$name?>"),"keydown","multilist_all_updown",'<?=$name?>');
addEvent(document.getElementById("multilist_textbox_<?=$name?>"),"keyup","multilist_all_keyup",'<?=$name?>');
multilist_show_list(multilist_names_<?=$name?>,multilist_values_<?=$name?>,'<?=$name?>');
multilist_hide_list('<?=$name?>');

document.getElementById("multilist_hidden_<?=$name?>").value="";
<?php
	$value=explode(";",$value);
	foreach ($value as $k) {
		if (trim($k)=="") { continue; }
?>
		multilist_add('<?=$name?>','<?=$k?>');
<?php
	}
?>
</script>
</div>
	</td>
</tr>
<tr>
	<td>
<div id="multilist_box_<?=$name?>" class="multilist_box"
	id="multlist_<?=$name?>">
</div>
	</td>
</tr>
</table>
</div>
<?=$js?>
