<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 20/06/2006					*
	*																*
	****************************************************************/

/*
$CONF = parse_ini_file('/etc/nexus/path');
include_once $CONF['NEXUS']."/core/nlib/common.nx";
set_include_path(get_include_path() . PATH_SEPARATOR . DIRNLIB . PATH_SEPARATOR . DIRBIN);
*/
//include_once "cls.nx";
include_once "in.nx";

Conf::touchconf(); // para corrigir algum problema de data futura nos confs

set_time_limit(0);
ignore_user_abort(true);

/* SEMAFORO
$my_pid = getmypid();
$sem_file = "/tmp/nx_sem";
while (true) {
	usleep(rand(200000,1000000)); // 200-1000 ms
	clearstatcache();
	if (file_exists($sem_file)) {
		$sem_pid = intval(trim(file_get_contents($sem_file)));
		if ($sem_pid!=0) {
			if ($sem_pid==$my_pid) { echo "c1";break; }
			$pid_info = getpidinfo($sem_pid);
			if (is_bool($pid_info) && $pid_info==false) { 
				// pid nao existe mais
				echo "c2";break;
			} else {
				// pid ainda tah rodando..
			}
		} else {
			// sem_pid==0, arquivo corrompido
			echo "c3";break;
		}
	} else { 
		// nao existe o arquivo de semaforo
		echo "c4";break;
	}

}
//file_put_contents($sem_file,$mypid);
shell_exec("echo $my_pid > $sem_file");
clearstatcache();

// END SEMAFORO

*/

//echo "iniciando $buf\n";

do {
	if (!$one) {
		$buf = trim(fgets(STDIN));
	}
	if (trim($buf)=="") { break; }
	
	record::in_log($buf);
	$resp = in($buf);
	record::out_log($resp);

	if ($resp&&trim($buf)!="") {
		// se tiver o wall, ele escreve e sai 
		if ($GLOBALS[wall]) {
			record::wall($GLOBALS[wall],$resp."\n");
		} else {
			echo $resp."\n";
		}
	} else {
		echo "ERROR(\"NULL Result\",\"NULLERROR\")\n";
	}

	if ($GLOBALS[wall]&&!$GLOBALS[wall1]) {
		record::wall($GLOBALS[wall],"/EOF/");
		break;
	}
	if ($GLOBALS[wall1])
		unset($GLOBALS[wall1]);

	if ($one==1)
		break;


} while  (1==1);

//@unlink($sem_file);
//echo "fim $buf\n";


?>
