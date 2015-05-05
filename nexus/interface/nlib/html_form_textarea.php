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
	var textarea_value_<?=$name?> = '<?=str_replace("\n","\\n",$value)?>';
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='textarea';
</script>
<table cellspacing=0 cellpadding=0 border=0><tr>
	<td>
<textarea type="text" class="textarea"
	name="<?=$name?>" id="textarea_<?=$name?>" 
	onfocus="textarea_onfocus('<?=$name?>');"
	onkeydown="textarea_onchange('<?=$name?>','<?=$formname?>');"
	onblur="textarea_onblur('<?=$name?>','<?=$formname?>');"
	><?=$value?></textarea>
	</td>
	<td><!-- mascara -->
	</td>
</tr></table>