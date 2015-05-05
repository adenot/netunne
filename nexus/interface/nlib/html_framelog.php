<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 28/06/2006					*
	*																*
	****************************************************************/


?>
<div id="framelog" style="width:582px;<? if (!$details) { ?>visibility:hidden;<? } ?>">
	<table width="582" cellspacing=0 cellpadding=0 border=0
		bgcolor="#1E3560">
		<tr>
			<td width="8" valign=top><img src="<?=DIRIMG?>frame_topleft.gif"></td>
			<td width="566">
				<div style="height:1px; overflow:hidden;">
				<img src="<?=DIRIMG?>dot.gif" width="566" height="1">
				</div>
			</td>
			<td width="8"valign=top><img src="<?=DIRIMG?>frame_topright.gif"></td>
		</tr>
		<tr>
			<td width="8" valign=top></td>
			<td width="566">
				<div>
				<table width="100%"
					cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td width="545"><nobr><span class="framebutton_text"><?=$logtitle?></span></nobr></td>
						<td><a href="javascript:unblackout();location.href='?';"><img src="<?=DIRIMG?>framelog_close.gif" border=0></a></td>
					</tr>
					<tr><td><img src="<?=DIRIMG?>dot.gif" height=8></td></tr>
				</table>
				</div>
				<table width="100%" class="table_frame" cellspacing=0 cellpadding=0 border=0>
					<tr height="10">
						<td valign=top><img src="<?=DIRIMG?>table_topleft.gif" width=9 height=8></td>
						<td><img src="<?=DIRIMG?>dot.gif" width="548" height="1"></td>
						<td valign=top align=right><img src="<?=DIRIMG?>table_topright.gif" width="9" height="8"></td>
					</tr>
					<tr>
						<td></td>
						<td><iframe frameborder="0" src="/_engine/if_log.php?wall=<?=$wall?>&action=<?=$action?>" width="100%" height="245"></iframe></td>
						<td></td>
					</tr>
					<tr height="10">
						<td valign=bottom><img src="<?=DIRIMG?>table_bottomleft.gif" width=9 height=8></td>
						<td><img src="<?=DIRIMG?>dot.gif" width="548" height="1"></td>
						<td style="text-align:right" valign=bottom align=right><img src="<?=DIRIMG?>table_bottomright.gif" width=9 height=8></td>
					</tr>
				</table>			
			</td>
			<td width="8" valign=top ></td>
		</tr>
		<tr>
			<td width="8" valign=bottom ><img src="<?=DIRIMG?>frame_bottomleft.gif"></td>
			<td width="566">
				<div style="height:1px; overflow:hidden;">
				<img src="<?=DIRIMG?>dot.gif" width="566" height="1">
				</div>
			</td>
			<td width="8"valign=bottom><img src="<?=DIRIMG?>frame_bottomright.gif"></td>
		</tr>
	</table>
</div>
<div id="frameuserlog" style="width:300px;position:absolute;top:70px;left:140px;<? if ($details) { ?>visibility:hidden;<? }?>">
	<table width="300" cellspacing=0 cellpadding=0 border=0
		bgcolor="#1E3560">
		<tr>
			<td width="8" valign=top><img src="<?=DIRIMG?>frame_topleft.gif"></td>
			<td width="284">
				<div style="height:1px; overflow:hidden;">
				<img src="<?=DIRIMG?>dot.gif" width="284" height="1">
				</div>
			</td>
			<td width="8"valign=top><img src="<?=DIRIMG?>frame_topright.gif"></td>
		</tr>
		<tr>
			<td width="8" valign=top></td>
			<td width="284">
				<div>
				<table width="100%"
					cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td width="263"><nobr><span class="framebutton_text"><?=$logtitle?></span></nobr></td>
						<td><a href="javascript:unblackout();location.href='?';"><img src="<?=DIRIMG?>framelog_close.gif" border=0></a></td>
						<tr><td><img src="<?=DIRIMG?>dot.gif" height=8></td></tr>
					</tr>
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
						<td><iframe scrolling="no" frameborder="0" src="/_engine/if_userlog.php?wall=<?=$wall?>&action=<?=$action?>" width="100%" height="90"></iframe></td>
						<td></td>
					</tr>
					<tr height="10">
						<td valign=bottom><img src="<?=DIRIMG?>table_bottomleft.gif" width=9 height=8></td>
						<td><img src="<?=DIRIMG?>dot.gif" width="266" height="1"></td>
						<td style="text-align:right" valign=bottom align=right><img src="<?=DIRIMG?>table_bottomright.gif" width=9 height=8></td>
					</tr>
				</table>			
			</td>
			<td width="8" valign=top ></td>
		</tr>
		<tr>
			<td width="8" valign=top></td>
			<td width="284">
				<table id="userlog_buttons" style="padding-top:3px;padding-bottom:3px;display:none;" width="284" align=center cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td colspan=2><div style="height:1px;overflow:hidden"><img src="<?=DIRIMG?>dot.gif" width="284" height="0"></div></td>
					</tr>
					<tr>
						<td align=center><input value="<?=_("View Details")?>" type="button" 
							onclick="document.getElementById('frameuserlog').style.visibility='hidden';
									 document.getElementById('framelog').style.visibility='visible';
								    "
							class="frame_greenbutton">
						</td>
						<td align=center><input value="<?=_("Ok")?>" type="button" 
							onclick="unblackout();location.href='?';
							"
							class="frame_greenbutton">
						</td>
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