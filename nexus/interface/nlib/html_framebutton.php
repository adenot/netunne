<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 28/06/2006					*
	*																*
	****************************************************************/

//unset($animation);
?>
<SCRIPT language="JavaScript" src="<?=DIRJS?>table.js"></SCRIPT>
<div style="overflow:hidden;height:42px;width:582px;">
	<div id="framebutton_<?=$name?>" class="framebutton_div" style="position:relative;<?= ($animate) ? "top:-38px;" : "" ?>width:582px;">

		<table width="582" cellspacing=0 cellpadding=0 border=0
			bgcolor="#1E3560">
			<tr>
				<td width="8" valign=top><img src="<?=DIRIMG?>frame_topleft.gif"></td>
				<td width="566" colspan=2>
					<div style="height:1px; overflow:hidden;">
					<img src="<?=DIRIMG?>dot.gif" width="566" height="1">
					</div>
				</td>
				<td width="8" valign=top><img src="<?=DIRIMG?>frame_topright.gif"></td>
			</tr>
			<tr>
				<td colspan=4>
					<div id="framebutton_<?=$name?>_hideout" style="width:100%;height:100%;<?= ($animate) ? "background-color:#1E3560;" : ""?>">
					<table width="100%" cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td width="8" valign=top></td>
				<td width="455">
					<div style="overflow:hidden">
					<table
						cellspacing=0 cellpadding=0 border=0>
						<tr><? if ($icon) { ?><td id="framebutton_<?=$name?>_1"><img 
							src="<?=DIRIMG?>status_<?=$icon?>.gif"></td><? }?>
							<td width="100%"><nobr><span style="cursor:help;"
							onmouseover="tooltip_show('<?=$help?>');"
							onmouseout="tooltip_hide();"
							class="framebutton_text"><?=$title?></span></nobr></td>
						</tr>
					</table>
					</div>
				</td>
				<td width="111" id="framebutton_<?=$name?>_2"><? if ($action) { ?>
					<input value="<?=$buttontext?>" type="button" 
						onclick="<? if (!$act_link) { ?>table_framelog('<?=$logtitle?>','<?=$action?>');<? 
									} else { ?>table_do_act('','<?=$action?>');<? } ?>"
						class="frame_greenbutton"><? } ?>
				</td>
				<td width="8" valign=top ></td>
			</tr>

					</table>
					</div>
				</td>
			</tr>		
			<tr>
				<td width="8" valign=bottom><img src="<?=DIRIMG?>frame_bottomleft.gif"></td>
				<td width="566" colspan=2>
					<div style="height:1px; overflow:hidden;">
					<img src="<?=DIRIMG?>dot.gif" width="566" height="1">
					</div>
				</td>
				<td width="8" valign=bottom><img src="<?=DIRIMG?>frame_bottomright.gif"></td>
			</tr>
		</table>
	</div>
</div>
<div style="height:20px"></div>
<?php if ($animate) { ?>
<script>framebutton_animate("<?=$name?>");</script>
<?php } ?>