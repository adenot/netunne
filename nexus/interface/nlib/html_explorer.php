<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Dez 21, 2006					*
	*																*
	****************************************************************/



?>
<!-- EXPLORER -->
<table width="100%" class="table_frame" cellspacing=0 cellpadding=0 border=0>
	<tr height="10">
		<td valign=top bgcolor="#707786"><img src="<?=DIRIMG?>form_topleft.gif" width=9 height=8></td>
		<td bgcolor="#707786"><img src="<?=DIRIMG?>dot.gif" width="141" height="1"></td>
		<td bgcolor="#1E3560"><img src="<?=DIRIMG?>dot.gif" width="2" height="1"></td>
		<td valign=top align=right><img src="<?=DIRIMG?>dot.gif" width="405" height="1"><img src="<?=DIRIMG?>form_topright.gif" width="9" height="8"></td>
	</tr>
	<tr height="10" id="tr_sep_<?=$item[$i][name]?>">
		<td bgcolor="#707786"></td>
		<td class="tablecell_form_label"><div id="explorer_menu_<?=$name?>"></div></td>
		<td class="tablecell_form_space"></td>
		<td class="tablecell_form_item" style="background-color:#A4A7AE"><div id="explorer_content_<?=$name?>"></div></td>
	</tr>
	<tr height="10">
		<td valign=bottom bgcolor="#707786"><img src="<?=DIRIMG?>form_bottomleft.gif" width=9 height=8></td>
		<td bgcolor="#707786"></td>
		<td class="tablecell_form_space"></td>
		<td style="text-align:right" valign=bottom align=right><img src="<?=DIRIMG?>form_bottomright.gif" width=9 height=8></td>
	</tr>
</table>
<SCRIPT language="JavaScript" src="<?=DIRJS?>table.js"></SCRIPT>
<script language="JavaScript">
	//tablelist_load("<?=$name?>","<?=$autorefresh?>");
	explorer_load("/_engine/jx_explorer.php?act=getmenu","explorer_menu_<?=$name?>");

</script>
<!-- / EXPLORER -->