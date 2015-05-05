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
	var list_value_<?=$name?> = '<?=$value?>';
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='list';
</script>
<div id="listdiv_<?=$name?>" class="listdiv listdiv_normal">
  <select 
  	onchange="list_onchange('<?=$name?>','<?=$formname?>'); form_renew_<?=$formname?>(); <?=$js?>"
    name="<?=$name?>" id="list_<?=$name?>"
    size="1" class="list">
<?php
	foreach ($values as $k=>$v) {
		if ($k==$value) { $s="selected"; } else { $s=""; }
?>
    <option <?=$s?> value="<?=$k?>"><?=$v?></option>
<?php
	}
?>
  </select>
</div>
  
