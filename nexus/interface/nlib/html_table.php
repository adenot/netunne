<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 07/06/2006					*
	*																*
	****************************************************************/
	
	$page = trim(Setting::load("table","page_".$name));
	if ($page=="") { $page = 1;}

	$orderby = trim(Setting::load("table","order_".$name));
	if ($orderby=="") { $orderby=$this->orderby; }
	

?>
<!-- TABLE -->
<script language="JavaScript">
var table_page_<?=$name?>=<?=$page?>;
var table_total_<?=$name?>=<?=$total?>;
var table_itemtotal_<?=$name?>=<?=$itemtotal?>;
var table_perpage_<?=$name?>=<?=$perpage?>;
var table_order_<?=$name?>='<?=$orderby?>';
var table_showall_<?=$name?>=0;
var table_search_value_<?=$name?>='';
var table_search_field_<?=$name?>='';


//alert("<?=$orderby?> QQ <?=$page?> || <?=$name?>");

</script>

<table width="100%" class="table_frame" cellspacing=0 cellpadding=0 border=0>
	<tr height="10">
		<td valign=top><img src="<?=DIRIMG?>table_topleft.gif" width=9 height=8></td>
		<td><img src="<?=DIRIMG?>dot.gif" width="548" height="1"></td>
		<td valign=top align=right><img src="<?=DIRIMG?>table_topright.gif" width="9" height="8"></td>
	</tr>
	<!-- FILTER OS RESULTS -->
	<tr>
		<td></td>
		<td align="right">
			<table width="100%" cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td>
						<div id="table_status_<?=$name?>" style="
								 		cursor:default;
								 		padding:0px 10px 0px 10px;
								 		font-family:Verdana;
								 		font-size:9px;
								 		color:#ffffff"></div>
					</td>
					<td>
						<table align="right">
							<tr>
								<td style="cursor:default; padding:0px 5px 0px 10px; font-family:Verdana; font-size:9px; text-align:right; color:#ffffff">
									Filter: <input type="text" name="search_value_<?=$name?>" id="search_value_<?=$name?>" style="	width:100px; height:18px; font-family:Verdana; font-size:9px;">
								</td>
								<td>
									<select name="search_field_<?=$name?>" id="search_field_<?=$name?>" style="height:16px;font-family:Verdana;font-size:9px;">
										<? for ($i = 0; $i < count($this->data[0]); $i++){ ?>
											<option value='<?=$i?>'><?=$this->data[0][$i]?></option>
										<? } ?>
									</select>
								</td>
								<td><img src="<?=DIRIMG?>/search.gif" style="cursor:pointer;" border="0" onclick="table_search('<?=$name?>')" onmouseover="tooltip_show('<?=_("Filter Result")?>');" onmouseout="tooltip_hide();"></td>
								<td>
									<a href="#">
										<img src="<?=DIRIMG?>/reload.gif" border="0" onclick="table_show_all_contents('<?=$name?>')" onmouseover="tooltip_show('<?=_("Show all itens")?>');"onmouseout="tooltip_hide();">
									</a>
								</td>
								
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td></td>
	</tr>
	<!-- END FILTER -->
	<tr>
		<td></td>
		<td>
			<div id="table_blackout_<?=$name?>"
				style="visibility:hidden;position:absolute;background-color:white;"
			>carregando...</div>
			<div id="table_<?=$name?>">
			</div>
		</td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<table cellpadding=0 width="100%" height=28 cellspacing=0 border=0>
				<tr>
					<td width=27 background="<?=DIRIMG?>table_bottom_back.gif" valign=top><img src="<?=DIRIMG?>table_bottom_left_1.gif"><?
					if (!$nocheck) { ?><img src="<?=DIRIMG?>table_bottom_left_2.gif"></td>
					<td width=214 valign=top 
						background="<?=DIRIMG?>table_bottom_back.gif">
						<table cellspacing=0 cellpadding=1 border=0>
							<tr>
								<td>
						<select id="select_multiactions_<?=$name?>" style="
									width:100px;
									height:16px;
									font-family:Verdana;
								 	font-size:9px;"><?php
								 		foreach ($multiactions as $mact) {
								 	?>
								 	<option value="<?=$actions[$mact][2]?>"><?=$actions[$mact][1]?></option>
								 	<?php } ?>
								 	</select>
								 </td>
								 <td width=1></td>
								 <td valign=middle><img style="cursor:pointer;"
								 	onmouseover="tooltip_show('<?=_("Execute action for selected itens")?>');"
								 	onmouseout="tooltip_hide();"
								 	onclick="table_multiaction('<?=$name?>');"
								 	src="<?=DIRIMG?>table_bottom_go.gif"></td>
							</tr>
						</table>
						
						<? } else { ?>
					</td><td width=214 valign=top 
						background="<?=DIRIMG?>table_bottom_back.gif">
						<? } ?>
					</td>
					<td width=66  valign=bottom align=center 
						background="<?=DIRIMG?>table_bottom_center.gif"
						><img id="table_button_more_<?=$name?>" 
							style="mouse:pointer;"
							onclick="table_showall('<?=$name?>');"
							onmouseover="tooltip_show('<?=_("Show all itens")?>');"
							onmouseout="tooltip_hide();"
							src="<?=DIRIMG?>table_bottom_more.gif">
						<img id="table_button_less_<?=$name?>" 
							style="mouse:pointer;display:none;"
							onclick="table_showall('<?=$name?>');"
							onmouseover="tooltip_show('<?=_("Restore Paging")?>');"
							onmouseout="tooltip_hide();"
							src="<?=DIRIMG?>table_bottom_less.gif">
							</td>
					<td width=233 valign=top
						background="<?=DIRIMG?>table_bottom_back.gif">
						<table id="table_paging_<?=$name?>" align=center cellspacing=0 cellpadding=0 border=0>
							<tr>
								<td><img style="cursor:pointer;"
										onclick="table_first('<?=$name?>')"
										onmouseover="tooltip_show('<?=_("First")?>');"
								 		onmouseout="tooltip_hide();"
										src="<?=DIRIMG?>table_bottom_nav_first.gif"></td>
								<td><img style="cursor:pointer;"
										onclick="table_prev('<?=$name?>')"
										onmouseover="tooltip_show('<?=_("Previous")?>');"
								 		onmouseout="tooltip_hide();" 
								src="<?=DIRIMG?>table_bottom_nav_prev.gif"></td>
								<td
								><div id=""
								 	style="
								 		cursor:default;
								 		padding:0px 10px 0px 10px;
								 		font-family:Verdana;
								 		font-size:9px;
								 		color:#ffffff"
								 	>page
								 	<span id="table_pagenum_<?=$name?>">1</span>
								 	of 
								 	<span id="table_pagetot_<?=$name?>"><?=$total?></span></div
								></td>
								<td><img style="cursor:pointer;"
										onclick="table_next('<?=$name?>')"
										onmouseover="tooltip_show('<?=_("Next")?>');"
								 		onmouseout="tooltip_hide();"
								 		src="<?=DIRIMG?>table_bottom_nav_next.gif"></td>
								<td><img style="cursor:pointer;"
										onclick="table_last('<?=$name?>')"
										onmouseover="tooltip_show('<?=_("Last")?>');"
								 		onmouseout="tooltip_hide();" 
								 		src="<?=DIRIMG?>table_bottom_nav_last.gif"></td>
							</tr>
						</table>						
					</td>
					<td width=8 valign=top><img src="<?=DIRIMG?>table_bottom_right.gif"></td>
				</tr>
			</table>
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
	table_load("<?=$name?>","<?=$page?>","<?=$orderby?>");
</script>
<!-- / TABLE -->
