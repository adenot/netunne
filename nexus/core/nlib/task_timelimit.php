<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 17/07/2006					*
	*																*
	****************************************************************/

/* ROTEIRO
 * para cada guest...
 * verifico o idle time
 * 		soma todas as conexoes do guest, se for maior que o total dele, bloqueia e exit.
 * 
 * 		� maior que o idle?
 * 			sim:  
 * 				salva a hora como hora-fim
 * 				fecha a conexao (guest? remove a regra do guest)
 * 			nao:
 * 				salva a hora-fim (mas nao fecha), para o caso do servidor reiniciar
 */
 


$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

file_put_contents(DIRTMP."/nx_timelimit.pid",getmypid());

record::msg_log("Running task_timelimit	...","account");

// esse script eh executado de minuto em minuto:
$cronfreq=60;

echo microtime();
$user = new Forward();
echo microtime();

/////////////////////////////////////////////////////////////////////////////////////
// USUARIOS ONLINE
///////////////////////
///////////////////////

$idle = $user->conf[config][idle];

// CODIGO NOVO

$arptable0 = explode("\n",shell_exec("arp -n"));
for ($i=1;$i<count($arptable0);$i++) {
	list($ip,$type,$mac,$flags,$int) = sscanf($arptable0[$i],"%s%s%s%s%s%s%s");
	$arptable[$ip]=$mac;
}

//print_r($arptable);

foreach ($user->linktable[gateway] as $int => $val) {
	if (!$user->linktable[$int]) { continue; }
	foreach ($user->linktable[$int] as $login => $timelogin) {	
		$tmplogin=explode(".",$login);
		// quem vai tratar guest eh embaixo.. 
		if ($tmplogin[0]=="guest") {
			// USUARIO CREDITO
			// por enquanto fica embaixo mesmo

		} else {
			// USUARIO CLIENTE
			
			$current_user = $user->returnuser($login);
			$userip=$current_user[ip];
			
			if (!array_key_exists($userip,$arptable)) {
				$acc = explode(" ",shell_exec("head -n 1 /proc/net/ipt_account/$login"));
				$useridle = trim($acc[17]);
				$last = time();	
				if ($useridle > $idle) {
					$user->disconnect_user($login,0,$useridle);
				}
			}
		}
	}
}
		
		
// CODIGO ANTIGO
/*
foreach ($linktable[gateway] as $int => $val) {
	if (!$linktable[$int]) { continue; }
	foreach ($linktable[$int] as $login => $timelogin) {
		
		$tmplogin=explode(".",$login);
		// quem vai tratar guest eh embaixo.. 
		if ($tmplogin[0]=="guest") { continue; }
		
		$acc = explode(" ",shell_exec("head -n 1 /proc/net/ipt_account/$login"));
		$useridle = trim($acc[17]);
		$last = time();
		
		if ($useridle > $idle) {
			// fechando a conexao dele
			$user->disconnect_user($login,$int,$useridle);
		}
	}
}
*/
// FIM





///////////////////////////////////////////////////////////////////////////////////
// GUESTS
/////////////////////////
/////////////////////////

$idle = intval($user->conf[guestconfig][idle]);

 
//$totalsfile = DIRDATA."/user/guest.totals";
//if (!file_exists($totalsfile)) {
//	// cria um arquivo vazio
//	shell_exec("echo \"\" > $totalsfile");
//}
//$totals = parse_ini_file($totalsfile);


foreach ($user->connguests as $key => $ip) {
	$key = $user->normalizeguestkey($key);
	
	$ip = str_replace("\"","",$ip);
	$acc = explode(" ",shell_exec("grep \"$ip \" /proc/net/ipt_account/guest.$key"));
	//print_r($acc);
	$guestidle = trim($acc[17]);

	$user->update_guest($key,$cronfreq);
	
	//record::msg_log("Guest key $key idle for $guestidle ($idle)","timelimit");

	if (($guestidle > $idle) ||
		($user->checkguest_valid($key,$ip,-$guestidle)==false)) 
	{
		$user->disconnect_guest($key,$ip,$guestidle);
	}
}


/* formato do totals:
 * key = inicio-fim;inicio-fim;inicio-fim; (tudo em unixtime)
 */
 

///////////////////////////////////////////////////////////////////////////////////
// ACCOUNT
/////////////////////////
/////////////////////////

 /*
$totalsfile = DIRDATA."/user/account.totals";
if (!file_exists($totalsfile)) {
	// cria um arquivo vazio
	shell_exec("echo \"\" > $totalsfile");
}
$totals = parse_ini_file($totalsfile);

$users = xml::normalizeseq($user->conf[users][user]);
$tplnoauth = $user->fwtpl[user][noauth];

foreach ($users as $user) {
	// se o usuario nao eh valido, ou seja:
	// 		o tempo dele jah passou
	//		ou a franquia jah passou
	// entao desabilito ele
	
	if ($user->checkuser_valid($user[login])==false) {
		$var[userchain]=$user[login];
		$cmdnoauth = conv::tplreplace($tplnoauth,$var); 
		exec($cmdnoauth);
	}
	
	
	
}
	
	
	*/
	
	
record::msg_log("task_timelimit finish","account");
	

 
?>
