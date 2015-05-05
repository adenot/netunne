<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 27/05/2006					*
	*																*
	****************************************************************/

$sh = $this->sh;

$final = array();
if (is_array($sh)) {
	foreach ($sh as $rule) {
		$cond = array();
		$code = array();
		
		// status=enable;type=external:address.show;netmask.show
		$tmp = explode(":",$rule);
		// tmp[0]status=enable;type=external tmp[1]address.show;netmask.show
		
		// tratando condicoes:
		$tmp_cond = explode(";",$tmp[0]);
		foreach ($tmp_cond as $c) {
			$tmp_c = explode("=",$c);
			// pode ser !alguma_coisa
			if (substr($tmp_c[1],0,1)=="!") {
				$tmp_c[1]=substr($tmp_c[1],1);
				$cond[] = "(document.getElementById('list_".$name."_".$tmp_c[0]."').value!='".$tmp_c[1]."')";
			} else {
				$cond[] = "(document.getElementById('list_".$name."_".$tmp_c[0]."').value=='".$tmp_c[1]."')";
			}
		}
		
		$tmp_code = explode(";",$tmp[1]);
		foreach ($tmp_code as $c) {
			$tmp_c = explode(".",$c);
			// $tmp_c[0]=nome_do_tr
			// $tmp_c[1]=show|hide
			$code[] = "list_".$tmp_c[1]."tr('".$tmp_c[0]."','".$name."');";
		}
		
		$fcond = implode(" && \n",$cond);
		$fcode = implode("\n",$code);
		
		$final[]="if ($fcond) { \n$fcode\n }";
		
	}
}

$ffinal = implode("\n",$final);

$javascript .= "\nfunction form_renew_$name() { $ffinal }\n";
$javascript .= "\nform_renew_$name();";

?>
<script language="JavaScript">
	var form_<?=$name?>_fields=new Array();
	var form_<?=$name?>_types=new Array();
	var form_<?=$name?>_allowsubmit=1;
	var form_<?=$name?>_fixed_show=new Array();
	var form_<?=$name?>_fixed_hide=new Array();

</script>
<table width="100%" class="table_form" id="<?=$name?>" cellspacing=0 cellpadding=0 border=0>
	<form method="post" id="form_<?=$name?>" target="iframe_<?=$name?>" action="/_engine/act.php?act=<?=$action?>" enctype="multipart/form-data">
	<input type="hidden" name="formname" value="<?=$name?>">
	<tr height="10">
		<td valign=top bgcolor="#707786"><img src="<?=DIRIMG?>form_topleft.gif" width=9 height=8></td>
		<td bgcolor="#707786"><img src="<?=DIRIMG?>dot.gif" width="141" height="1"></td>
		<td bgcolor="#1E3560"><img src="<?=DIRIMG?>dot.gif" width="2" height="1"></td>
		<td valign=top align=right><img src="<?=DIRIMG?>dot.gif" width="405" height="1"><img src="<?=DIRIMG?>form_topright.gif" width="9" height="8"></td>
	</tr>
	<?php
		for ($i=0;$i<count($item);$i++) {
			if ($item[$i][noshow]==1) {
				echo $item[$i][html];
				continue;
			}
	?>
	<!-- ITEM <?=$i?> -->
	<tr id="tr_<?=$item[$i][name]?>">
		<td bgcolor="#707786"></td>
		<td class="tablecell_form_label"><span style="width:100%;<? if (trim($item[$i][help])!="") { ?>cursor:help;<? } else {?>cursor:default;<? } ?>"
			onmouseover="tooltip_show('<?=$item[$i][help]?>','<?=$item[$i][name]?>',600,1);"
			onmouseout="tooltip_hide();"
			><?=$item[$i][label]?></span></td>
		<td class="tablecell_form_space"></td>
		<td class="tablecell_form_item"><div style="z-index:-1;height:100px;left:0px;position:absolute;float:left" id="<?=$item[$i][name]?>">q</div><?=$item[$i][html]?></td>
	</tr>
	<!-- FIM ITEM <?=$i?> -->
	<?php
			if ($i!=count($item)-1) {
	?>
	<!-- SEPARADOR <?=$i?> -->
	<tr height="10" id="tr_sep_<?=$item[$i][name]?>">
		<td bgcolor="#707786"></td>
		<td class="tablecell_form_label"></td>
		<td class="tablecell_form_space"></td>
		<td class="tablecell_form_item" style="background-color:#A4A7AE"></td>
	</tr>
	<!-- FIM SEPARADOR <?=$i?>  -->
	<?php
		 	} 
		}
	?>
	<tr height="10">
		<td valign=bottom bgcolor="#707786"><img src="<?=DIRIMG?>form_bottomleft.gif" width=9 height=8></td>
		<td bgcolor="#707786"></td>
		<td class="tablecell_form_space"></td>
		<td style="text-align:right" valign=bottom align=right><img src="<?=DIRIMG?>form_bottomright.gif" width=9 height=8></td>
	</tr>
	</form>
</table>
<script language="JavaScript">
<?=$javascript?>
</script>