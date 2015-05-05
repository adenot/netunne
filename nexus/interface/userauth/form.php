<?php

ignore_user_abort(true);
set_time_limit(30);

// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

if ($_POST[url]) {
	$url = $_POST[url];
} else if ($_GET[url]){
	$url = base64_decode(urldecode($_GET[url]));
} else {
	$url = "http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
}

$user = $_POST[username];
$pass = $_POST[password];
$key = $_POST["key"];

include "../nlib/lib_common.php";
$custom = @parse_ini_file(DIRSET."settings.ini",1);
$custom=$custom[custom];

	
	// Traducoes novas
	if (!$custom['newpassword'])
		$custom['newpassword'] = _("New Password");
	if (!$custom['changepass'])
		$custom['changepass'] = _("Change Password");
		
	// antigas
	if (!$custom[welcome])
		$custom[welcome] = _("Welcome to Netunne");
	if (!$custom[poslogin])
		$custom[poslogin]= _("Customer Login");
	if (!$custom[prelogin])
		$custom[prelogin] = _("Credit Login");
	if (!$custom[login])
		$custom[login] = _("User");
	if (!$custom[password])
		$custom[password] = _("Password");
	if (!$custom[key])
		$custom[key] = _("Key");
	if (!$custom[button])
		$custom[button] = _("Enter");
	if (!$custom[title])
		$custom[title] = _("Netunne Internet Access");

	if (!$custom[changemac])
		$custom[changemac] = _("Access Denied: MAC Changed");


$ip = $_SERVER["REMOTE_ADDR"];

if ($_POST[func]) {
	$arp = new cmd_Arp();
	$mac = $arp->getmac($ip);
	$conn = new Conn();
}

if ($_POST[func]=="auth") {

		$out = $conn->command(message::generate_function("CHECKUSER",$user,$pass,$ip,$mac));
		//echo "OUT:".$out;
		$out = message::has_function($out,"RESULT");
		if ($out!=FALSE) {
			if (trim(strtoupper($out[0]))=="USERFAIL") {
				$msg = _("Invalid User");
			} else if (trim(strtoupper($out[0]))=="FAIL") {
				$msg = _("Incorrect Password");
			} else if (trim(strtoupper($out[0]))=="TRYAGAIN") {
				$msg = _("Error: Please Try Again");
			} else if (trim(strtoupper($out[0]))=="OUTLIMIT") {
				$msg = $custom[limit];
			} else if (trim(strtoupper($out[0]))=="PPPOEONLY") {
				$msg = _("User cannot login: PPPoE only");
			} else if (trim(strtoupper($out[0]))=="DISABLED0") {
				$msg = _("User Disabled");
			} else if (trim(strtoupper($out[0]))=="DISABLED1") {
				$msg = $custom[discustom1];
			} else if (trim(strtoupper($out[0]))=="DISABLED2") {
				$msg = $custom[discustom2];
			} else if (trim(strtoupper($out[0]))=="DISABLED3") {
				$msg = $custom[discustom3];
			} else if (trim(strtoupper($out[0]))=="CHANGEMAC") {
				$msg = $custom[changemac];
			} else if (trim(strtoupper($out[0]))=="CHANGEIP") {
				$msg = $custom[changemac]; // por enquanto a mesma de changemac
			} else if (trim(strtoupper($out[0]))=="OK") {
				$redir = $url;
				$msg = trim($out[1]);
				$fastauth=1; // soh pra enganar embaixo...
				$custom[welcome]="";
			}
		} else {
			$msg = _("Error: Contact the system administrator");
		}
} else if ($_POST[func]=="guest") {
		$out = $conn->command(message::generate_function("CHECKGUEST",$key,$ip,$mac));
		$out = message::has_function($out,"RESULT");
		if ($out!=FALSE) {
			if (trim(strtoupper($out[0]))=="FAIL") {
				$msg = _("Incorrect Password");
			} else if (trim(strtoupper($out[0]))=="OK") {
				$redir = $url;
				$fastauth=1;
				$msg = _("Authentication Ok, redirecting...");
			}
		} else {
			$msg = _("Error: Contact the system administrator");
		}
 
} else if ($_GET[func]=="changepass") {
	$changepass=1;
	
}  else if ($_POST[func]=="changepass") {
	$out = $conn->command(message::generate_function("CHANGEPASS",$_POST[username],$_POST[password],$_POST[newpassword]));
	$out = message::has_function($out,"RESULT");
	if ($out!=FALSE) {
		if (trim(strtoupper($out[0]))=="FAIL") {
			$msg = _("Incorrect Password");
		} else if (trim(strtoupper($out[0]))=="OK") {
			$redir = $url;
			//$fastauth=1;
			$msg = _("Password changed successfully");
		}
	} else {
		$msg = _("Error: Contact the system administrator");
	}
}




/*
 * PRECISA DIZER.. AGUARDE, REDIRECIONANDO
 * Quando existe a variavel $redir
 */

	$themedefault="default";
	
	if ($custom[theme])
		$theme = $custom[theme];
	else
		$theme = $themedefault;
	
	$actFormPos = "/form.php";
	$actFormPre = "/form.php";


	if (trim($custom['forceurl'])!=""&&$redir) {
		 $redir=$custom['forceurl'];
	}

	// se nao tiver fastauth, entao mostra o form
	if ($fastauth) {
		
		if (file_exists("theme/$theme/alert.htm"))
			$text = file_get_contents("theme/$theme/alert.htm");
		else 
			$text = file_get_contents("theme/$themedefault/alert.htm");
			
		// se tiver nao mostra o form e mostra o link
		//$text = str_replace("{goto_button}",$redir,$text);
		
		$text = str_replace("{msg}",$msg,$text);
		$text = str_replace("{goto_text}",sprintf(_("You have logged in. You will be redirected within five seconds. If not, click <a href='%s'>here</a> to continue."),$redir),$text);
	} else {
	
		# Mostra a linha de alerta
		if (trim($msg)!="") {
			if (file_exists("theme/$theme/alert.htm"))
				$text = file_get_contents("theme/$theme/alert.htm");
			else 
				$text = file_get_contents("theme/$themedefault/alert.htm");
			$text = str_replace("{msg}",$msg,$text);
			$referer = $_SERVER["HTTP_REFERER"];
			$text = str_replace("{goto_text}",sprintf(_("<a href='%s'>return to login page</a> "),$referer),$text);
			unset($redir);
			
			
			// se nao tiver mensagem, nao para pra clicar
			// falta mensagem quando fixa
		} else {
			if ($changepass) {
				if (file_exists("theme/$theme/password.htm"))
					$text = file_get_contents("theme/$theme/password.htm");
				else 
					$text = file_get_contents("theme/$themedefault/password.htm");
			} else {
				if (file_exists("theme/$theme/form.htm"))
					$text = file_get_contents("theme/$theme/form.htm");
				else 
					$text = file_get_contents("theme/$themedefault/form.htm");
			}
			
		}
	}

	

	if (trim($custom['title'])!="") {
		$text = str_replace("{window_title}",$custom['title'], $text);
	} else {
		$text = str_replace("{window_title}",sprintf(_("%s Internet Access"),constant("PRODNAME")), $text);
	}
	$text = str_replace("{welcome_message}",str_replace("\n","<br>",stripslashes($custom['welcome'])), $text);
	
	if (file_exists(DIRAUTH."/logo/logo.gif"))
		$logofile = "logo.gif";
	else if (file_exists(DIRAUTH."/logo/logo.jpg"))
		$logofile = "logo.jpg";
	else
		$logofile = "default.jpg";

	$text = str_replace("{logo_img}",$logofile,$text);

	
	$urlchangepass = "/form.php?url=".urlencode($_GET[url])."&func=changepass";
	$frmchangepass = "/form.php?url=".urlencode($_GET[url]);
	
	# Form Pos
	$text = str_replace("{frm_customer1}",$actFormPos, $text);
	$text = str_replace("{url}",$url, $text);
	$text = str_replace("{customer_title1}",$custom['poslogin'], $text);
	$text = str_replace("{customer_login1}",$custom['login'], $text);
	$text = str_replace("{customer_pass1}",$custom['password'], $text);
	$text = str_replace("{customer_newpass1}",$custom['newpassword'], $text);
	$text = str_replace("{customer_button1}",$custom['button'], $text);
	
	$text = str_replace("{url_changepass}",$urlchangepass, $text);
	$text = str_replace("{customer_changepass}",$custom['changepass'], $text);
	$text = str_replace("{frm_changepass}",$frmchangepass, $text);
	
	$text = str_replace("{url_return}",$_SERVER["HTTP_REFERER"], $text);
	$text = str_replace("{customer_return}",_("Back"), $text);
	
	
	# Form Pre
	$text = str_replace("{frm_customer2}",$actFormPre, $text);
	$text = str_replace("{url}",$url, $text);
	$text = str_replace("{customer_title2}",$custom['prelogin'], $text);
	$text = str_replace("{customer_key}",$custom['key'], $text);
	$text = str_replace("{customer_button2}",$custom['button'], $text);
	
	
	$text = str_replace("{theme}",$theme, $text);
	
	echo $text;
	
	
?>
<script language="JavaScript">
function loadurl () {
<?php if ($redir) { ?>
	//alert('<?=urldecode($redir)?>');
	window.setTimeout("window.location='<?=$redir?>';",5000);
<?php } ?>
}
</script>
</HTML>