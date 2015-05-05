<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Dez 21, 2006					*
	*																*
	****************************************************************/


?>
<table cellspacing=0 cellpadding=0 border=0 width="100%">
	<tr>
		<td class="explorer_menu_item"
			onclick="explorer_load('<?=$url_online?>','<?=$myid?>');"
		>Customers Online</td>
	</tr>
		<?php 
		if (isset($online)) { 
			foreach ($online as $user) { ?>
			<tr><td class="explorer_menu_subitem" style="color: #bbffbb;"><?=$user?></td></tr>
		<?php 
			} 
			if (count($online)==0) {
		?>
			<tr><td class="explorer_menu_subitem"><?=_("All offline")?></td></tr>
		<?php
			}	
		} ?>
	<tr>
		<td class="explorer_menu_item"
			onclick="explorer_load('<?=$url_offline?>','<?=$myid?>');"
		>Customers Offline</td>
	</tr>
		<?php 
		if (isset($offline)) { 
			foreach ($offline as $user) { ?>
			<tr><td class="explorer_menu_subitem" style="color: #ffbbbb;"><?=$user?></td></tr>
		<?php 
			} 
			if (count($offline)==0) {
		?>
			<tr><td class="explorer_menu_subitem"><?=_("All online")?></td></tr>
		<?php
			}	
		} ?>
	<tr>
		<td class="explorer_menu_item"
			onclick="explorer_load('<?=$url_conline?>','<?=$myid?>');"
		>Credits Online</td>
	</tr>
	<tr>
		<td class="explorer_menu_item"
			onclick="explorer_load('<?=$url_coffline?>','<?=$myid?>');"
		>Credits Offline</td>
	</tr>	
</table>