<?php
	$step=1;
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/top.php");
?>
<table width="50%" align=center border=0 cellspacing=0 cellpadding=0><tr><td>
<p>&nbsp;</p>
<p align=center class=title>Welcome to <?=constant("PRODNAME")?> Installation</p>

<p align=justify class=description>Next few steps will guide you through the process of installation of your server.</p>

<p>&nbsp;</p>

<form name="hd_info" method="POST" action="act_install.php?pag=lang">
<p align=center class=description>Please select your language<BR><BR>

<select name="language" style="">
	<option value="en">English</option>
	<option value="pt_BR">Portugu&ecirc;s do Brasil</option>
</select>
</p>

<p align=right>
<input type="submit" class="button-right" value="proceed">
</p>

</form>

</td></tr></table>

<?php
	require_once("include/bottom.php");
?>
