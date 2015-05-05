<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 15/05/2007					*
	*																*
	****************************************************************/

// executado apos o merge do forward para otimizar a aplicacao das regras de firewall
// soh vai aplicar o q for necessario
$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

record::msg_log("Running task_forward...","account");

clearstatcache();

function files_dir ($dir) {

        $handle = opendir($dir);

        while (false !== ($file = readdir($handle))) {
                if ($file=="."||$file=="..") { continue; }
                $files[]=$file;

                if (is_dir($dir.$file)) {
                        $files = array_merge(files_dir($dir.$file."/"),$files);
                }
        }
        return $files;
}

$dir0 = "/tmp/newforward/";
$dir1 = "/tmp/oldforward/";

$nxfiles0 = files_dir($dir0);
$nxfiles1 = files_dir($dir1);

$nxfix=array();
$nxforward=array();

clearstatcache();

// verifico se o computador tah ligando
if (!file_exists(DIRTMP."nx_firewall.sh")) {
	echo "\nNOVO FIREWALL\n";
	echo shell_exec("sh /etc/nexus/firewall.sh");
	echo shell_exec("sh /etc/nexus/shaper.sh");
}


// registro as modificacoes
foreach ($nxfiles0 as $file0) {
	$diff=0;

	if (file_exists($dir1.$file0)) {
		$md50 = md5_file($dir0.$file0);
		$md51 = md5_file($dir1.$file0);
		if ($md51!=$md50) {
			// marco pra ativacao, mudou usuario
			$diff=1;
		}
		
	} else {
		// marco pra ativacao, novo usuario
		$diff=1;
	}
	
	
	if ($diff==1) {
		$tmp = explode(".",$file0);
		unset($tmp[0]);
		$user = implode(".",$tmp);
		if (conv::startwith("nx_forward.",$file0)) {
			$nxforward[$user]=$user;
		} else if (conv::startwith("nx_fix.",$file0)) {
			$nxfix[$user]=$user;
		}
		
		echo "MUDOU ($file0): ".shell_exec("diff ".$dir0.$file0." ".$dir1.$file0);
	}
	
}

// aplico, na ordem: 
// forward, unfix, fix 
foreach ($nxforward as $user) {
	echo shell_exec("sh ".$dir0."nx_forward.$user");
	echo shell_exec("sh ".DIRTMP."nx_unfix.$user");
	echo shell_exec("sh ".$dir0."nx_fix.$user");
	
	// nao preciso mais fazer o mesmo no fix
	if ($nxfix[$user]) { unset($nxfix[$user]); }
}

// unfix, fix
foreach ($nxfix as $user) {
	echo shell_exec("sh ".DIRTMP."nx_unfix.$user");
	echo shell_exec("sh ".$dir0."nx_fix.$user");
}

echo "\nNXFORWARD:";print_r($nxforward);
echo "\nNXFIX:";print_r($nxfix);


?>
