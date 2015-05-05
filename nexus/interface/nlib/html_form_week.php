<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 06/10/2006					*
	*																*
	****************************************************************/

$value = explode(",",$value);

$days = explode(",","Mon,Tue,Wed,Thu,Fri,Sat,Sun");
$daystr = array(_("Mon"),_("Tue"),_("Wed"),_("Thu"),_("Fri"),_("Sat"),_("Sun"));

?>
<script language="JavaScript">
	// var textbox_value_<?=$name?> = '<?=$value?>';
	// form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	// form_<?=$formname?>_types[<?=$itemcount?>]='week';
</script>
<table cellspacing=0 cellpadding=0 border=0><tr>
	<td><?php
		foreach ($days as $k=>$day) {
			if (in_array($day,$value)) { $checked="checked"; } else { $checked=""; }
		?>
		<input type="checkbox" <?=$checked?> name="<?=$name?>_<?=$day?>" id="<?=$name?>_<?=$day?>" value="1"><label 
		for="<?=$name?>_<?=$day?>"><span 
		style="font-size:10px;font-face:Verdana;color:white;"><?=$daystr[$k]?></span></label>
		<?php } ?>
	</td>
	<td><!-- mascara -->
	</td>
</tr></table>