<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 28/06/2006					*
	*																*
	****************************************************************/

include_once "lib_common.php";

class Act {
	
	var $input;
	var $formname;
	var $actident;
	var $refer;
	
	function load_act ($sec) {
		if (file_exists(DIRNLIB."cls_act_$sec.php")) {
			include_once DIRNLIB."cls_act_$sec.php";
		}
	}	

	function get_text($status,$actident) {
		if ($status==true) {
			$t = _("Alterations saved successfully");
		} else {
			// aqui vai vir um link pro usuario clicar e ver detalhes do erro
			$t = sprintf(_("Error #%s"),$actident);
		}
		return $t;
	}
	
	function html_status ($status,$t) {
		$formname = $this->formname;
		if (is_bool($status)&&$status==true) {
			$status="ok";
		} else if (is_bool($status)&&$status==false) {
			$status="fail";
		}
		include DIRHTML."html_status.php";
	}
	function html_redirect ($url,$parent=0) {
		//echo $url;
		//print_r($_SERVER);
		include DIRHTML."html_redirect.php";
	}
	
	function html_framelog ($title,$action,$details=0) {
		
		$action = urlencode($action);
		$title = urlencode($title);
		//echo $this->refer;
		
		$this->html_redirect(append_url($this->refer,"title=$title&action=$action&details=$details"),1);
		
	}

	function execute ($act) {
		$this->actident 	= conv::randkey();
		if (is_array($this->input)) {
			// removendo alguns caracteres q foram colocados para compor o visual.
			$this->input[id] = str_replace(array("<s>","</s>"),"",$this->input[id]);
			
			
			//foreach ($this->input as $key=>$value) {
				
				// toda essa gambiarra eh pq o allowed_tags do strip_tags nao suporte o tag de comentario, 
				// e eu jah estava usando ele pra passar o ID escondido
				/*
				$this->input[$key] = str_replace("<!--","<comment>",$this->input[$key]);
				$this->input[$key] = str_replace("-->","</comment>",$this->input[$key]);
				
				$this->input[$key] = strip_tags($this->input[$key],'<comment>');
				
				$this->input[$key] = str_replace("<comment>","<!--",$this->input[$key]);
				$this->input[$key] = str_replace("</comment>","-->",$this->input[$key]);			
				*/
				
			//}
		}
		//print_r($this->input);
		
		switch (strtoupper($act)) {
			case "MERGE":
				$conf = new Conf();
				$conf->merge($this->actident);

				break;
			case "DISCARD":
				$conf = new Conf();
				$conf->discard($this->actident);
				
				break;
			case "LICENSEINFO":
				$this->load_act("info");
				$a = new act_licenseinfo($this->actident);
				$status = $a->process($this->input);
				$t = $this->get_text($status,$this->actident);
				if (is_string($status)&&$status=="NULLID") {
					$this->html_status(false,_("Error: ID and Key cannot be null"));
				} else {
					$this->html_status($status,$t);
				}
				
				break;
			case "CHECKLICENSE":
				$conn = new Conn();
				record::wall($this->actident,
					message::generate_function("INFO",_("Checking Registry..."))."\n");
				$conn->command(message::generate_function("CHECKLICENSE"),$this->actident);

				break;

			case "REQUESTLICENSE":
				$conn = new Conn();
				record::wall($this->actident,
					message::generate_function("INFO",_("Requesting Registry..."))."\n");
				$conn->command(message::generate_function("REQUESTLICENSE"),$this->actident);

				break;
			/*
			case "PREFETCHUPDATES":
				$conn = new Conn();
				record::wall($this->actident,
					message::generate_function("INFO",_("Prefetching List..."))."\n");
				$conn->command(message::generate_function("NPAK","getlist"),$this->actident);
				//record::wall($this->actident,$out);
				
				break;	
			*/
			case "MANUALUPDATE":
				// aqui vou receber o arquivo e copiar pro temp
				// ai chamo o installupdate com o nome dele no temp
			
				$file = $_FILES[manualupdate_file][name];
				$pack = explode("/",urldecode($file));
				$pack = $pack[count($pack)-1];
				$file0 = $pack; // deixando apenas o nome do arquivo
				$pack = explode(".",$pack);
				$pack = $pack[0];
			
				ob_start();
				echo "file: $file // pack: $pack\n";
				print_r($_FILES);
				$tmpfile = $_FILES[manualupdate_file][tmp_name];
				$file = "/tmp/$pack.npak";
				shell_exec("cp -af $tmpfile $file");
				
				file_put_contents("/tmp/saida2",ob_get_contents());
				ob_end_clean();
				
				//echo $file;
		
				
				$this->html_framelog(_("Install Update"),"INSTALLUPDATE_".$file0);
				break;
		
		
			case "NETWORKSETUP_DO_EDIT":
				$this->html_redirect("/network/cardedit.php?editid=".$this->input[id]);
				break;
			case "NETWORKSETUP_DO_NEW":
				$this->html_redirect("/network/cardedit.php?newid=".$this->input[id]);
				break;
			case "NETWORKSETUP_DO_GRAPH":
				$this->html_redirect("/control/sysgraph.php?login=".$this->input[id]);
				break;
			case "NETWORKSETUP_CARDEDIT":
				$this->load_act("network");
				$a = new act_networksetup($this->actident);
				$status = $a->process($this->input);
				//$t = $this->get_text($status,$this->actident);
				if (is_string($status)&&$status=="INVALIDFIRSTDHCP") {
					$this->html_status(false,_("Error: Invalid first DHCP IP address"));
				} else if (is_string($status)&&$status=="INVALIDADDRESS") {
					$this->html_status(false,_("Error: Invalid static IP address"));
				} else if (is_string($status)&&$status=="ONEINTERNAL") {
					$this->html_status(false,_("Error: Only one internal interface allowed"));
				} else if (is_string($status)&&$status=="NULLDNS") {
					$this->html_status(false,_("Error: DNS required"));
				} else {
					$this->html_redirect("/network/index.php?status=".$status,1);
				}
				break;
			case "DNS":
			
				$this->load_act("network");
				$a = new act_networksetup($this->actident);
				$status = $a->processdns($this->input);
				$t = $this->get_text($status,$this->actident);
				$this->html_status($status,$t);
				
				break;
			case "PRIMARY":
			
				$this->load_act("network");
				$a = new act_networksetup($this->actident);
				$status = $a->processprimary($this->input);
				$t = $this->get_text($status,$this->actident);
				$this->html_status($status,$t);
				
				break;		
				
			case "USEREDIT_DO_EDIT":
			case "USEREDIT_DO_NEW":
				$this->html_redirect("/control/useredit.php?editid=".$this->input[id]);
				break;
			case "USEREDIT_DO_GRAPH":
				$this->html_redirect("/control/graph.php?login=".$this->input[id]);
				break;
			case "USEREDIT_DO_REMOVE":
				// vou fazer a acao de remover e soh passo o id do undo de volta
				$this->load_act("user");
				$a = new act_useredit($this->actident);
				$status = $a->remove($this->input);
				$undo = $this->actident;
				
				$this->html_redirect("/control/user.php?status=".$status."&undo=$undo");
				break;
			case "USEREDIT_DO_DISCONNECT":
				$this->load_act("user");
				$a = new act_useredit($this->actident);
				$status = $a->disconnect($this->input);
				$undo = $this->actident;
				
				$this->html_redirect("/control/user.php?status=".$status);
				break;
			case "USEREDIT_DO_ENABLE":
				$this->load_act("user");
				$a = new act_useredit($this->actident);
				$status = $a->enable($this->input);
				$undo = $this->actident;
				
				$this->html_redirect("/control/user.php?status=".$status);
				break;
			case "USEREDIT":
				$this->load_act("user");
				$a = new act_useredit($this->actident);
				$status = $a->process($this->input);
				$undo = $this->actident;
				if (is_string($status)&&$status=="LOGINEXISTS") {
					$this->html_status(false,_("Error: Login already exists"));
				} else if (is_string($status)&&$status=="LOGINNULL") {
					$this->html_status(false,_("Error: Login null"));
				} else if (is_string($status)&&$status=="IPREQUIRED") {
					$this->html_status(false,_("Error: This plan is PPPoE, please enter a IP"));
				} else if (is_string($status)&&$status=="PLANREQUIRED") {
					$this->html_status(false,_("Error: Please Select a Plan"));
				} else if (is_string($status)&&$status=="INVALIDIP") {
					$this->html_status(false,_("Error: Invalid Ip"));
				} else if (is_string($status)&&$status=="INVALIDMAC") {
					$this->html_status(false,_("Error: Invalid MAC"));
				} else if (is_string($status)&&$status=="INVALIDLOGIN") {
					$this->html_status(false,_("Error: Invalid Login. Use only letters or numbers"));

				} else {
					$this->html_redirect("/control/user.php?status=".$status."&undo=$undo",1);
				}
				
				break;
				
			case "GUESTCONFIG":
				$this->load_act("user");
				$a = new act_guestconfig($this->actident);
				$status = $a->process($this->input);
				$t = $this->get_text($status,$this->actident);
				$this->html_status($status,$t);
				
				break;
			case "GUEST_DO_EDIT":
			case "GUEST_DO_NEW":
				$this->html_redirect("/control/guestedit.php?editid=".$this->input[id]);
				break;
			case "GUESTEDIT":
				$this->load_act("user");
				$a = new act_guestedit($this->actident);
				$status = $a->process($this->input);
				$undo = $this->actident;
				if (is_string($status)&&$status=="KEYEXISTS") {
					$this->html_status(false,_("Error: Login already exists"));
				} else if (is_string($status)&&$status=="NOTIME") {
					$this->html_status(false,_("Error: Empty value in Expire or Total Minutes"));
				} else {
					$this->html_redirect("/control/guest.php?status=".$status."&undo=$undo",1);
				}
				
				break;
			case "GUEST_DO_REMOVE":
				// vou fazer a acao de remover e soh passo o id do undo de volta
				$this->load_act("user");
				$a = new act_guestedit($this->actident);
				$status = $a->remove($this->input);
				$undo = $this->actident;
				
				$this->html_redirect("/control/guest.php?status=".$status."&undo=$undo");
				break;

			case "PUBLISH_DO_EDIT":
				$this->html_redirect("/control/publishedit.php?editid=".$this->input[id]);
				break;
			case "PUBLISH_DO_REMOVE":
				// vou fazer a acao de remover e soh passo o id do undo de volta
				$this->load_act("user");
				$a = new act_publishedit($this->actident);
				$status = $a->remove($this->input);
				$undo = $this->actident;
				
				$this->html_redirect("/control/publish.php?status=".$status."&undo=$undo");
				break;
			case "PUBLISHEDIT":
				$this->load_act("user");
				$a = new act_publishedit($this->actident);
				$status = $a->process($this->input);
				$undo = $this->actident;
				if (is_string($status)&&$status=="DPORTEXISTS") {
					$this->html_status(false,sprintf(_("Error: %s port already exists"),PRODNAME));
				} else if (is_string($status)&&$status=="INVALIDDEST") {
					$this->html_status(false,_("Error: Invalid destination customer or IP"));
				} else if (is_string($status)&&$status=="INVALIDPORT") {
					$this->html_status(false,_("Error: Invalid Port"));
				} else if (is_string($status)&&$status=="RESERVEDPORT") {
					$this->html_status(false,_("Error: Reserved Port"));
				} else {
					$this->html_redirect("/control/publish.php?status=".$status."&undo=$undo",1);
				}
				break;
			
			case "DATETIME":
				$this->load_act("info");
				$a = new act_datetime($this->actident);
				$status = $a->process($this->input);

				$t = $this->get_text($status,$this->actident);
				$this->html_status($status,$t);
				break;

			case "TIMEZONE":
				$conn = new Conn();
				$conn->command(message::generate_function("TIMEZONE",$this->input[timezone]),$this->actident);
				$t = $this->get_text(true,$this->actident);
				$this->html_status(true,$t);
				break;	

			case "NTPDATE":
				$conn = new Conn();
				record::wall($this->actident,
					message::generate_function("INFO",_("Updating date and time..."))."\n");
				$conn->command(message::generate_function("NTPDATE"),$this->actident);
				break;	

			case "DDCLIENT":
				$this->load_act("network");
				$a = new act_networksetup($this->actident);
				$status = $a->processddclient($this->input);
				$t = $this->get_text($status,$this->actident);
				
				if (is_string($status)&&$status=="REQUIRED") {
					$this->html_status(false,_("Error: All fields must be filled"));
					break;
				}
				$this->html_status($status,$t);
				
				break;
				
			case "DDCLIENTDISABLE":
				$this->load_act("network");
				$a = new act_networksetup($this->actident);
				$status = $a->processddclient(array());
				$this->html_redirect("/setup/ddns.php");
				break;

			case "SHUTDOWN":
				$conn = new Conn();
				$conn->command(message::generate_function("SHUTDOWN","h","now"),$this->actident);
				break;
				
			case "RESTART":
				$conn = new Conn();
				$conn->command(message::generate_function("SHUTDOWN","r","now"),$this->actident);
				break;

			case "ADMINPASS":
				$conn = new Conn();
				
				$ret = $conn->command(message::generate_function("CHECKROOT",$this->input[oldpassword]));
				if (trim($ret)=="OK") {
					$conn->command(message::generate_function("CHANGEROOT",$this->input[password]));
					$this->html_redirect("/setup/admin.php",1);
				} else {
					$this->html_status(false,_("Invalid Password"));
				}
				break;
				
			case "DOPING": 
				//echo "pinging";
				if (trim($this->input[host])=="") {
					$this->html_status(false,_("Empty Host"));
				} else {
					$this->html_framelog("Pinging Host","PING_".$this->input[host],1);
				}
				break;
			case "DOTRACEROUTE": 
				//echo "pinging";
				if (trim($this->input[host])=="") {
					$this->html_status(false,_("Empty Host"));
				} else {
					$this->html_framelog(_("Tracing Route to Host"),"TRACEROUTE_".$this->input[host],1);
				}
				break;
			case "CLEANALERTS":
				$conn = new Conn();
				$conn->command(message::generate_function("CMD","rm -fr ".DIRDATA."/events.ser"));
				
				$this->html_redirect("/setup/server.php");
				break;

			case "CUSTOMALERTS":
			case "CUSTOMLOGIN":
				$this->load_act("custom");
				$a = new act_customsetup($this->actident);
				if ($_FILES[customlogin_logo][name]) {
					$this->input[logo]=$_FILES[customlogin_logo];
				}
				$status = $a->process($this->input);
				if (is_string($status)&&$status=="INVALIDIMAGE") {
					$this->html_status(false,_("Error: Please upload only GIF or JPEG images"));
				} else if (is_string($status)&&$status=="INVALIDFORCEURL") {
					$this->html_status(false,_("Error: Invalid URL"));
				} else {
					$t = $this->get_text($status,$this->actident);
					$this->html_status($status,$t);
				}
				
				break;
			case "CUSTOMRESTORE":
				$this->load_act("custom");
				$a = new act_customsetup($this->actident);

				$status = $a->restore($this->input[lang]);
				if (!$status) {
					$this->html_status(false,_("Error: Language not found in system defaults"));
				} else {
					$this->html_redirect("/custom/login.php?status=$status",1);
				}
				
				break;
			case "BILLINGDAY":
				$this->load_act("custom");
				$a = new act_customsetup($this->actident);
				$status = $a->processbill($this->input);
				$t = $this->get_text($status,$this->actident);
				$this->html_status($status,$t);
				
				break;
			
			case "EDITPLAN":
				$this->load_act("plan");
				$a = new act_plan($this->actident);
				$planname = $a->processplan($this->input);
				$urlplanname = urlencode($planname);
				
				if ($planname=="NULLNAME") {
					$this->html_status(false,_("Error: Invalid Plan name"));
					return;
				} else if ($planname=="INVALIDSPEED") {
					$this->html_status(false,_("Error: Invalid Speed. Only numbers"));
					return;
				} else if ($planname=="INVALIDNAME") {
					$this->html_status(false,_("Error: Invalid Name. Only letters or numbers"));
					return;
				}
				
				if (trim($this->input[editid])==trim($planname)) {
					$t = $this->get_text(true,$this->actident);
					$this->html_status(true,$t);
				} else {
					$this->html_redirect("/control/planedit.php?editid=$urlplanname",1);
				}
				
				
				break;

			case "PLAN_DO_EDIT":
				$this->html_redirect("/control/planedit.php?editid=".$this->input[id]);
				break;
				
			case "PLAN_DO_REMOVE":
				// vou fazer a acao de remover e soh passo o id do undo de volta
				$this->load_act("plan");
				$a = new act_plan($this->actident);
				$status = $a->removeplan($this->input);
				$undo = $this->actident;
				
				$this->html_redirect("/control/plan.php?status=".$status."&undo=$undo");
				break;
				
			case "ACLEDIT":
				$this->load_act("plan");
				$a = new act_plan($this->actident);
				$status = $a->processacl($this->input);
				
				$urlplanname = urlencode($this->input[planname]);
				
				if (is_string($status)) {
					if ($status=="INVALIDDPORT") {
						$this->html_status(false,_("Error: Invalid Destination Port"));
					} else if ($status=="INVALIDDST") {
						$this->html_status(false,_("Error: Invalid Destination"));
					} else if ($status=="INVALIDDAYS") {
						$this->html_status(false,_("Error: Select at least one Weekday"));
					} else if ($status=="INVALIDBAND") {
						$this->html_status(false,_("Error: Invalid Speed Rate"));
					} else if ($status=="INVALIDSITELISTNAME") {
						$this->html_status(false,_("Error: Enter a Name for this site list"));
					} else if (conv::startwith("ERRORIPBLOCK_",$status)) {
						$tmp=explode("_",$status);$line = $tmp[1];
						$this->html_status(false,sprintf(_("Error on IP block list, line %s"),$line));
					} else if (conv::startwith("ERRORIPUNBLOCK_",$status)) {
						$tmp=explode("_",$status);$line = $tmp[1];
						$this->html_status(false,sprintf(_("Error on IP block exception list, line %s"),$line));
					}
					return;
				}				
				$this->html_redirect("/control/planedit.php?status=".$status."&editid=$urlplanname",1);
				break;

			case "BACKUP_DO_RESTORE":
				ereg("<!--(.*)-->(.*)",$this->input[id],$tmpreg);
				$id = intval($tmpreg[1]);
				
				$file = DIRDATA."/backupconf/conf-$id.tgz";
				
				$conf = new Conf();
				$conf->ident = $this->actident;
				$conf->restore($file);
				
				$undo = $this->actident;
				
				record::act_log(_("Backup restored"));
				
				$status = true;
				$this->html_redirect("/setup/backup.php?status=".$status."&undo=$undo");
				break;
				
			case "BACKUP_DO_DOWNLOAD":
				ereg("<!--(.*)-->(.*)",$this->input[id],$tmpreg);
				$id = intval($tmpreg[1]);
				$file = DIRDATA."/backupconf/conf-$id.tgz";
				$date = conv::formatdatefile($id);
				$newfile = "netunne-confbackup-$date.tgz";
				
				conv::download($file,$newfile);
				
				/*
				
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header("Content-Disposition: attachment; filename=\"".basename($filename)."\";");
				
				header('Content-Length: ' . filesize($file));
				@readfile($file) OR die();
				
				*/
				
				break;
				
			case "BACKUP_MANUALRESTORE":
				$file = $_FILES[backup_manualrestore_file][name];
				$filename = basename($file);

				$fileto = "/tmp/$filename";
				$tmpfile = $_FILES[backup_manualrestore_file][tmp_name];
				shell_exec("cp -af $tmpfile $fileto");
				
				
				/*
				ob_start();
				print_r($_FILES);
				echo "\nfile: $fileto";
				file_put_contents("/tmp/saida5",ob_get_contents());
				ob_end_clean();
				*/
				
				
				$conf = new Conf();
				$conf->ident = $this->actident;
				if (!$conf->restore($fileto)) {
					$status = "error";
					$msg = base64_encode(_("Invalid backup file to restore"));
				} else {
					record::act_log(_("Manual backup restored"));
					$status = true;
				}
				$undo = $this->actident;
				
				$this->html_redirect("/setup/backup.php?msg=".$msg."&status=".$status."&undo=$undo",1);
				
				break;
				
			case "LOCALE":
				$this->load_act("info");
				$a = new act_locale($this->actident);
				$status = $a->process($this->input);
				$t = $this->get_text($status,$this->actident);
				$this->html_redirect("/setup/admin.php",1);
				
				break;
				
			case "USER_EXPORT":
				$this->load_act("user");
				$a = new act_useredit($this->actident);
				$absfile = $a->export_csv();
				$undo = $this->actident;
				conv::download($absfile,"clients.csv");
				// JAH TAH OK.
				
				break;

			case "USER_IMPORT":
				// aqui vou receber o arquivo e copiar pro temp
				// ai chamo o installupdate com o nome dele no temp
			
				$file = $_FILES[manualupdate_file][name];
				$pack = explode("/",urldecode($file));
				$pack = $pack[count($pack)-1];
				$file0 = $pack; // deixando apenas o nome do arquivo
				$pack = explode(".",$pack);
				$pack = $pack[0];
				
				// PRECISA PROCESSAR O CVS, RETORNAR UM ARQUIVO TEXTO
				// E ENVIAR PRA UMA PAGINA DIZENDO O RESULTADO E AVISANDO:
				// CONFIRMA ou VOLTE E CORRIJA o SEU CSV.

				break;
				
			case "CONNCHECK":
				$this->load_act("network");
				$a = new act_networksetup($this->actident);
				$status = $a->processconncheck($this->input);
				$t = $this->get_text($status,$this->actident);
				$this->html_status($status,$t);
				
				break;
				
			case "INSTALLPROXY":
				$conn = new Conn();
				record::wall($this->actident,
					message::generate_function("INFO",_("Installing..."))."\n");
				$conn->command(message::generate_function("INSTALLPROXY"),$this->actident);
				
				break;
				
			case "CLEANPROXY":
				$conn = new Conn();
				record::wall($this->actident,
					message::generate_function("INFO",_("Cleaning..."))."\n");
				$conn->command(message::generate_function("CLEANPROXY"),$this->actident);
			
				break;
			case "PROXY":
				$this->load_act("network");
				$a = new act_proxysetup($this->actident);
				$status = $a->process($this->input);
				
				if (is_string($status)&&$status=="CACHETOOBIG") {
					$this->html_status(false,_("Error: Your cache is too big"));
					return;
				}
				
				$t = $this->get_text($status,$this->actident);
				$this->html_status($status,$t);
				
				break;

			case "CHANGECUSTOMLOGIN":
				$this->load_act("custom");
				$a = new act_customsetup($this->actident);
				$status = $a->changecustomlogin();
				// redireciona pra login.php
				
				$this->html_redirect("/custom/login.php");
				
				break;


			case "UPLOADPAGE":
			
				$this->load_act("custom");
				$a = new act_customsetup($this->actident);
				if ($_FILES[uploadpage_logo][name]) {
					$this->input[logo]=$_FILES[uploadpage_logo];
				}
				$status = $a->uploadpage($this->input);
				
				$this->html_redirect("",1);
				
				break;

			case "CUSTOMTHEME":
				$this->load_act("custom");
				$a = new act_customsetup($this->actident);
				$status = $a->changetheme($this->input);
				$this->html_status($status,$t);
			
				break;
				
			case "CONFOLD":
				$this->load_act("info");
				$a = new act_confold($this->actident);
				$status = $a->process($this->input);
				$t = $this->get_text($status,$this->actident);
				$this->html_status($status,$t);
				
				break;

			// FUNCOES DE ACL ESTAO ABAIXO

		}
		
		/*
		 * ACOES PARAMETRIZADAS
		 */
		
		if (conv::startwith("DISCONNECTUSER_",$act)) {	
			$user = strtolower(str_replace("DISCONNECTUSER_","",strtoupper($act)));
			$conn = new Conn();
			record::wall($this->actident,
				message::generate_function("INFO",_("Disconnecting User..."))."\n");
			$conn->command(message::generate_function("DISCONNECT",$user),$this->actident);
			
			$this->load_act("user");
			$a = new act_useredit();
			$a->disable($user);
		}
		
		if (conv::startwith("ENABLEINTERFACE_",$act)) {	
			$int = strtolower(str_replace("ENABLEINTERFACE_","",strtoupper($act)));
				
			$conn = new Conn();
			record::wall($this->actident,
				message::generate_function("INFO",_("Enabling Interface..."))."\n");
			$conn->command(message::generate_function("ENABLEINTERFACE",$int),$this->actident);
		}
		
		if (conv::startwith("ACL_REMOVE_",$act)) {
			$planname = strtoupper($act);
			$planname = urldecode(str_replace("ACL_REMOVE_","",$planname));
			$this->input[planname]=$planname;
			$urlplanname = urlencode($planname);
			
			$this->load_act("plan");
			$a = new act_plan($this->actident);
			$status = $a->removeacl($this->input);
			$undo = $this->actident;

			$this->html_redirect("/control/planedit.php?status=".$status."&undo=$undo&editid=$urlplanname");
			
		}
		if (conv::startwith("ACLBLOCK_DO_EDIT_",$act)) {
			$func="block";
			$editacl=1;
		}
		if (conv::startwith("ACLBAND_DO_EDIT_",$act)) {
			$func="band";
			$editacl=1;
		}
		if ($editacl) {
			$planname=strtoupper($act);
			$planname = urldecode(str_replace("ACLBLOCK_DO_EDIT_","",$planname));
			$planname = urldecode(str_replace("ACLBAND_DO_EDIT_","",$planname));
			if ($this->input[id]) {
				$id = explode("<!--",$this->input[id]);
				$id = str_replace("-->","",$id[1]);
			} else {
				$id="new";
			}
			$this->html_redirect("/control/acledit.php?func=$func&editid=$id&editplan=$planname");
		}
			
		if (conv::startwith("PING_",$act)) {
			$tmp = explode("_",$act);
			$host = $tmp[1];
			$conn = new Conn();
			record::wall($this->actident,
				message::generate_function("INFO",sprintf(_("Pinging %s..."),$host))."\n");
			$conn->command(message::generate_function("PING",$host),$this->actident);
			$this->html_redirect($_SERVER["HTTP_REFERER"]);
		}
		if (conv::startwith("TRACEROUTE_",$act)) {
			$tmp = explode("_",$act);
			$host = $tmp[1];
			$conn = new Conn();
			record::wall($this->actident,
				message::generate_function("INFO",sprintf(_("Tracing route to %s..."),$host))."\n");
			$conn->command(message::generate_function("TRACEROUTE",$host),$this->actident);
			$this->html_redirect($_SERVER["HTTP_REFERER"]);
		}
		if (conv::startwith("INSTALLUPDATE_",$act)) {
			$tmp = explode("_",$act);
			$pkg = urldecode($tmp[1]);
			$conn = new Conn();
			record::wall($this->actident,
				message::generate_function("INFO",_("Initializing Install..."))."\n");
			$conn->command(message::generate_function("NPAK","install",$pkg),$this->actident);
		}
		
		if (conv::startwith("GO_",$act)) {
			$tmp = explode("_",$act);
			if ($tmp[1]=="REFER")
				$tmp[1]=$this->refer;
				
			$this->html_redirect(strtolower($tmp[1]));
		}
		
		if (conv::startwith("UNDO_",$act)) {
		//if (strtoupper(substr($act,0,5))=="UNDO_") {
			$tmp = explode("_",$act);
			$c = new Conf();
			$c->undo($tmp[1]);
			//print_r($_SERVER);
			//$this->html_redirect($_SERVER["HTTP_REFERER"]);
			$this->html_redirect(append_url($this->refer,"undone=1"));
			//$this->html_redirect("/control/user.php?status=".$act."&undo=$undo",1);
		} 
	}
	
	function process ($act,$result) {
		switch (strtoupper($act)) {
			case "PREFETCHUPDATES":
				$ret = message::has_function($result,"result");
				//echo "<hr>$result<hr>";
				if ($ret) {
					file_put_contents(DIRSET."npak.list",$ret);
					return "\n".message::generate_function("OK",_("List Received Sucefully"))."\n\n";
				} else {
					return "\n".message::generate_function("FAIL",_("Cannot receive list"))."\n\n";
				}
				break;
			case "MERGE":

				$ret = message::has_function($result,"result");
				if ($ret) {
					$msg = $result."\n";
					
					if (substr_count($result,"INVALIDLICENSE")!=0) {
						$lic=1;
					}
					
					$msg.= message::generate_function("OK",_("Changes Applied"))."\n\n";
					if ($lic) { 
						$msg.= message::generate_function("FAIL",_("Network Applied.\nInvalid Registry."))."\n\n";
					}
					return $msg;
				}		
				break;
			case "INSTALLPROXY":
				// vo pegando de tras pra frente o resultado a procura do OK solitario
				$success=false;
				$result1 = explode("\n",$result);
				
				while ($res = array_pop($result1)) {
					if (trim($res)=="") 	{ continue; }
					if (trim($res)=="OK") 	{ $success=true; break; }
				}

				if ($success) {
					$confobj = new Conf("proxy");
					$confobj->conf[proxy]["new"] = 0;
					$confobj->write();
					unset($confobj);
					record::act_log(_("Proxy Installed"));
					
					return $result."\n\n".message::generate_function("OK",_("OK"))."\n\n";
				} else {
					return $result."\n\n".message::generate_function("ERROR",_("Error"))."\n\n";
				}
			

				
				break;
			
				
			default:
				if (message::has_function($result,"result")) {
					return $result."\n".message::generate_function("INFO",_("Action Performed. <BR>Click View Details."))."\n\n";
				}
				return $result;
		}
	}
}


?>
