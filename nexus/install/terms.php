<?php
	$step=2;	
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/top.php");

?>
<table width="80%" align="center">
<tr>
	<td align="center">
	<p>&nbsp;</p>
	<p class="description">
		<?=_("Before proceeding, you must accept to the following Terms of Use.")?>
	</p>
		<iframe style="border:1px solid #C0C0C0;" src="include/termsfile.php?langfile=<?=urlencode($terms)?>" height="240" width="100%" frameborder=0 scrolling=auto></iframe>

	<br><br>
		<input type="button" class="button-left" onclick="location.href='welcome.php'" value="<?=_("Decline")?>">
		&nbsp;&nbsp;
		&nbsp;&nbsp;
		<input type="button" class="button-right" onclick="location.href='installa.php'" value="<?=_("Accept")?>">
	</td>
</tr>
</table>


<?php
	require_once("include/bottom.php");
?>
