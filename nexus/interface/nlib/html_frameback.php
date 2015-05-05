<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 28/06/2006					*
	*																*
	****************************************************************/


?>
<div id="framebutton_<?=$name?>" class="framebutton_div" style="width:582px;">
	<table width="582" cellspacing=0 cellpadding=0 border=0
		bgcolor="#1E3560">
		<tr>
			<td width="8" valign=top><img src="<?=DIRIMG?>frame_topleft.gif"></td>
			<td width="566" colspan=2>
				<div style="height:1px; overflow:hidden;">
				<img src="<?=DIRIMG?>dot.gif" width="566" height="1">
				</div>
			</td>
			<td width="8"valign=top><img src="<?=DIRIMG?>frame_topright.gif"></td>
		</tr>
		<tr>
			<td width="8" valign=top></td>
			<td width="111" align=center>
				<input value="<?=$buttontext?>" type="button" 
					onclick="goMenu('<?=$link?>');"
					class="frame_greenbutton">
			</td>
			<td width="455">
			</td>
			<td width="8" valign=top ></td>
		</tr>
		<tr>
			<td width="8" valign=bottom><img src="<?=DIRIMG?>frame_bottomleft.gif"></td>
			<td width="566" colspan=2>
				<div style="height:1px; overflow:hidden;">
				<img src="<?=DIRIMG?>dot.gif" width="566" height="1">
				</div>
			</td>
			<td width="8"valign=bottom><img src="<?=DIRIMG?>frame_bottomright.gif"></td>
		</tr>
	</table>
</div>
<div style="height:20px"></div>