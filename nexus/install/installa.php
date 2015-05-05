<?php
	$step=3;
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/top.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/hd_list.php");
?>
<script>
function hl(TR) {
	document.getElementById(TR).style.backgroundColor='#EFEFEF';
}
function hl2(TR) {
	document.getElementById(TR).style.backgroundColor='white';
}
</script>
<form name="hd_list" method="POST" action="act_install.php?pag=1">
<table align="center" width="90%" class=description>
<?php
	if (count($arrayListaHD)==0||$arrayListaHD[0]=="") {
?>
<tr height=20><td></td></tr>
<tr height=30>
	<td class=attention align=center bgcolor="#DFDFDF">
		<?=_("No Harddisks found in your computer!")?>
	</td>
</tr>
<tr>
	<td class=attention align=center>
<br>
<?=_("This could have happened because the harddisk was not correctly installed or it's not supported by this software.")?>
<br><br><br>
<?=_("The installation process was interrupted")?>
	</td>
</tr>
<?php
	} else {
?>

<tr height=50>
	<td>
		<p class=description align=center>
		<?=_("Pick a HD to install your server.")?>
		</p>
	</td>
</tr>
<tr><td><table class=description width="100%" cellspacing=0 cellpadding=4 align="center">
	<tr>
		<td bgcolor="#DFDFDF" colspan="4" align="center">
			<b><?=_("HardDisks found in your computer")?></b>
		</td>

	</tr>
	<tr>
		<td align="center" width="20%"><b>
			<?=_("ID")?>
		</b></td>
		<td align="center" width="30%"><b>
			<?=_("Model")?>
		</b></td>
		<td align="center" width="20%"><b>
			<?=_("Size")?>
		</b></td>
		<td align="center" width="15%"><b>
			<?=_("Selected")?>
		</b></td>
	</tr>

<?php
	foreach ($arrayHDFinal as $hd) {
		echo "<tr id=hd$i onclick=\"document.getElementById('".$hd[0]."_sys').checked=true\" onmouseover='hl(\"hd$i\");' onmouseout='hl2(\"hd$i\");'>";
		$i++;
		echo "<td align=\"center\">". $hd[0] . 
		"</td><td align=\"center\">". $hd[1] . 
		"</td><td align=\"center\">". $hd[2] . " GB".
		"</td><td align=\"center\"> <input type=\"radio\" id=\"".$hd[0]."_sys\" name=\"sys\" value=\"".$hd[0]."_sys\" ".iif($_SESSION['hdsys']==$hd[0]."_sys", "checked", "").">
		</td></tr>";
	}
		//</td><td align=\"center\"> <input type=\"radio\" id=\"".$hd[0]."_data\" name=\"data\" value=\"".$hd[0]."_data\" ".iif($_SESSION['hddata']==$hd[0]."_data", "checked", "").">
	if(!$_SESSION['hdsys']) {
		echo "	<script language=\"Javascript\">hd_list." .
				$arrayHDFinal[0][0]."_sys.checked=true;
				</script>"; 
	}
			
?>
</tr>
<tr height=2><td colspan=5><table width="100%" height=2 cellspacing=0 cellpadding=0 border=0 bgcolor="#DFDFDF"><tr><td></td></tr></table></td></tr>
</table>

<p align=center class=description>
<?sprintf(_("If previous %s configuration and data are found in this harddisk, they will be preserved."),PRODNAME)?>
</p>
<p align=center>
<input type="button" onclick="location.href='terms.php';" class="button-left" value="<?=_("Back")?>" name="back">
&nbsp;&nbsp;
&nbsp;&nbsp;
<input type="button" onclick="form.submit();" value="<?=_("Proceed")?>" name="proceed" class="button-right">
</p>
</td></tr>

<?php
}
?>
</table>
<?php
	require_once("include/bottom.php");

?>
