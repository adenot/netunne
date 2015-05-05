<?php
	$step=5;
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/top.php");

	$hdsys=	explode("_",$_SESSION['hdsys']);
	$hdsys= $hdsys[0];
	$part=	$_POST['part'];
	$pass=	$_SESSION['password'];
	$ip=	$_SESSION['ip'];
	$mask=	$_SESSION['mask'];

//	echo("5installer $hdsys $part $pass $ip $mask");
	exec("screen -d -m sudo /sbin/installer $hdsys $part $pass $ip $mask $lang 2>&1 > /dev/null &");
//	system("sudo /sbin/installer $hdsys $part $pass $ip $mask 2>&1");

?>
<script language="JavaScript"> 
<!-- 

var xmlhttp; 

function ajax() { 

   if (window.XMLHttpRequest) { 
      xmlhttp = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   } 

    xmlhttp.open("GET", "jx_installstatus.php", true); 
    xmlhttp.onreadystatechange = processReqChange; 
    xmlhttp.send(null); 
    window.clearTimeout(toID);
    toID = window.setTimeout('ajax();', 500);

} 

function processReqChange() {
    var entrada;
    var aentrada;
    var som; 
    var refr;
    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
           entrada = xmlhttp.responseText; 

	   aastep=entrada.split(":");
	   step=aastep.shift();

	   fimstep=aastep.pop();
	   astep=fimstep.split(",");
	   percent=astep.pop();


	   document.getElementById("percent").innerHTML = percent+"%";

	   document.getElementById("tablepercent").width = percent+"%";


 	   if (step=="part") {
		step="<?=_("Partitioning Harddisk")?>";
	   } else if (step=="format") {
		step="<?=_("Formatting Harddisk")?>";
	   } else if (step=="copy") {
		step="<?=_("Copying Files")?>";
	   } else if (step=="post") {
		step="<?=_("Configuring")?>";
	   } else if (step=="reboot") {
		step="<?=_("Rebooting in")?> "+(10-(percent/10))+" <?=("seconds")?>";
	   }

	   document.getElementById("step").innerHTML = step;

      } 
    } 
} 


-->
</script>

<table width="80%" align="center">
<tr>
	<td align="center">
	<p>&nbsp;</p>
	<p class="description">
		<?=_("Installing System... Please Wait.")?>
	</p>
<br><BR><BR><BR>
<table bgcolor=white width=325 height=23 cellspacing=0 cellpadding=0 style="border: 2px solid #B5B5B5;">
<tr>
<td width=46 bgcolor="#B5B5B5" align=center valign=middle>
<div id=percent class=smalldescription>0%</div>
</td>
<td bgcolor=white width=279 align=left>
<table width=0% id="tablepercent" height=21 cellspacing=0 cellpadding=0 bgcolor="#DFDFDF">
<tr><td></td></tr></table>
</td>
</tr>
</table>
<div id=step class=smalldescription></div>
	</td>
</tr>
</table>


<script>
toID = window.setTimeout('ajax();', 500);
</script>
<?php
	require_once("include/bottom.php");
?>
