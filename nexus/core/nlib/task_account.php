<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 11/01/2006					*
	*																*
	****************************************************************/

/*
 * ATENCAO, PRECISA SER EXECUTADO DE 5 em 5 MINUTOS, POR CAUSA DO GRAFICO
 * 
 * Task: Account
 * � o script executado de tempo em tempo q realiza as seguintes tarefas na sequencia:
 * 1. Salva os dados de accounting de cada usuario em disco
 *    
 * 2. Verifica se o idle time � maior q o especificado na configuracao
 * 2.1. Se o idle time for maior, desabilita o condition do usuario
 * 3. Verifica se o usuario passou a franquia dele
 * 3.1. Se passsou, desabilita o condition e marca e nao deixa mais logar
 *     ao logar vai verificar a franquia, entao nao tem problema.
 * 4. Limpa o account no kernel (mas nao limpa o idle time)
 * 5. Atualiza o RRD com dados novos (deixa acumular um pouco pra nao pesar)
 * 
 */
 
/*
 * Esse script tambem monitora quem estah logado
 * Dados que sao monitorados:
 * - hora de login
 * - hora de logout (por timeout)
 * - tempo logado (hora atual - hora login)
 * 
 * 
 */

/* (short) ip = 192.168.31.41 bytes_src = 170 packets_src = 4 bytes_dest = 96 packets_dest = 2 time = 6

ip = 192.168.31.41 
bytes_src = 1991960 
(upload. total)
packets_src = 12055
(upload. mesmo acima soh q em pacotes)
bytes_dest = 8345053
(download. total) 
packets_dest = 16045
(download. em pacotes)
time = 8
(idle time)

echo "ip = 192.168.31.41 bytes_src = 0 packets_src = 0 bytes_dest = 0 packets_dest = 0" > eth1
zera apenas os especificados, o time continua!

sequencia:
- para cada usuario:
	pego os dados pelo ip no proc
	somo os dados aos dados em disco
		armazeno como? estilo wnet?
	chamo o update [usuario]
	limpo os dados no proc
	verifico o idle time > q certo valor
		positivo? entao bloqueia usuario
	verifico franquia > franquia
		positivo? entao bloqueia usuario


*/

//echo "iniciando\n";

$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

file_put_contents(DIRTMP."/nx_account.pid",getmypid());

record::msg_log("Running task_account...","account");

if (file_exists(DIRTMP."nx_dhcprestart")) {
	record::msg_log("DHCP need to be merged...","account");
	//shell_exec("sh $NEXUS/core/bin/scripts/exec.sh $NEXUS/core/bin/nexus.sh \"merge(dhcp)\"");
	shell_exec("/etc/nexus/bin/nexus.sh \"merge(dhcp)\"");
	shell_exec("rm -fr ".DIRTMP."nx_dhcprestart");
}

if (file_exists(DIRTMP."nx_proxyrestart")) {
	record::msg_log("PROXY need to be merged...","account");
	shell_exec("/etc/nexus/bin/nexus.sh \"merge(proxy)\"");
	shell_exec("rm -fr ".DIRTMP."nx_proxyrestart");
}
// iniciando o pppoe-server caso ele esteja parado
shell_exec('if [ "a`ps ax|grep pppoe-server|grep -v grep`" == "a" ]; then sh /etc/ppp/pserver; fi');




$user = new Forward();
$obj = new Object();

$idle = $user->conf[config][idle];

$limits = $user->limits;
$users = $user->users;unset($user->users);

//$timelimits = xml::normalizeseq($user->conf[timelimits][timelimit]);

//$totalsfile = DIRDATA."user/".date("Ym").".totals";
$totalsfile = DIRDATA."user/user.totals";

if (!file_exists($totalsfile)) {
	// cria um arquivo vazio
	shell_exec("echo \"\" > $totalsfile");
}

$onlinefile = DIRTMP."nx_user.online";
if (!file_exists($onlinefile)) {
	// cria um arquivo vazio
	shell_exec("echo \"\" > $onlinefile");
}
$totals = parse_ini_file($totalsfile);
//$online = parse_ini_file($onlinefile);

$pdata =new pdata("sqlite:".DIRDATA."/db/log.db");
$dlog = new datalog($pdata);

foreach ($user->linktable[gateway] as $int => $val) {
	if (!$user->linktable[$int]) { continue; }
		foreach ($user->linktable[$int] as $login => $timelogin)
			$online[$login]=$int;
}


// para cada usuario...
for ($i=0;$i<count($users);$i++) {
	//record::msg_log("Processing {".$users[$i][login]."}","account");
	
	if ($online[$users[login]]) { continue; } // soh vou pegar quem tah online !!!
	
	if (!$users[$i][ip]&&!$users[$i][macs])
		continue;
	
	if (intval($users[$i][disabled])>=1)
		continue;
	
	if (($users[$i][noauth]==1)||($user->conf[config][noauth]==1))
		$noauth=1;
	else
		unset($noauth);
		
	unset($userlimit);
	$limit = $user->returnplanlimit($users[$i][plan]);
	//var_dump($limit);
	if (is_array($limit)&&$limit[action]=="drop") {
		$userlimit = $limit;
	}

	
	/*
	if ($users[$i][limit]) {
		foreach ($limits as $k => $v) {
			if ($v[id]==$users[$i][acllimit]&&$v[action]=="drop")
				$userlimit = $v;
		}
	} else {
		unset($userlimit);
	}
	*/
	
	// pego os dados do ip no proc
	$ip = $users[$i][ip];
	//$int= $users[$i]["int"];
	$int = $obj->get("`INTERFACE.INTERNAL`");
	$int = $int[device];
	
	$login = trim($users[$i][login]);
	$acc = explode(" ",shell_exec("grep \"$ip \" /proc/net/ipt_account/$login"));
		
	/*
	[0] => ip
    [1] => =
    [2] => 192.168.31.41
    [3] => bytes_src
    [4] => =
    [5] => 75749845
    [6] => packets_src
    [7] => =
    [8] => 180073
    [9] => bytes_dest
    [10] => =
    [11] => 228099252
    [12] => packets_dest
    [13] => =
    [14] => 222469
    [15] => time
    [16] => =
    [17] => 15
	 */
	// primeiro vamos criar o rrd caso nao exista
	// * troquei de $ip pra $login
	if (!file_exists(DIRDATA."/rrd/$login.rrd")) {
		// * troquei de $ip pra $login
		shell_exec(DIRBIN."/scripts/rrd_create.sh $login");
		record::msg_log("RRD file for user {".$login."} created","account");
	}
	// atualizo o rrd
	// * troquei de $ip pra $login
	shell_exec(DIRBIN."/scripts/rrd_update.sh $int $ip $login");

	// * troquei de $ip pra $login
	if (!$totals[$login]) {
		$totals[$login]="0 0";
	}
	$usertotals = explode(" ",$totals[$login]);
	$usertotals[0]=$usertotals[0]+$acc[11];
	$usertotals[1]=$usertotals[1]+$acc[5];
	$totals[$login]=implode(" ",$usertotals);
	
	
	$tplnoauth = $user->fwtpl[user][authremove][command];
	$var[userchain]=$login;
	$cmdnoauth = conv::tplreplace($tplnoauth,$var); 

	$time = trim(str_replace("\n","",$acc[17]));
	/* NAO VAI MAIS SABER SE TAH ONLINE POR AQUI
	 * AGORA VAI SER PELO TASK_TIMELIMIT
	 * 
	if (($time > $idle)&&($online[$login])) {
		// se ele estava online, vamos desconecta-lo

		// melhor gravar isso no banco, senao depois vai ficar mto lento
		unset($online[$login]);
		
		$dlog->insert("disconnects",$login);
		record::msg_log("User {$login} disconnects","account");
		
		if (!$noauth) {
			// desabilita o usuario (somente se nao tiver noauth)
			$condret = shell_exec($cmdnoauth);
			record::msg_log("User $login disabled due inactivity ($time > $idle)","account");
		}
	} 
	// nao eh exatamente um else... mas quase isso
	if (($time <= $idle)&&(!$online[$login])) {
		$online[$login]=time();
		$dlog->insert("connects",$login);
		
		record::msg_log("User {$login} connects","account");
	}
	*/
	
	
	if ($userlimit) {
		// se tiver limit de drop no usuario, entao bloqueio ele
		// ele vai cair na pagina de autenticacao e vai receber um aviso
		// dizendo q excedeu a cota
		
		// mesmo q tenha noauth, eu tenho q bloquear o usuario, nesse caso, ele
		// vai cair em uma pagina de autenticacao
		
		$userlimit = explode(" ",$userlimit[traffic]);
		if ((($usertotals[0]>$userlimit[0])&&($userlimit[0]!=0)) ||
			(($usertotals[1]>$userlimit[1])&&($userlimit[1]!=0))) {
			// passou o trafego de upload ou download!

			$usercond = trim(exec("cat /proc/net/ipt_condition/$login"));
			if ($usercond==1) {
				$condret = shell_exec($cmdnoauth);
				record::msg_log("Disabling user with command {$cmdnoauth}","account");
				record::msg_log("User {".$login."} disabled due exceed of quota (max: {".$userlimit[0]."} {".$userlimit[1]."})","account");
			}
		}
	}

	echo shell_exec("echo \"ip = $ip bytes_src = 0 packets_src = 0 bytes_dest = 0 packets_dest = 0\" > /proc/net/ipt_account/$login");

}


// agora os traffics

// para cada regra de traffic...
$traffics = $user->traffics;
for ($i=0;$i<count($traffics);$i++) {
	$accname = "traffic".$traffics[$i][id];
	// tou usando o head pra pegar soh a primeira linha
	// pq o account quando recebe uma rede no aaddr ele loga cada IP individualmente 
	// e o primeiro eh o total
	$acc = explode(" ",shell_exec("head -n 1 /proc/net/ipt_account/$accname"));
	$ip = $acc[2];
	
	// primeiro vamos criar o rrd caso nao exista
	if (!file_exists(DIRDATA."/rrd/$accname.rrd")) {
		echo shell_exec(DIRBIN."/scripts/rrd_create.sh $accname");
		record::msg_log("RRD file for traffic $accname created","account");
	}
	
	echo shell_exec(DIRBIN."/scripts/rrd_update.sh $accname $ip $accname");

	if (!$totals[$accname]) {
		$totals[$accname]="0 0";
	}
	$trtotals = explode(" ",$totals[$accname]);
	$trtotals[0]=$trtotals[0]+$acc[11];
	$trtotals[1]=$trtotals[1]+$acc[5];
	$totals[$accname]=implode(" ",$trtotals);

}

$external = $obj->get("`NETWORK.INTERFACE.EXTERNAL`");
//print_r($external);

foreach ($external as $k=>$v) {
//for ($i=0;$i<count($external);$i++) {
	$accname = $k;
	// tou usando o head pra pegar soh a primeira linha
	// pq o account quando recebe uma rede no aaddr ele loga cada IP individualmente 
	// e o primeiro eh o total
	$acc = explode("\n",shell_exec("ip -s link show $accname"));
	/*
	3: eth1: <BROADCAST,MULTICAST,UP> mtu 1500 qdisc cbq qlen 1000
    link/ether 00:0c:29:f0:83:7a brd ff:ff:ff:ff:ff:ff
    RX: bytes  packets  errors  dropped overrun mcast   
    367863420  321054   0       0       0       0      
    TX: bytes  packets  errors  dropped carrier collsns 
    32647656   227966   0       0       0       0     
	*/
	$rxbytes = trim(sscanf($acc[3],"%s"));
	$txbytes = trim(sscanf($acc[5],"%s"));
	
	//$acc = explode(" ",shell_exec("head -n 1 /proc/net/ipt_account/$accname"));
	//$ip = $acc[2];
	

	// primeiro vamos criar o rrd caso nao exista
	if (!file_exists(DIRDATA."/rrd/$accname.rrd")) {
		echo shell_exec(DIRBIN."/scripts/rrd_create.sh $accname");
		record::msg_log("RRD file for network interface $accname created","account");
	}
	
	//echo shell_exec(DIRBIN."/scripts/rrd_update.sh $accname $ip $accname");

	if (!$totals[$accname]) {
		$totals[$accname]="0 0";
	}
	
	// aqui tenho q inverter o upload do download
	
	$trtotals = explode(" ",$totals[$accname]);
	$trtotals[1]=$trtotals[1]+$acc[11];
	$trtotals[0]=$trtotals[0]+$acc[5];
	$totals[$accname]=implode(" ",$trtotals);
	
	//shell_exec("echo \"ip = $ip bytes_src = 0 packets_src = 0 bytes_dest = 0 packets_dest = 0\" > /proc/net/ipt_account/$accname");
	
}

write_ini_file($totalsfile,$totals);
//write_ini_file($onlinefile,$online);


record::msg_log("task_account finish","account");

	
?>
