<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 29/05/2006					*
	*																*
	****************************************************************/


?>
<script language="JavaScript">
	var textbox_value_<?=$name?> = '<?=$value?>';
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='textbox';
</script>
<table cellspacing=0 cellpadding=0 border=0>
<tr><td>
<input type="text" class="textbox"
	name="<?=$name?>" id="keybox_<?=$name?>" value="<?=$value?>" 
	maxlength="<?=$this->imaxlength?>"
	readonly
	style="width:217px"
	></td>
	<td><img style="padding:0px 0px 0px 3px;cursor:pointer;"
		src="<?=DIRIMG?>key_reload.gif"
		onmouseover="tooltip_show('<?=$keyhelp?>');"
		onmouseout="tooltip_hide();"
		onclick="getkey('<?=$name?>');"
		></td>
	</tr>
</table>
<script>
getkey('<?=$name?>');
</script>