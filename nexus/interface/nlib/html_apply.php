<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 28/06/2006					*
	*																*
	****************************************************************/


?>
<div id="frameuserlog" style="width:300px;position:absolute;top:70px;left:140px;">
	<table width="300" cellspacing=0 cellpadding=0 border=0
		bgcolor="#1E3560">
		<tr>
			<td width="8" valign=top><img src="<?=DIRIMG?>frame_topleft.gif"></td>
			<td width="284"><div 
				style="height:1px; overflow:hidden;"><img 
				src="<?=DIRIMG?>dot.gif" width="284" height="1"></div></td>
			<td width="8" valign=top><img src="<?=DIRIMG?>frame_topright.gif"></td>
		</tr>
		<tr>
			<td width="8" valign=top></td>
			<td width="284" align=center>
				<div>
				<table width="100%"
					cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td width="263"><nobr><span class="framebutton_text"><?=_("Apply Changes")?></span></nobr></td>
						<td><a href="javascript:unblackout();"><img src="<?=DIRIMG?>framelog_close.gif" border=0></a></td>
					</tr>
					<tr><td><img src="<?=DIRIMG?>dot.gif" height=8></td></tr>
				</table>
				</div>
				<table width="100%" class="table_frame" cellspacing=0 cellpadding=0 border=0>
					<tr height="10">
						<td valign=top><img src="<?=DIRIMG?>table_topleft.gif" width=9 height=8></td>
						<td><img src="<?=DIRIMG?>dot.gif" width="266" height="1"></td>
						<td valign=top align=right><img src="<?=DIRIMG?>table_topright.gif" width="9" height="8"></td>
					</tr>
					<tr>
						<td></td>
						<td><div
						 	style="overflow:auto;height:200px">
						 	<span style="font-family:Verdana;font-size:13px;font-weight: bold;"><?=_("Last changes:")?><HR size=1 border=1></span>
						 	<span style="font-family:Verdana;font-size:11px;"><?=$actions?></span><HR size=1 border=1>
						 	<span style="font-family:Verdana;font-size:13px;font-weight: bold;"><?=_("Apply this changes now?")?></span>
						 </div></td>
						<td></td>
					</tr>
					<tr height="10">
						<td valign=bottom><img src="<?=DIRIMG?>table_bottomleft.gif" width=9 height=8></td>
						<td><img src="<?=DIRIMG?>dot.gif" width="266" height="1"></td>
						<td style="text-align:right" valign=bottom align=right><img src="<?=DIRIMG?>table_bottomright.gif" width=9 height=8></td>
					</tr>
				</table>			
			</td>
			<td width="8" valign=top></td>
		</tr>
		<tr>
			<td width="8" valign=top></td>
			<td width="284">
				<table id="userlog_buttons" style="padding-top:3px;padding-bottom:3px;" width="284" align=center cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td colpspan=2><div style="height:1px;overflow:hidden"><img src="<?=DIRIMG?>dot.gif" width="284" height="0"></div></td>
					</tr>
					<tr>
						<td align=center><input value="<?=_("Apply!")?>" type="button" 
							onclick="table_framelog('<?=_("Applying...")?>','MERGE');"
							class="frame_greenbutton">
						</td><!--
						<td align=center><input value="<?=_("Discard")?>" type="button" 
							onclick="table_framelog('<?=_("Discarding...")?>','DISCARD');"
							class="frame_greenbutton">
						</td>-->
					</tr>
				</table>
			</td>
			<td width="8" valign=top ></td>
		</tr>
			
		<tr>
			<td width="8" valign=bottom ><img src="<?=DIRIMG?>frame_bottomleft.gif"></td>
			<td width="284">
				<div style="height:1px; overflow:hidden;">
				<img src="<?=DIRIMG?>dot.gif" width="284" height="1">
				</div>
			</td>
			<td width="8"valign=bottom><img src="<?=DIRIMG?>frame_bottomright.gif"></td>
		</tr>
	</table>
</div>