<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 12/06/2006					*
	*																*
	****************************************************************/
$order=explode(" ",$order);
$orderimg[$order[1]]="<div style='width:10px;display:inline;height:1px;'></div><img style='padding:2px 4px 2px 10px;' src='".DIRIMG."table_order_".strtolower($order[0]).".gif'>";

?>
<!-- TABLE CONTENT -->
		<table width="100%" class="table_table" cellspacing=0 cellpadding=0 border=0>
		<?php
			$c=2;
			for ($i=0;$i<count($data);$i++) {
				if ($c==1) { $c=2; } else { $c=1; }
				$d=$data[$i];
				if ($i==0) {
		?>
			<!-- TITLE BEGIN -->
			<tr>
				<td class="table_cell_header" style="height:23px;width:25px;border:0;padding:2px 0px 2px 0px;"
				align="center"><input type="checkbox" <? if ($nocheck>=1) { ?>style="visibility:hidden;"<? } ?> onclick="table_checkall('<?=$name?>',this)"></td>
		<?php		for ($j=0;$j<count($d);$j++) { 	?>
						<td 
							onclick="table_order('<?=$name?>','<?=$j?>');"
							class="table_cell_header" width="<?=$size[$j]?>"><nobr><?=$d[$j]?><?=$orderimg[$j]?></nobr></td>
		<?php		} 								?>

				<td class="table_cell_header_actions"></td>
		
			</tr>
			<!-- TITLE END -->
		<?php
			if (count($data)==1) {		
		?>
			<tr class="table_tr_1">
				<td colspan=<?=count($d)+1?> class="table_cell" style="text-align:center">
					no data in table
				</td>
			</tr>
		
		<?php
			}
				} else {
					
					if (!is_integer($linkid)) {
						$linkact = $i+$startat;
					} else {
						$linkact = urlencode($d[$linkid]);
					}
					
		?>
			<tr class="table_tr_<?=$c?>"
				
				style="cursor:hand;"
				
				onmouseover="
					this.className='table_tr_over'; 
					document.getElementById('table_action_<?=$name?>_<?=$i?>').style.visibility='visible';
					"
				onmouseout="
					this.className='table_tr_<?=$c?>';
					document.getElementById('table_action_<?=$name?>_<?=$i?>').style.visibility='hidden';
					"
				onclick="
					table_check('<?=$name?>','<?=$i+$startat?>','<?=$nocheck?>','<?=$actions[0][2]?>','<?=$linkact?>');
					"
			>
			
		<?php	if($d!="EMPTY") { ?>
			
				<td class="table_cell_header" style="height:23px;width:26px;border:0;padding:2px 0px 2px 0px;" align="center"
				><input type="checkbox" onclick="table_check('<?=$name?>','<?=$i+$startat?>');" <? if ($nocheck>=1) { ?>style="visibility:hidden;"<? } ?> name="table_check_<?=$name?>" id="table_check_<?=$name?>_<?=$i+$startat?>" value="<?=$linkact?>"></td>

		<?php
					for ($j=0;$j<count($d);$j++) { 	?>
						<td	class="table_cell"><?=$d[$j]?></td>
		<?php		}	?>
				
				<!-- AÇÕES -->
				<td class="table_cell_actions"  valign="middle" align="center">
					<div id="table_action_<?=$name?>_<?=$i?>" style="vertical-align:middle;visibility:hidden; text-align:right; width:<?=(19*count($actions))?>px;">
						<nobr><?php foreach ($actions as $k=>$v) {
							if ($v[3]==1) { continue; } // se o array[3] for 1, nao mostro aqui (é uma acao multipla somente)	
						?><img 
						src="<?=DIRIMG?>action_<?=$v[0]?>.gif" style="padding-right:3px; cursor:pointer;" onmouseover="tooltip_show('<?=$v[1]?>');" onmouseout="tooltip_hide();" onclick="table_do_act('<?=$linkact?>','<?=$v[2]?>');" ><div 
						style="width:3px; display:inline;height:1px;"></div><?php } ?></nobr>
					</div>
				</td>
				<!-- FIM AÇÕES -->
				
		<?php	} else {							?>
					<td class="table_cell_header" style="height:23px;width:26px;border:0;padding:2px 0px 2px 0px;" align="center"></td>
					<td class="table_cell" colspan="<?=count($data[0])+1?>"><div id="table_action_<?=$name?>_<?=$i?>"></div></td>
		<?php 	}	?>
				
			</tr>
		<?php
				}
			}
		?>
		</table>
<!-- / TABLE CONTENT -->