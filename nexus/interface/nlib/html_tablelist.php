<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 6, 2006					*
	*																*
	****************************************************************/

?>
<script>
var tablelist_<?=$name?>_short=new Array();
var tablelist_<?=$name?>_lastresponse;
<?php $id=0; 
	foreach ($data as $dat) { $id++ ?>
tablelist_<?=$name?>_short[<?=$id?>]='<?=str_replace("\n","\\n",$dat[short])?>';
<?    } ?>
</script>
<!-- TABLELIST -->
<table width="100%" class="table_frame" cellspacing=0 cellpadding=0 border=0>
	<tr height="10">
		<td valign=top><img src="<?=DIRIMG?>table_topleft.gif" width=9 height=8></td>
		<td><img src="<?=DIRIMG?>dot.gif" width="548" height="1"></td>
		<td valign=top align=right><img src="<?=DIRIMG?>table_topright.gif" width="9" height="8"></td>
	</tr>
	<tr>
		<td></td>
		<td><div id="table_<?=$name?>"></div>
		</td>
		<td></td>
	</tr>
	<tr height="10">
		<td valign=bottom><img src="<?=DIRIMG?>table_bottomleft.gif" width=9 height=8></td>
		<td><img src="<?=DIRIMG?>dot.gif" width="548" height="1"></td>
		<td style="text-align:right" valign=bottom align=right><img src="<?=DIRIMG?>table_bottomright.gif" width=9 height=8></td>
	</tr>
</table>
<SCRIPT language="JavaScript" src="<?=DIRJS?>table.js"></SCRIPT>
<script language="JavaScript">
	tablelist_load("<?=$name?>","<?=$autorefresh?>");
</script>
<!-- / TABLELIST -->
