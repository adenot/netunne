<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 27/05/2006					*
	*																*
	****************************************************************/


?>
</td>
			<td width="8" bgcolor="#1E3560"></td>
		</tr>
		<tr>
			<td width="8" valign=top bgcolor="#D6F1B9"><img src="<?=DIRIMG?>frame_bottomleft.gif"></td>
			<td width="566" background="<?=DIRIMG?>frame_status_back.gif">
				<div id="div1" style="width:566px;height:30px;overflow:hidden">
				<table
					align="right"
					height=30 cellspacing=0 cellpadding=0 border=0 
					bgcolor="#D6F1B9">
					<tr>
						<td width=48 bgcolor="#D6F1B9"><img src="<?=DIRIMG?>frame_status_left.gif"></td>
						<td bgcolor="#1E3560">
							<div style="width:246px;" id="frame_<?=$formname?>_status" class="frame_status">
								<iframe src="/_engine/act.php" scrolling="no" frameborder="0" name="iframe_<?=$formname?>" id="iframe_<?=$formname?>" width="246" height="30"></iframe>
							</div>
						</td>
						<td align=center bgcolor="#1E3560" width=124>
							<input value="<?=$buttontext?>" type="button" 
								onclick="
								document.getElementById('frame_<?=$formname?>_status').style.display='block';
								document.getElementById('form_<?=$formname?>').submit();"
								class="frame_greenbutton">
						</td>
					</tr>
				</table>
				</div>
			</td>
			<td width="8" bgcolor="#1E3560" valign=bottom><img src="<?=DIRIMG?>frame_bottomright.gif"></td>
		</tr>
	</table>
</div>
<div style="height:20px"></div>