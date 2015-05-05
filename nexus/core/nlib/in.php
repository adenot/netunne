<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 21/02/2006					*
	*																*
	****************************************************************/


include_once "common.nx";
include_once "out.nx";
//include_once "valid.nx";

function merge ($service,$confonly=0) {
	echo "Merging $service\n";
	clearstatcache();
	
	// A ORDEM IMPORTA !!
	$validservices=array("FORWARD","NETWORK","DHCP","PROXY");
	// dhcp fica por ultimo pq o network e o forward mechem nele
	$nonetwork=array("FORWARD","DHCP","PROXY");
	
	if(trim($service) == "ALL") {
		foreach ($validservices as $service) {
			$ret .= merge($service)."\n";
		}
		return $ret;
	} else if (trim($service) == "NONETWORK") {	
		foreach ($nonetwork as $service) {
			$ret .= merge($service)."\n";
		}
		return $ret;
	}
	
	if (!in_array($service,$validservices))
		return -1;
		
	$ucservice = ucfirst(strtolower($service));
	$lcservice = strtolower($service);
	__autoload($ucservice);
	if (!class_exists($ucservice))
		return -2;

	$cls = new $ucservice();
	$tplfile = DIRTPL."/$lcservice.xml.tpl";


	//if ($conf[act][pre])
	//		$res .= shell_exec($conf[act][pre]);
	if (file_exists($tplfile)) {
		$tpl = xml::loadxml($tplfile);
		if ($tpl[act][pre] && !$confonly) {
			$pre = explode("\n",$tpl[act][pre]);
			foreach ($pre as $cmd) {
				if (trim($cmd)=="") { continue; }
				// convertendo a variavel nexus
				$cmd = str_replace("\$NEXUS",NEXUS,trim($cmd));

				record::msg_log("Executing {".$cmd."}",$lcservice);
				$res .= shell_exec(html_entity_decode($cmd))."\n";
			}
		}		
	}


	
	$cls->merge();
	$conf = $cls->conf;
		
	if (method_exists($cls,"dependency")) {
		$servdeps = $cls->dependency();
		foreach ($servdeps as $servdep) {

			$res .= merge($servdep);
		}
	}
	unset($cls);

	
	if (file_exists($tplfile)) {
		if ($tpl[maps]) { 
			$maps = xml::normalizeseq($tpl[maps][map]);
			foreach ($maps as $map) {
				$from = html_entity_decode(DIROUT.$map[from]);
				$to   = html_entity_decode($map[to]);
				if (file_exists($from) || (substr_count($from,"*")>0) ) {
					record::msg_log("Copying {".$from."} to {".$to."}",$lcservice);
					echo "Copying {".$from."} to {".$to."}\n";
					$res .= shell_exec("/bin/cp -a $from $to")."\n";
				}
			}
		}
	
		if ($tpl[act][post] && !$confonly) {
			//echo "AA".$tpl[act][post]."BB";
			$post = explode("\n",$tpl[act][post]);
			foreach ($post as $cmd) {
				if (trim($cmd)=="") { continue; }
				// convertendo a variavel nexus
				$cmd = str_replace("\$NEXUS",NEXUS,trim($cmd));
			
				record::msg_log("Executing {".$cmd."}",$lcservice);
				echo "Executing {".$cmd."}\n";
				$res .= shell_exec(html_entity_decode($cmd))."\n";
			}
		}		
	}
	echo "End Merging $service\n";
	return conv::cleanout($res);
}

function copyxml ($path,$xml) {
	if ($xml=="dirproxy") {
		exec ("cp -a $path/proxy ".DIRCONF."/");
		return;
	}
	
	return @copy($path."/$xml.xml",DIRCONF."/$xml.xml");
}
function copyproxy () {
	//shell_exec("rm -fr ".DIRCONF."/proxy/ ;cp -a ".INTERFACECONF."/proxy/ ".DIRCONF);
	shell_exec("cp -a ".INTERFACECONF."/proxy/ ".DIRCONF);
	return true;
}


function normalize () {
	$ret = shell_exec(DIRBIN."normalize.sh 2>&1 > /dev/null");

	$guestsfile = DIRDATA."/user/guest.totals";
	if (!file_exists($guestsfile))
		shell_exec("echo \"\" > $guestsfile");
		
	
	$usersfile = DIRDATA."/user/user.totals";
	if (!file_exists($usersfile))
		shell_exec("echo \"\" > $usersfile");

	return TRUE;	
}
	

/*
 * FUNCOES de ENTRADA 
 * 
 */

function in_teste($var) {
	if ($var=="spike") {
		return out_ok();
	} else {
		return out_fail();
	}
}
function out_boolean ($bol) {
	if ($bol==FALSE) {
		return out_fail();
	} else {
		return out_ok();
	}
}

function initialize ($pid) {
	shell_exec("echo \"$pid\" > ".DIRRUN."nexus.pid");
	//if (ini_set("extension_dir","/usr/lib/php5.1/20051025/"))
	//	echo "SETOU";
	//	else 
	//	echo "nao setou";
	//echo PHP_CONFIG_FILE_PATH;
	//phpinfo();
}


function in_newconnection ($client,$pid) {
	//echo "PID: ".$pid."\n";
	//echo "PEERHOST: ".$client->peerhost()."\n";
	//echo "PEERPORT: ".$client->peerport()."\n";
	return "Welcome\n";
}

function in ($buf) {
	$input = message::input_function($buf);
	//return $input[func];
	
	$checklic = new Checklicense();
	$open_license = $checklic->open_license();
	
	clearstatcache();
	// sempre normalizo antes!
	normalize();
	switch ($input[func]) {
		case "REQUESTLICENSE":	
			$a = new License();
			return $a->request_license();
			
		case "CHECKLICENSE":
			return out_boolean($open_license);
			
		case "MERGE": 										// aqui verifica licensa
			if (file_exists("/root/demo")) 
				return out_boolean(true);
		
			Conf::backup();
		
			if ($open_license==false) {
				// se a licensa tah expirada, soh deixo mergear o network
				return out_invalidlicense()."\n".message::generate_function("result",merge("NETWORK"));
			}		
			return message::generate_function("result",merge(strtoupper($input[service])));
		
		case "MERGECONF":
			if (file_exists("/root/demo")) 
				return out_boolean(true);
		
			if ($open_license==false) {
				// se a licensa tah expirada, soh deixo mergear o network
				return out_invalidlicense();
			}
			return message::generate_function("result",merge(strtoupper($input[service]),1));

		case "CMD":
			if (file_exists("/root/demo")) 
				return out_boolean(true);
				
			// estah sendo usado para ping
			// sendo usado para limpar os events.ser
			return message::generate_function("result",shell_exec($input[c]));
			
		case "CHECKUSER": 									// aqui verifica licensa
			if ($open_license==false)	
				return out_invalidlicense();
							
			$a = new Forward($checklic);
			return message::generate_function("result",$a->checkuser($input[login],$input[pass],$input[ip],$input[mac]));
			
		case "CHECKGUEST": 									// aqui verifica licensa
			if ($open_license==false)	
				return out_invalidlicense();
			$a = new Forward($checklic);
			return message::generate_function("result",$a->checkguest($input["key"],$input[ip],$input[mac]));
			

		case "FASTAUTH": 									// aqui verifica licensa
			if ($open_license==false)
				return out_invalidlicense();
			
			$queue = DIRTMP."nx_fastauth_queue";
			$mypid = getmypid();
			
			if (!file_exists($queue)) {
				// vai criar novo arquivo embaixo
				$exec=true;
				$pid=$mypid."\n";
				
			} else {
				// verifico se o dono da fila morreu, se morreu, eu assumo
				$file = file_get_contents ($queue);
				$line = explode("\n",$file);

				$queuepid = intval($line[0]);
				if (!getpidinfo($queuepid)) {
					// troco o pid e executo abaixo
					$line[0]=$mypid;
					file_put_contents($queue,implode("\n",$line));
					clearstatcache();
					$exec=true;
					
				}
			}

			// criando/adicionando a fila
			
			
			do {
				$fp = fopen($queue,"a");
				if (!$fp) { usleep(100); }
			} while (!$fp);
			fwrite($fp,$pid.$input[ip].";".$input[mac]."\n");
			fclose($fp);
			
			
			//shell_exec ("echo ".$pid.$input[ip].";".$input[mac]." >> $queue");
			
			clearstatcache();
			
			if ($exec) {
				$a = new Forward($checklic);
				//echo "contruido\n";
				
				
				while (true) {
					
					clearstatcache();
					
					if (file_exists(DIRTMP."nx_applying")) {
						// aguardemmmmmmmm
						sleep(1);
						continue;
					}
					
					$file = file_get_contents ($queue);
					$line = explode("\n",$file);
					

					// se nao sobrou nada no line, saio.
					if (trim($line[0])==$mypid && trim($line[1])=="") { break; }
					
					// primeira linha sempre é o pid, entao retiro logo ele
					$pid = array_shift($line);
					
					// sobrou algo, entao vamos pegar
					$tmp = array_shift($line);
					
					// escrevo o arquivo com o q sobrou
					file_put_contents($queue,$pid."\n".implode("\n",$line));
					clearstatcache();

					// se era uma linha em branco, pulo pra proxima
					if ($tmp=="") { continue; }
					
					// senao explodo e chamo o fastauth
					$tmp = explode(";",trim($tmp));
					
					$a->openlinktable(); // ele jah abriu, mas pode estar rolando um merge ai eh melhor abrir de novo pra atualizar
					$ret .= message::generate_function("result",$a->fastauth($tmp[0],$tmp[1]))."\n";
					
					unset($file);unset($line);unset($tmp);
					
					sleep(1);
					
				}
				//echo "apagando queue\n";
				unlink($queue);
					
			} else {
				//echo "soh adicionando na fila\n";
				
				$ret = out_boolean(true);
			}

			return $ret;
			
		case "DISCONNECT":
			$a = new Forward($checklic);
			
			$list_to_remove = explode(",",$input[login]);
			
			foreach ($list_to_remove as $login)
				$a->disconnect_user($login);
				
			return out_boolean(true);

		case "PPPOEDISCONNECT":
			$a = new Forward($checklic);
			return out_boolean($a->disconnect_pppoe($input[ip],$input["int"]));

					
		case "COPYXML":
			return out_boolean(copyxml($input[path],$input[xml]));

		case "COPYPROXY":
			return out_boolean(copyproxy());
			
		case "NORMALIZE":
			return out_boolean(normalize());
					
		case "WALL":
			$GLOBALS[wall]=$input[wall];
			$GLOBALS[wall1]="1";
			return;
			
		case "WAIT":
			for ($i=0;$i<10;$i++) {
				sleep(1);
				record::wall($GLOBALS[wall],"INFO(\"$i\")\n");
			}
			return out_boolean(true);
			
		case "NPAK":
			if (file_exists("/root/demo")) 
				return out_boolean(true);
				
			$a = new Npak();
			if ($input[action]=="getlist") {
				return $a->getlist();
			} else if ($input[action]=="install") {
				$file = $input["file"];
				$pack = explode("/",urldecode($file));
				$pack = $pack[count($pack)-1];
				$pack = explode(".",$pack);
				$pack = $pack[0];
				return $a->install($pack,$file);
			}
							
			return;
		
		case "DATE":
			// [MMDDhhmm[[CC]YY][.ss]]
			
			$date = $input["date"];
			$ret = shell_exec("date \"$date\"");
			$ret.= shell_exec("/etc/init.d/hwclock.sh stop");
			record::msg_log($ret."date: $date","date");
			return out_boolean(true);

		case "NTPDATE":	
			$result = shell_exec("/etc/init.d/ntpdate restart");
			Conf::touchconf();
			return message::generate_function("result",$result);
	
		case "TIMEZONE":
			if (file_exists("/root/demo")) 
				return out_boolean(true);
				
			$tz = $input["tz"];
			shell_exec("rm -fr  /etc/localtime");
			shell_exec("ln -s /usr/share/zoneinfo/Etc/$tz /etc/localtime");
			shell_exec("echo \"Etc/$tz\" > /etc/timezone");
			shell_exec("nohup /etc/init.d/ntpdate restart &");

		case "SHUTDOWN":
			if (file_exists("/root/demo")) 
				return out_boolean(true);
				
			exec ("nohup shutdown \"-".$input[mode]."\" \"".$input[when]."\" &");
			return out_boolean(true);
			
		case "CHECKROOT":
			$ret = Sys::checkrootpass($input[pass]);
			return out_boolean($ret);

		case "CHANGEROOT":
			if (file_exists("/root/demo")) 
				return out_boolean(true);
				
			$ret = Sys::changerootpass($input[pass]);
			return out_boolean(true);
			
		case "BACKUP":
			if (file_exists("/root/demo")) 
				return out_boolean(true);
				
			$ret = Conf::backup();
			return message::generate_function("result",$ret);
			
		case "RESTORE":
			if (file_exists("/root/demo")) 
				return out_boolean(true);
		
			$ret = Conf::restore($input[file]);
			if ($ret==true) {
				merge();
			}
			return out_boolean($ret);
			
		case "ENABLEINTERFACE":
			$ret = Network::enableinterface($input[int]);
			return message::generate_function("ok");
			
		case "PING":
			shell_exec ("ping -c 4 ".$input[host]." >> ".DIRWALL.$GLOBALS[wall].".log");
			return out_boolean(true);
		case "TRACEROUTE":
			shell_exec ("traceroute -n ".$input[host]." >> ".DIRWALL.$GLOBALS[wall].".log");
			return out_boolean(true);
			
		case "INSTALLPROXY":
			$proxy = new Proxy();
			$result = $proxy->install();
			$ret = message::generate_function("result",$proxy->ret)."\n\n";
			$ret.= out_boolean($result);
			return $ret;
			
		case "CLEANPROXY":
			$proxy = new Proxy();
			$ret = $proxy->clean();
			return message::generate_function("result",$ret);
			
			
		case "FORCEUPDATE":
			$npak = new Npak();
			$npak->getlist();
			$npak->autoinstall();
			return message::generate_function("ok");
			
		case "CHANGEPASS":
			$a = new Forward($checklic);
			return message::generate_function("result",$a->changepass($input[username],$input[password],$input[newpassword]));


	}	
}

//var_dump(in("CHECKUSER(spike,visual)"));
//echo md5 ("visual");
?>
