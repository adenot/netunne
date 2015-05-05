<?php

/****************************************************************
*																*
* 			Console Tecnologia da Informa��o Ltda				*
* 				E-mail: contato@console.com.br					*
* 				Arquivo Criado em Sep 6, 2006					*
*																*
****************************************************************/
//rgb(164, 167, 174)
//rgb(200, 203, 220)
//A4A7AE
//C8D0DC

$open = $_SESSION["table_$name"][open];
?>

<?php

$id = 0;
foreach ($data as $dat) {
	if ($id != 0) {
?><div style="height:10px;width:10px;font-size:1px;"></div><?php

	}
	$id++;
?><table border=0 cellspacing=0 cellpadding=0 
	style="
		width:100%;
		border:1px solid gray;

		background-color:rgb(200, 203, 220);


		">
	<tr>
		<td  onclick="tablelist_showhide('<?=$name?>','<?=$id?>');"
 			onmouseover="this.style.backgroundColor='#FFFFFF';"
			onmouseout="this.style.backgroundColor='#D8E0EC';"
			style="
				font-family:Verdana;
				font-size:14px;
				padding:5px 0px 0px 0px;
				cursor:pointer;
				<?php if ($autorefresh=="yes"&&$id==1) { ?>
				border:5px solid rgb(150, 200, 150);
				<?php } else { ?>
				border:5px solid rgb(200, 203, 220);
				<?php } ?>
				background-color: #D8E0EC;
				
			">
			<!--
			<span style="padding:0px 10px 0px 10px"><?

?>

</span>
			<span style="padding-right:20px;font-size:14px;"><?=$dat[title]?></span>
			<span style="
				padding: 5px 10px 5px 10px;
				font-family:Verdana;
				font-size:12px;"
				id="tablelist_<?=$name?>_<?=$id?>_short"
				><?=$dat[short]?></span>
				<div 
					style="padding: 5px 10px 5px 10px;
							font-family:Verdana;
						font-size:12px;
						display:none;"
					id="tablelist_<?=$name?>_<?=$id?>_content"></div>
			-->
			<table border=0 cellspacing=0 cellpadding=0 style="width:536px"><tr><td style="width:20px;"><? if($dat[icon]) { ?><img src="/_images/<?=$dat[icon]?>"><? } ?></td>
			<td style="width:516px;"><table 
				style="	font-family:Verdana;
						padding: 0px 0px 5px 0px;
						overflow:hidden;
						
						/* tirei 20px pro icone */
						width:516px;
						"
				border=0 cellspacing=0 cellpadding=0>
				<tr>
					<!--<td style="padding-right:10px;padding-left:10px"><? if($dat[icon]) { echo $dat[icon]; } ?></td>-->
					<td style="padding-right:20px;font-size:14px;"><nobr><?=$dat[title]?></nobr></td>
					<td style="overflow:hidden;" ><div style="visibility:<?= ($open==1) ? "hidden" : "visible" ?>;overflow:hidden;height:14px;font-size:11px;" id="tablelist_<?=$name?>_<?=$id?>_short"><?=$dat[desc]?></div></td>
				</tr>
			</table></td>
			</tr></table>			
			</td>
	</tr>
<!--<span style="display:none;visibility:hidden;" id="tablelist_<?=$name?>_<?=$id?>_text"><?=nl2br($dat[desc])?></span>-->

	<tr id="tablelist_<?=$name?>_<?=$id?>_content"
		style="display:<?= ($open==1) ? "block" : "none" ?>;">
		<td 
			style="
				padding-top:5px;
				padding-left:25px;
				padding-bottom:5px;
				font-family:Verdana;
				font-size:12px;
		"><?=nl2br($dat[desc])?></td>
	</tr>
</table><?php

}
?>