<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 30/05/2006					*
	*																*
	****************************************************************/

/*******************************
 *  OBSOLETO !!!!!
 */

$value = explode(";",$value);
foreach ($values as $k=>$v) {
	if (in_array($k,$value)) {
		$value2[$k]=$v;
	} else {
		$value1[$k]=$v;
	}
}

?>
<script language="JavaScript">
	var textbox_value_<?=$name?> = '<?=$value?>';
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='duallist';
</script>
<table cellspacing=0 cellpadding=0 border=0>
	<tr>
		<td class="duallist_title">Available</td>
		<td></td>
		<td class="duallist_title">Selected</td>
	</tr>
	<tr>
		<td>
			<select name="name" size="5" class="duallist" style="background-color: #A4A7AE;">
			<?php 
				foreach ($value1 as $k=>$v) {
			?>
				<option value="<?=$k?>"><?=$v?></option>
			<?php } ?>
			</select>
		</td>
		<td width=41>
		</td>
		<td>
			<select name="name" size="5" class="duallist">
			<?php 
				foreach ($value2 as $k=>$v) {
			?>
				<option value="<?=$k?>"><?=$v?></option>
			<?php } ?>			</select>
		</td>
	</tr>
</table>		
  