<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 30/05/2006					*
	*																*
	****************************************************************/


?>
<HTML><HEAD>
<STYLE>
	/****************** IFRAME de STATUS ******************/
	
	.status_body {
		background-color: #1e3560; 
		height: 100%;
		margin: 0;
		padding: 0;
	}
	.status_text {
		vertical-align:middle;
		text-align:right;
		font-family: Verdana;
		color: #FFFFFF;
		font-size: 7pt;
	}
</STYLE>
</HEAD>
<BODY class="status_body">
<?php
	if ($status=="wait") {
?>
<table align="right" height="100%" border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td class="status_text"><?=$t?></td>
		<td><img src="<?=DIRIMG?>dot.gif" width="10"></td>
		<!--<td><img src="<?=DIRIMG?>status_wait.gif"></td>-->
	</tr>
</table>
<? } else if ($status=="ok") { ?>
<table align="right" height="100%" border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td class="status_text"><?=$t?></td>
		<td><img src="<?=DIRIMG?>dot.gif" width="10"></td>
		<td><img src="<?=DIRIMG?>status_ok.gif"></td>
	</tr>
</table>
<script>
	parent.form_release('<?=$formname?>');
</script>
<? } else if ($status=="fail") { ?>
<table align="right" height="100%" border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td class="status_text"><?=$t?></td>
		<td><img src="<?=DIRIMG?>dot.gif" width="10"></td>
		<td><img src="<?=DIRIMG?>status_fail.gif"></td>
	</tr>
</table>
<? } ?>
</BODY></HTML>