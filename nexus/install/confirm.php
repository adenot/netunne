<?php
	$step=5;
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/top.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/hd_list.php");

?>
<table width="80%" align="center" class=description>
<tr>
	<td colspan="2">
		<?=_("The following HD(s) will be formatted:")?>
	</td>
</tr>
<tr>
	<td colspan="2">
<?php

	$sys = explode("_", $_SESSION['hdsys']);
	$hdsys=$sys[0];
	for ($i=0;$i<count($arrayHDFinal);$i++)
		if ($arrayHDFinal[$i][0]==$hdsys)
			$hd=$arrayHDFinal[$i];

?>
<br>
<table width=200 align=center class=description cellspacing=0 cellpadding=4 bgcolor="#DFDFDF">
<?php
	echo 	"<tr><td><b>"._("ID").":</b></td><td>".$hd[0]."</td></tr>\n".
		"<tr><td><b>"._("Model").":</b></td><td>".$hd[1]."</td></tr>\n".
		"<tr><td><b>"._("Size").":</b></td><td>".$hd[2] ." GB</td></tr>\n";
?>
</table>
<?php
	if ($arrayFoundData) {
		foreach ($arrayFoundData as $hddata)
			if ($hd[0]."3" == $hddata)
				$found=1;
	}

	// CHEAT CODE:
	// se tiver cleanhd, vai apagar de qualquer jeito
	if (file_exists("/etc/cleanhd")) {
		unset($found);
		$cleanhd=1;
	}

?>
	<p align=center class=attention<?=$found?>>
	<?php
		if ($found==1) {
			$part="no";
			echo sprintf(_("Previous %s configuration and data were found in a harddisk partition."),PRODNAME);
			echo "<BR><BR>";
			echo _("This installer will format ONLY the system partitions, so the configuration and data can be recovered later.");
		} else {
			$part="yes";
			echo sprintf(_("No previous %s configuration and data were found."),PRODNAME);
			echo "<BR><BR>";
			echo _("All partitions in this harddisk will be formatted and ALL data will be lost.");

			if ($cleanhd==1) {
				echo "<BR><BR>";
				echo _("*cleanhd* cheat code detected");
			}
		}
	?>
	</p>
	<p align=center class=description>
		<?=_("Confirm operation?")?>
	</p>

	</td>
</tr>
</table>
<form action="install.php" method="POST">
<input type=hidden name="part" value="<?=$part?>">
<p align=center>

		<input type="button" onclick="location.href='installb.php'" class="button-left" value="<?=_("Cancel")?>">
		&nbsp;&nbsp;
		&nbsp;&nbsp;
		<input type="submit" class="button-right" value="<?=_("Confirm")?>">
</p>
</form>
<?php
	require_once("include/bottom.php");
?>
