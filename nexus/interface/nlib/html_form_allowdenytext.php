<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 31/08/2007					*
	*																*
	****************************************************************/


?>
<script language="JavaScript">
	var textarea_value_<?=$name?> = '<?=str_replace("\n","\\n",$value)?>';
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='textarea_big';
		

</script>
<div style="padding:1px 0px 4px 1px">
<a class="tablecell_form_dynamiclink" href="javascript:textarea_big_div_showhide('<?=$name?>');"><?=_("Edit List")?></a>
</div>
<div id="textarea_big_div_<?=$name?>" style="visibility:hidden;display:none;overflow:hidden;">
<table cellspacing=0 cellpadding=0 border=0><tr>
	<td><span class="textarea_big_caption"><?=$denytext?></span><BR>
<textarea type="text" class="textarea_big"
	name="<?=$name?>_deny" id="textarea_deny_<?=$name?>" 
	onfocus="textarea_big_onfocus('<?=$name?>');"
	onkeydown="textarea_big_onchange('<?=$name?>','<?=$formname?>');"
	onblur="textarea_big_onblur('<?=$name?>','<?=$formname?>');"
	><?=$value[deny]?></textarea>
	</td>
	<td><!-- mascara -->
	</td>
</tr>
<tr>
	<td><span class="textarea_big_caption"><?=$allowtext?></span><BR>
<textarea type="text" class="textarea_big" style="height:100px"
	name="<?=$name?>_allow" id="textarea_allow_<?=$name?>" 
	onfocus="textarea_big_onfocus('<?=$name?>');"
	onkeydown="textarea_big_onchange('<?=$name?>','<?=$formname?>');"
	onblur="textarea_big_onblur('<?=$name?>','<?=$formname?>');"
	><?=$value[allow]?></textarea>
	</td>
	<td><!-- mascara -->
	</td>
</tr>
</table>
</div>