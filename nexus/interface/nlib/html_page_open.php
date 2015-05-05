<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/
	
	$i=0;
	$menu["config"][$i]["name"]	  = _("Administration");
	$menu["config"][$i]["location"] = "/setup/admin.php"; $i++;

	$menu["config"][$i]["name"]	  = _("Network Setup");
	$menu["config"][$i]["location"] = "/network/index.php"; $i++;

	$menu["config"][$i]["name"]	  = _("Proxy Definitions");
	$menu["config"][$i]["location"] = "/setup/proxy.php"; $i++;

	$menu["config"][$i]["name"]	  = _("DDNS");
	$menu["config"][$i]["location"] = "/setup/ddns.php"; $i++;
	
	$menu["config"][$i]["name"]	  = _("Time & Date");
	$menu["config"][$i]["location"] = "/setup/date.php"; $i++;
	
	$menu["config"][$i]["name"]	  = _("Server Registry");
	$menu["config"][$i]["location"] = "/setup/license.php"; $i++;
	
	$menu["config"][$i]["name"]	  = _("Update Info");
	$menu["config"][$i]["location"] = "/setup/update.php"; $i++;
	
	$menu["config"][$i]["name"]	  = _("Backup & Data");
	$menu["config"][$i]["location"] = "/setup/backup.php"; $i++;
	
	$menu["config"][$i]["name"]	  = _("Server Info");
	$menu["config"][$i]["location"]= "/setup/server.php"; $i++;

	$menu["config"][$i]["name"]	  = _("Hardware Info");
	$menu["config"][$i]["location"]= "/setup/hardware.php"; $i++;

	$menu["config"][$i]["name"]	  = _("Connection Check");
	$menu["config"][$i]["location"]= "/network/conncheck.php"; $i++;
	
	$i=0;
	$menu["custom"][$i]["name"]	  = _("Customer Login");
	$menu["custom"][$i]["location"] = "/custom/login.php"; $i++;
	
	//$menu["custom"][$i]["name"]	  = _("Customer Area");
	//$menu["custom"][$i]["location"] = "/custom/area.php"; $i++;
	
	$menu["custom"][$i]["name"]	  = _("Customer Alerts");
	$menu["custom"][$i]["location"] = "/custom/alerts.php"; $i++;
	
	$menu["custom"][$i]["name"]	  = _("Limits");
	$menu["custom"][$i]["location"] = "/custom/billing.php"; $i++;
	
	$i=0;
	$menu["control"][$i]["name"]	  = _("Customers");
	$menu["control"][$i]["location"]= "/control/user.php"; $i++;
	
	$menu["control"][$i]["name"]	  = _("Credit");
	$menu["control"][$i]["location"]= "/control/guest.php"; $i++;
	
	$menu["control"][$i]["name"]	  = _("Plans");
	$menu["control"][$i]["location"]= "/control/plan.php"; $i++;
	
	$menu["control"][$i]["name"]	  = _("Publish Server");
	$menu["control"][$i]["location"]= "/control/publish.php"; $i++;
	

	
/*
	$menu['system'][] = array("name" => "Basic Setup"		, "location" => "");
	$menu['system'][] = array("name" => "Time/Date"			, "location" => "");
	$menu['system'][] = array("name" => "Update Center"		, "location" => "/setup/update.php");
	$menu['system'][] = array("name" => "Licensing"			, "location" => "/setup/license.php");
	$menu['system'][] = array("name" => "Backup"			, "location" => "");
	$menu['system'][] = array("name" => "Data Management"	, "location" => "");
	
	$menu['definitions'][] = array("name" => "Host"			, "location" => "");
	$menu['definitions'][] = array("name" => "Network"		, "location" => "");
	$menu['definitions'][] = array("name" => "Time"			, "location" => "");
	$menu['definitions'][] = array("name" => "Service"		, "location" => "");
	//$menu['definitions'][] = array("name" => "Service Group", "location" => "#");
	
	$menu['network'][] = array("name" => "Setup"			, "location" => "/network/index.php");
	//$menu['network'][] = array("name" => "DNS Proxy"		, "location" => "#");
	//$menu['network'][] = array("name" => "DHCP Server"		, "location" => "#");
	$menu['network'][] = array("name" => "Publish Server"	, "location" => "");
	
	$menu['control'][] = array("name" => "Account"			, "location" => "/control/user.php");
	$menu['control'][] = array("name" => "Guests"			, "location" => "/control/guest.php");
	$menu['control'][] = array("name" => "Plans"			, "location" => "");
	$menu['control'][] = array("name" => "User Login Config", "location" => "");
	
	$menu['security'][] = array("name" => "Server Firewall"	, "location" => "");
	
	$menu['reports'][] = array("name" => "Setup"			, "location" => "");
	$menu['reports'][] = array("name" => "View"				, "location" => "");
	*/
	
	function getSubMenu($menu){
	
		$tb  = "<table width='153' border='0' cellpadding='0' cellspacing='0'>";
		for($i = 0; $i < count($menu); $i++){
			$tb .= "<tr><td class=\"menuSubItem\" onClick=\"goMenu('" . $menu[$i]['location'] . "')\">" . $menu[$i]['name'] . "</td></tr>\n";
			$tb .= "<tr class='menuItemSep'><td></td></tr>";
		}
		$tb .= "</table>";
		return $tb;
		
	}
	
	$fulltitle = constant("PRODNAME")." // ".$title;
?>
<HTML>
  <HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <TITLE><?=$fulltitle?></TITLE>
    <link rel="stylesheet" href="<?=DIRCSS."main.css.php"?>" type="text/css" media="all" />
    <SCRIPT language="JavaScript" src="<?=DIRJS?>main.js"></SCRIPT>
    <SCRIPT>var blackout_opac;</SCRIPT>
    <script type="text/javascript">
	function handleError(msg) {
		//alert(msg);
		return true;
	}
	

	//window.onerror = handleError;
	</script>
	<script>

	
	var this_page = '<?=$_SERVER[PHP_SELF]."?".$_SERVER[QUERY_STRING]?>';
	
	
	
	function trocaClasse(e){
	    if(typeof(e)=='undefined')var e=window.event
	    source=e.target?e.target:e.srcElement
	    if(source.nodeType == 3)source = source.parentNode
	    o=source.parentNode
	    o.className=(o.className=="aberto")?"fechado":"aberto"
	//	alert(o.className);
	    return false
	}
	function goMenu (url) {
		location.href = url;
	}

	function show_framelog() {
<?php
	if ($_GET[action])
		echo "table_framelog('$_GET[title]','$_GET[action]','$_GET[details]');\n";
?>
		return 1;
	}
	
function openMenu() {
var section = new String("<?php
	foreach ($menu as $k=>$v) {
		foreach ($v as $url) {
			if ($url[location]==$_SERVER[PHP_SELF]) {
				echo $k;
			}
		}
	}
?>");

showSubMenu(section.ucFirst());

}
</script>

  </HEAD>
  <BODY onload="parent.document.title='<?=$fulltitle?>';pageunblackout(this);show_framelog();auto_logout();openMenu();" onunload="pageblackout(this);">
  	<div id="tooltip"></div>
  	<div id="tooltipshadow"></div>
  	<div id="blackout"></div>
  	<div id="blackout2"></div>
	<!--
	<div id="menu">
	  <ul>
	    <li><a href="/network/index.php">System</a></li>
	    <li><a href="/poc/index.php">Definitions</a></li>
	    <li><a href="/poc/css_test.html">Network</a></li>
	    <li><a href="/network/index.php">Control</a></li>
	    <li><a href="/network/index.php">Security</a></li>
	  </ul>
	</div>
	-->
    <div id="pagetitle"><?=$title?></div>
    <?php
    	if (!file_exists("/root/demo")) {
    ?>
    <div id="btn_apply"><img 
    	src="<?=DIRIMG?>btn_apply.gif" onclick="table_apply();"
    	onmouseover="tooltip_show('<?=_("Review last changes and apply")?>');"
    	onmouseout="tooltip_hide();"
     border=0></div>
     <? }?>
    <table cellpadding=0 cellspacing=0 border=0 class="table_main"><tr>
    		<td colspan=3 class="tablecell_header"><img src="<?=DIRIMG?>header.jpg"></td>
    	</tr>
    	<tr>
    		<td class="tablecell_sidebar" valign="top">
    			<img src="<?=DIRIMG?>dot.gif" width=153 height=20>
				
	<table width="153"  border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td class="menuItem" onClick="showSubMenu('Config')">
			<?=_("Configuration")?>
		</td>
	  </tr>
		<tr>
			<td>
				<div class="menuSubItemDisabled" id="menuConfig">
					<?=getSubMenu($menu['config'])?>
				</div>
			</td>
		</tr>
	  <tr class="menuItemSep">
		<td></td>
	  </tr>
	  <tr>
		<td class="menuItem" onClick="showSubMenu('Custom')">
			<?=_("Customization")?>
		</td>
	  </tr>
	  <tr>
		<td>
			<div class="menuSubItemDisabled" id="menuCustom">
				<?=getSubMenu($menu['custom'])?>
			</div>
		</td>
	  </tr>
	  <tr class="menuItemSep">
		<td></td>
	  </tr>
	  <tr>
		<td class="menuItem" onClick="showSubMenu('Control')">
			<?=_("Control")?>
		</td>
	  </tr>
	  <tr>
		<td>
			<div class="menuSubItemDisabled" id="menuControl">
				<?=getSubMenu($menu['control'])?>
			</div>
		</td>
	  </tr>
	  
	  <tr class="menuItemSep">
		<td></td>
	  </tr>
	  
	  <!--
	  <tr>
		<td class="menuItem" onClick="showSubMenu('Control')">
			Control
		</td>
	  </tr>
	  <tr>
		<td>
			<div class="menuSubItemDisabled" id="menuControl">
				<?=getSubMenu($menu['control'])?>
			</div>
		</td>
	  </tr>
	  
	  <tr class="menuItemSep">
		<td></td>
	  </tr>
	  <tr>
		<td class="menuItem" onClick="showSubMenu('Reports')">
			Reports
		</td>
	  </tr>
	  <tr>
		<td>
			<div class="menuSubItemDisabled" id="menuReports">
				<?=getSubMenu($menu['reports'])?>
			</div>
		</td>
	  </tr>
	  <tr>
		<td class="menuItem" onClick="showSubMenu('Security')">
			Security
		</td>
	  </tr>
	  <tr>
		<td>
			<div class="menuSubItemDisabled" id="menuSecurity">
				<?=getSubMenu($menu['security'])?>
			</div>
		</td>
	  </tr>
	  -->
	</table>
				
    		</td>
    		<td valign="top" class="tablecell_body">
    			<img src="<?=DIRIMG?>dot.gif" width="626" height="1" style="width:626px;height:1px;">
    		    <div id="body">
<?php

	if ($_GET[status]=="error") {
		$frameundo = new Framebutton ();
		$frameundo->title = sprintf(_("Error! %s"),base64_decode($_GET["msg"]));
		$frameundo->icon = "fail";
		$frameundo->animate=1;
		$frameundo->draw();

	} else if ($_GET[status]&&!$_GET[undo]&&!$nostatus) {
		$frameundo = new Framebutton ();
		$frameundo->title = _("Alterations saved successfully");
		$frameundo->icon = "ok";
		$frameundo->animate=1;
		$frameundo->draw();
		
	} else if ($_GET[undone]&&!$nostatus) {
		$frameundo = new Framebutton ();
		$frameundo->title = _("Alterations undone!");
		$frameundo->icon = "ok";
		$frameundo->buttontext = _("Redo");
		$frameundo->animate=1;
		$frameundo->draw();

	} else if ($_GET[status]&&$_GET[undo]&&!$nostatus) {
		$frameundo = new Framebutton ("undo_".$_GET[undo]);
		$frameundo->title = _("Alterations saved successfully");
		$frameundo->help = _("Click on Undo to cancel last alteration");
		$frameundo->icon = "ok";
		$frameundo->buttontext = _("Undo");
		$frameundo->animate=1;
		$frameundo->draw();
		
	} else if ($_GET[alertlic]&&!$nostatus) {
		$framelic = new Framebutton ("go_/setup/license.php");
		$framelic->title = _("This action cannot be perfomed due to license restrictions");
		$framelic->help = _("Your license disables you to make this action.<BR>Click on View Details to find informations about your license.");
		$framelic->icon = "fail";
		$framelic->buttontext = _("View Details");
		$framelic->animate=1;
		$framelic->draw();
	}    
	?>
