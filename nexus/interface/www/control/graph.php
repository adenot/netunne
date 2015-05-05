<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 16/08/2006					*
	*																*
	****************************************************************/

	require_once ("../common.php");
	
	$page = new Page(_("Customer Graphs"));
	$page->open();

	$login = $_GET[login];
	
	if (conv::startwith("eth",$login)) {
		$refer="/network/index.php";
		$no_graph_error=_("No graphs yet for this interface");
	} else {
		$refer="/control/user.php";
		$no_graph_error=_("No graphs yet for this user");
	}
	
	
	$b = new Frameback($refer);
	$b->draw();
	
	$in = new Framebutton();
	$in->title=sprintf(_("Info for %s"),$login);
	$in->draw();
	
	$totalsfile = DIRDATA."user/user.totals";
	$info = @parse_ini_file($totalsfile);
	$tmp = explode(" ",$info[$login]);
	
	$totalmb1 = round(floatval(floatval($tmp[0]))/1024/1024);
	$totalkb1 = round(floatval(floatval($tmp[0]))/1024);
	$totalmb2 = round(floatval(floatval($tmp[1]))/1024/1024);
	$totalkb2 = round(floatval(floatval($tmp[1]))/1024);

	$useinfo[0][title]=_("This Month");
	$useinfo[0][desc]=sprintf(_("Total Download: %s Mb (%s Kb)"),$totalmb1,$totalkb1)."\n";
	$useinfo[0][desc].=sprintf(_("Total Upload: %s Mb (%s Kb)"),$totalmb2,$totalkb2);

	$oldls = explode("\n",trim(shell_exec("ls -t ".DIRDATA."user/*.oldtotals")));	

	$i=1;
	foreach ($oldls as $oldfile) {
		$oldfile = trim($oldfile);
		if ($oldfile=="") { continue; }
		
		$tmp = explode("/",$oldfile);
		$tmp = explode(".",$tmp[count($tmp)-1]);
		$year = intval(substr($tmp[0],0,4));
		$month= conv::formatmonth(intval(substr($tmp[0],4,2)));
		
		//echo $oldfile.substr($tmp[0],4,2)."<BR>";
		
		$info = @parse_ini_file($oldfile);
		$tmp = explode(" ",$info[$login]);
		
		$totalmb1 = round(floatval(floatval($tmp[0]))/1024/1024);
		$totalkb1 = round(floatval(floatval($tmp[0]))/1024);
		$totalmb2 = round(floatval(floatval($tmp[1]))/1024/1024);
		$totalkb2 = round(floatval(floatval($tmp[1]))/1024);
			
		$useinfo[$i][title]=$year." ".$month;
		$useinfo[$i][desc]=sprintf(_("Total Download: %s Mb (%s Kb)"),$totalmb1,$totalkb1)."\n";
		$useinfo[$i][desc].=sprintf(_("Total Upload: %s Mb (%s Kb)"),$totalmb2,$totalkb2);
		$i++;
	}
	
	$f = new Framelist();
	$f->title = _("Use Info");
	$f->data = $useinfo;
	$f->draw();
		
	/*
	$info = @parse_ini_file(DIRDATA."user/user.totals");
	if ($info) {
		if (file_exists(DIRDATA."/user/reset.date")) {
			$resetdate = conv::formatdate(file_get_contents(DIRDATA."/user/reset.date"));
		} else {
			$resetdate = _("install");
		}
		$tmp = explode(" ",$info[$login]);
		
		$useinfo[0][title]=sprintf(_("Total Download since %s: %s Mb (%s Kb)"),$resetdate,round($tmp[0]/1024/1024),round($tmp[0]/1024));
		$useinfo[1][title]=sprintf(_("Total Upload since %s: %s Mb (%s Kb)"),$resetdate,round($tmp[1]/1024/1024),round($tmp[1]/1024));
		$f = new Framelist();
		$f->title = _("Use Info");
		$f->data = $useinfo;
		$f->draw();
	}
	*/
	
	$fls[]="$login-last-day.png";
	$fls[]="$login-last-week.png";
	$fls[]="$login-last-month.png";
	$fls[]="$login-last-year.png";
	
	for ($i=0;$i<count($fls);$i++) {
		if (file_exists(DIRGRAPH.$fls[$i])) {	
			$ok=1;
?>
	<img src="<?=WWWGRAPH.$fls[$i]?>">
<?php
		}
	}
	
	if (!$ok) {
		$f = new Framebutton ();
		$f->title = $no_graph_error;
		$f->icon="fail";
		$f->draw();
	}	

	$page->close();
?>