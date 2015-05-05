<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 29/08/2006					*
	*																*
	****************************************************************/

	require_once ("../common.php");

		$tz["GMT+12"]="(GMT-12) Eniwetok, Kwajalein";
		$tz["GMT+11"]="(GMT-11) Midway Island, Samoa";
		$tz["GMT+10"]="(GMT-10) Hawaii";
		$tz["GMT+9"]="(GMT-09) Alaskan";
		$tz["GMT+8"]="(GMT-08) Pacific Time(US&amp;Canada)";
		$tz["GMT+7"]="(GMT-07) Arizona, MountainTime(US&amp;Canada)";
		$tz["GMT+6"]="(GMT-06) CentralTime(US&amp;Canada), MexicoCity, Tegucigalpa";
		$tz["GMT+5"]="(GMT-05) Bogota, Lima, Quito";
		$tz["GMT+5"]="(GMT-05) EasternTime(US&amp;Canada), Indiana(East)";
		$tz["GMT+4"]="(GMT-04) AtlanticTime(US&amp;Canada), Caracas, LaPaz";
		$tz["GMT+3"]="(GMT-03) BuenosAires, Georgetown, Brasilia";
		$tz["GMT+2"]="(GMT-02) Mid-Atlantic";
		$tz["GMT+1"]="(GMT-01) Azores, CapeVerdeIs.";
		$tz["GMT"]="(GMT) Casablanca, Monrovia, Dublin, Edinburgh, Lisbon, London";
		$tz["GMT-1"]="(GMT+01) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna";
		$tz["GMT-1"]="(GMT+01) Belgrade, Bratislave, Budapest, Ljubljana, Prague";
		$tz["GMT-1"]="(GMT+01) Brussels, Copenhagen, Madrid, Paris, Vilnius";
		$tz["GMT-1"]="(GMT+01) Sarajevo, Skopje, Sofija, Warsaw, Zagreb";
		$tz["GMT-2"]="(GMT+02) Athens, Bucharest, Cairo, Istanbul, Minsk";
		$tz["GMT-2"]="(GMT+02) Harare, Helsinki, Jerusalem, Pretoria, Riga, Tallinn";
		$tz["GMT-3"]="(GMT+03) Moscow, St.Petersburg, Volgograd, Baghdad, Kuwait, Riyadh";
		$tz["GMT-4"]="(GMT+04) AbuDhabi, Baku, Muscat, Tbilist";
		$tz["GMT-5"]="(GMT+05) EKaterinburg, Islamabad, Karachi, Tashikent";
		$tz["GMT-6"]="(GMT+06) Astana, Almaty, Colombo, Dhaka";
		$tz["GMT-7"]="(GMT+07) Bangkok, Hanoi, Jakarta";
		$tz["GMT-8"]="(GMT+08) Beijing, HongKong, Singapore, Taipei";
		$tz["GMT-9"]="(GMT+09) Seoul, Tokyo, Yakutsk";
		$tz["GMT-10"]="(GMT+10) Canberra, Guam, PortMoresby, Vladivostok";
		$tz["GMT-11"]="(GMT+11) Magadan, SolomonIslands";
		$tz["GMT-12"]="(GMT+12) Fiji, Kamchatka, MarshallIslands, Wellington";
	
	$page = new Page (_("Adjust Date and Time"));
	$page->open();

	$fclock = new Frameclock();
	$fclock->text = _("Server Time: ");
	$fclock->draw();

	$frame = new Framebutton ("ntpdate");
	$frame->title = _("Update from time server");
	$frame->help = _("Update date and time from a time server");
	$frame->logtitle = _("Update Time and Date");
	$frame->action = "ntpdate";
	$frame->draw();
	
	$frame2 = new Frame("datetime");
	$frame2->title = _("Date and Time");
	
	$date = date("n/j/Y");
	$time = date("H:i");
	$timezone = file_get_contents("/etc/timezone");
	$timezone = explode("/",$timezone);
	$timezone = trim($timezone[1]);
	if ($timezone=="Sao_Paulo") { $timezone="GMT+3"; }
	//echo $timezone;
	//echo $date.$time;

	$form = new Form ("timezone");
	
	$form->itype="list";
	$form->iname="timezone";
	$form->ilabel=_("Timezone");
	$form->ihelp="";
	$form->ivalue=$timezone;
	$form->ivalues=$tz;
	$form->nextitem();
		
	/*
	$form->itype="date";
	$form->iname="date";
	$form->ilabel=_("Date");
	$form->ihelp="";
	$form->ivalue=$date;
	$form->nextitem();

	$form->itype="time";
	$form->iname="time";
	$form->ilabel=_("Time");
	$form->ihelp="";
	$form->ivalue=$time;
	$form->nextitem();
	*/
	
	$frame2->draw($form);
	
	
	$page->close();
	
?>
