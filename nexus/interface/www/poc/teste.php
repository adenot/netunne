<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 19/06/2006					*
	*																*
	****************************************************************/
//phpinfo();exit();
include "../common.php";

$obj = new Object();
$external = $obj->get("`NETWORK.INTERFACE.EXTERNAL`");

print_r($external);
exit();

$conf = new Conf();

print_r(xml::normalizeseq($conf->conf[forward][traffics]));
exit();

$var[maxusers]=10;
$var[maxguests]=20;
$var[desc]=urlencode(htmlentities("Obrigado por utilizar o Netunne Provider.\nVocê pode utilizar 10 usuarios e 20 guest."));
$var[alert]=urlencode(htmlentities("Atenção, verifique sua conta em netunne.com"));
echo serialize($var);exit();

//$a = @file_get_contents("naoexite");

//var_dump($a);

$a = file_get_contents("css_test.html");

$a = "<a href=\"/UserFiles/AAAAAAAAAjknkjAAAAA.php\">aPa</a><a href=\"AAAAAAApppAAAAAAA\">aPPa</a>";
//ereg ("<a href=\"(.*)\">",$a,$reg);


$pattern = "/<a(.*?)href\s*=\s*['|\"|\s*](.*?)['|\"|>](.*?)>(.*?)<\/a>/i";

function cb2($matches) {
   
	$tmp = explode("/",$matches[2]);
	$tmp2= explode(".",$tmp[count($tmp)-1]);
	$ext = array('php','php3','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi');
	if (
		(strtoupper(substr($matches[2],0,11))=="/USERFILES/")
		&&
		(!in_array($tmp2[1],$ext))
		)
		 {
		$file="file.php?file=";
		$matches[2]=$file.rawurlencode($matches[2]);
 
	} else {
		$matches[2] = implode("/", array_map("rawurlencode", explode("/", $matches[2])));
	}
	return "<a" . $matches[1] . "href='" . $matches[2] . "'" . $matches[3] . ">" . $matches[4] . "</a>";
}
$line = preg_replace_callback($pattern,"cb2",$a);
//echo $line;

// AQUI CELAO !!!!!!!!!!!!!!!!

$a = "\n\n<img src=\"acjnadkjc.gif\" border=0>acndjc\n\n<img border=0 src='acjdn.jpg'>\n";

$pattern = "/<img(.*?)src\s*=\s*['|\"|\s*](.*?)['|\"|>](.*?)>/i";

function cb3($matches) {
   
	$img = "img.php?img=";
	$matches[2]=$img.rawurlencode($matches[2]);
	return "<img" . $matches[1] . "src='" . $matches[2] . "'" . $matches[3] . ">";
}
$line = preg_replace_callback($pattern,"cb3",$a);
echo $line;

// ATEH AQUI !!!!!!!!!!!!!!!!!!!!!





ob_start();
?>
<html><body>
<form action=teste.php method=post>
<textarea name=texto cols=70 rows=10></textarea>

  <input type="submit" name="name" value="value"/>
  
  
</form><!--
#!/bin/sh
#
# pppoe-server                     This script starts or stops a pppoe-server
#
# chkconfig: 2345 99 01
# description: Start pppoe-server
#
# Copyright (C) 2000 Roaring Penguin Software Inc.  This software may
# be distributed under the terms of the GNU General Public License, version
# 2 or any later version.
# Modifed to work with SuSE 6.4 linux by Gary Cameron.
# Modifed and fixed to work with SuSE linux by Anas Nashif. 
#
### BEGIN INIT INFO
# Provides: pppoe-server
# Required-Start: network route
# Required-Stop: network
# Default-Start: 3 5
# Default-Stop: 0 1 2 6
# Description: Start pppoe-server
### END INIT INFO                               

# Source function library.
. /etc/rc.status

# First reset status of this service

#Tweak this
restart_time=10

# From AUTOCONF
prefix=/usr
exec_prefix=${prefix}

# Paths to programs : includes the server address and the lower dhcp address
START=&amp;amp;amp;quot;${exec_prefix}/sbin/pppoe-server -I eth1 -L 44.151.31.31 -R 44.151.31.220&amp;amp;amp;quot;
STOP=${exec_prefix}/sbin/pppoe-server
STATUS=${exec_prefix}/sbin/pppoe-server


rc_reset                                 
case &amp;amp;amp;quot;$1&amp;amp;amp;quot; in
    start)
        echo -n &amp;amp;amp;quot;Starting pppoe server&amp;amp;amp;quot;
        startproc $START  &amp;amp;amp;gt; /dev/null 2&amp;amp;amp;gt;&amp;amp;amp;amp;1 
	rc_status -v
        ;;

    stop)
        echo -n &amp;amp;amp;quot;Shutting pppoe server&amp;amp;amp;quot;
        killproc $STOP &amp;amp;amp;gt; /dev/null 2&amp;amp;amp;gt;&amp;amp;amp;amp;1 
	rc_status -v
        ;;

    try-restart)
        ## Stop the service and if this succeeds (i.e. the
        ## service was running before), start it again.
        $0 stop  &amp;amp;amp;amp;&amp;amp;amp;amp;  $0 start
 
        # Remember status and be quiet
        rc_status
        ;;

    restart)
        $0 stop
        echo &amp;amp;amp;quot;Waiting&amp;amp;amp;quot; $restart_time &amp;amp;amp;quot;seconds for the host to reset itself&amp;amp;amp;quot;
        sleep $restart_time  #Note: Need time for host to reset itself
        $0 start
	rc_status
        ;;

    status)
        checkproc $STATUS
        rc_status -v
        ;;

    *)
        echo &amp;amp;amp;quot;Usage: pppoe-server {start|stop|restart|status|try-restart}&amp;amp;amp;quot;
        exit 1
esac
rc_exit

-->
</body></html>
<?php ob_end_clean(); ?>