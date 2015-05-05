<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 29/05/2006					*
	*																*
	****************************************************************/
//January | February | March | April | May | June | July | August | September | October | November | December
	$type = $this->itype;
	
	$months = explode("|",_("January|February|March|April|May|June|July|August|September|October|November|December"));
	$years = range(2006,2036);
	$days = range(1,31);
	
	$hours = range(0,23);
	$minutes = range(0,59);
	
	//$date = date("n/j/Y");
	//$time = date("H:i");
	
	if ($type=="time") {
		$value = explode(":",$value);
		$hour = $value[0];
		$minute=$value[1];
	} else if ($type=="date") {
		$value = explode("/",$value);
		$year = $value[2];
		$month= $value[0];
		$day  = $value[1];
	}
	
	

?>
<script language="JavaScript">
	var textbox_value_<?=$name?> = '<?=$value?>';
	form_<?=$formname?>_fields[<?=$itemcount?>]='<?=$name?>';
	form_<?=$formname?>_types[<?=$itemcount?>]='<?=$type?>';
</script>
<table cellspacing=0 cellpadding=0 border=0>
<tr><td>
<? if ($type=="date") { ?>
<select name="<?=$name?>_month"
	style="width:80px;font-size:10px;font-face:Verdana;"
><?
	for($i=1;$i<=count($months);$i++) {
		if ($i==$month) { $sel = "selected"; } else { $sel=""; } 
?><option value="<?=$i?>" <?=$sel?>><?=$months[$i-1]?></option><?
	}
	?></select><select name="<?=$name?>_day"
	style="width:40px;font-size:10px;font-face:Verdana;"
><?
	for($i=1;$i<=count($days);$i++) {
		if ($days[$i-1]==$day) { $sel = "selected"; } else { $sel=""; } 
?><option value="<?=$days[$i-1]?>" <?=$sel?>><?=$days[$i-1]?></option><?
	}
	?></select><select name="<?=$name?>_year"
	style="width:60px;font-size:10px;font-face:Verdana;"
><?
	for($i=1;$i<=count($years);$i++) {
		if ($years[$i-1]==$year) { $sel = "selected"; } else { $sel=""; } 
?><option value="<?=$years[$i-1]?>" <?=$sel?>><?=$years[$i-1]?></option><?
	}
	?></select>
<? } else if ($type=="time") { ?>
<select name="<?=$name?>_hour"
	style="width:40px;font-size:10px;font-face:Verdana;"
><?
	for($i=1;$i<=count($hours);$i++) {
		if ($hours[$i-1]==$hour) { $sel = "selected"; } else { $sel=""; } 
?><option value="<?=$hours[$i-1]?>" <?=$sel?>><?=sprintf("%02s",$hours[$i-1])?></option><?
	}
	?></select><span style="font-size:10px;font-face:Verdana;color:white;"> h </span
	><select name="<?=$name?>_minute"
	style="width:40px;font-size:10px;font-face:Verdana;"
><?
	for($i=1;$i<=count($minutes);$i++) {
		if ($minutes[$i-1]==$minute) { $sel = "selected"; } else { $sel=""; } 
?><option value="<?=$minutes[$i-1]?>" <?=$sel?>><?=sprintf("%02s",$minutes[$i-1])?></option><?
	}
	?></select><span style="font-size:10px;font-face:Verdana;color:white;"> minutes </span
	>

<? } ?>
	</td>
	<td></td>
	</tr>
</table>
