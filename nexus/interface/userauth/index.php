<?php


include "../nlib/lib_common.php";

if (file_exists("/tmp/nx_applying")) {
	echo "<HTML><HEAD><TITLE>"._("Server down")."</TITLE></HEAD><BODY>\n";
	echo nl2br(_("The server is overloaded or down for maintenance and due to this was unable to process the client request.\nTry Again in a few minutes"));
	echo "\n</BODY></HTML>";
	exit();
}

$arp = new cmd_Arp();
$int = $arp->getint($_SERVER["REMOTE_ADDR"]);

$obj = new Object();
$myip = $obj->get("`HOST.INTERFACE.$int`");


$url = urlencode(base64_encode("http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]));
/*
header("Expires: Mon, 15 Jan 1997 10:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
*/

header("Location: http://".$myip.":3080/form.php?url=$url");

@ header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
@ header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
@ header('Cache-Control: no-cache, must-revalidate, max-age=0');
@ header('Pragma: no-cache');

//header("HTTP/1.1 302 Moved Temporarily");

//header("Status: 302 Moved Temporarily");


/*
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");


$url = urlencode("http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]);
//header("Location: http://".$_SERVER[SERVER_NAME]."/form.php?url=$url");

echo "<html><body><form name='userauth' action='/form.php' method='post'>";
echo "<input type='hidden' name='url' id='urldata' value='" . $url . "'>";
echo "</form></body>";
echo "<script>document.getElementById('urldata').value=window.location;document.userauth.submit();</script></html>";
exit();
*/

exit();
?>
<HTML><BODY>
<script language="JavaScript">
function loadurl () {
<?php if ($redir) { ?>
	//alert('<?=urldecode($redir)?>');
	window.setTimeout("window.location='<?=$redir?>';",5000);
<?php } ?>
}
</script>
</BODY>
</HTML>
http://".$myip.":3080/form.php?url=$url