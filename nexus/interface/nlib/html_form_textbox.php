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
<table cellspacing=0 cellpadding=0 border=0><tr>
	<td>
<input type="text" class="textbox"
	name="<?=$name?>" id="textbox_<?=$name?>" value="<?=$value?>" 
	onfocus="textbox_onfocus('<?=$name?>');"
	onkeydown="textbox_onchange('<?=$name?>','<?=$formname?>');"
	onblur="textbox_onblur('<?=$name?>','<?=$formname?>');"
	maxlength="<?=$this->imaxlength?>"
	>
	</td>
	<td><!-- mascara -->
	</td>
</tr></table>