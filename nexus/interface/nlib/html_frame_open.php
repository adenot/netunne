<?php

if ($startminimized==1) {
	$minimizedstyle = "display:block;";
	$maximizedstyle = "display:none;";
} else { 
	$minimizedstyle = "display:none;";
	$maximizedstyle = "display:block;";
}

?>
<!-- FRAME OPEN -->
<div id="frame_minimized_<?=$name?>" style="width:582px;<?=$minimizedstyle?>">
	<table width="582" cellspacing=0 cellpadding=0 border=0
		bgcolor="#1E3560">
		<tr>
			<td width="8" valign=top><img src="<?=DIRIMG?>frame_topleft.gif"></td>
			<td width="566">
				<div style="height:30px;overflow:hidden">
				<table
					height=30 cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td><nobr><a class="frame_title_text_minimized" href="javascript:maximize_frame('<?=$name?>');"><?=$title?></a></nobr></td>
					</tr>
				</table>
				</div>
			</td>
			<td width="8" valign=top ><img src="<?=DIRIMG?>frame_topright.gif"></td>
		</tr>
		<tr>
			<td width="8" valign=bottom ><img src="<?=DIRIMG?>frame_bottomleft.gif"></td>
			<td width="566"><img src="<?=DIRIMG?>dot.gif" width="566" height="1"></td>
			<td width="8"valign=bottom><img src="<?=DIRIMG?>frame_bottomright.gif"></td>
		</tr>
	</table>
</div>

<div id="frame_<?=$name?>" style="width:582px;<?=$maximizedstyle?>">
	<table
		width="582" cellspacing=0 cellpadding=0 border=0  
		bgcolor="#D6F1B9">
		<tr>
			<td width="8" bgcolor="#1E3560" valign=top><img src="<?=DIRIMG?>frame_topleft.gif"></td>
			<td width="566" background="<?=DIRIMG?>frame_title_back.gif">
				<div style="height:30px;overflow:hidden">
				<table
					height=30 cellspacing=0 cellpadding=0 border=0 
					bgcolor="#A4A7AE">
					<tr>
						<td bgcolor="#1E3560"><nobr><a class="frame_title_text" href="javascript:minimize_frame('<?=$name?>');"><?=$title?></a></nobr></td>
						<td>
							<img src="<?=DIRIMG?>frame_title_right.gif">
						</td>
				</table>
				</div>
			</td>
			<td width="8" valign=bottom bgcolor="#D6F1B9"><img src="<?=DIRIMG?>frame_topright.gif"></td>
		</tr>
		<tr>
			<td width="8" bgcolor="#1E3560"></td>
			<td width="566" bgcolor="#1E3560">
<!-- / FRAME OPEN -->