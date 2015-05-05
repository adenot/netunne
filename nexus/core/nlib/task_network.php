<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 26/01/2006					*
	*																*
	****************************************************************/

// CASO ESPECIAL:
// - a placa primaria tah ativa mas o servidor de dhcp tah fora do ar.
// -- nesse caso vai ficar sem gateway, jah que esse scirpt vai apagar os outros gateways.
// -- nao pode apagar os outros gateways se o primario nao tiver subido, marco ele como off e uso outro primario.

/*
 * OQ EU FACO?
 * - removo o gateway padrao do link q nao eh primario
 * - gravo o resolv.conf com o DNS do link primario 
 * - crio a rota pra usuarios q estao online em um link q caiu
 * 
 */

$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

record::msg_log("Running task_network...","account");

$ip = new cmd_Ip();

/* novo roteiro
 * esse script eh executado apos mergear o network e
 * apois detectada uma queda do link, por isso,
 * ele precisa apagar as rotas q nao sao do link primario e recolocar todo mundo nos gateways
 * q estao ativos.
 * quem tava em um gateway ativo, continua nele
 * quem tava em um gateway q caiu, vai ser rebalanceado
 * 
 */

$ip_gateways = $ip->gateways();
$ip_interfaces = $ip->interfaces();

$obj = new Object();
$int_internals = $obj->getinterfaces("INTERNAL");


$tplroute = xml::loadxml(DIRTPL."/routes.xml.tpl");

$var = array();
$table = 100;

$obj_ipv4 = new Net_ipv4();
$fw = new Forward();

$cmd="";

$int_externals = $obj->getinterfaces("EXTERNAL");

echo "LINKTABLE:";print_r($fw->linktable);
//echo "INTE:";print_r($int_externals);

/* PRIMEIRO
 * primeiro vamos apagar os gateways exceto o primario e ver os links q estao fora pra rebalancear 
 * os usuarios
 * 
 * a ideia eh:
 * - o primario fica com a rota padrao
 * - se o primario est� fora do ar, a rota padrao vai pro proximo ativo com maior weight
 * - se nao tem nenhum, a rota padrao continua com o primario ateh q ele seja reparado
 */


// zerando...
$fw->linktable[weight]=array();
$fw->linktable[dns]=array();
$fw->linktable[gateway]=array();

// salvando os weights
foreach ($int_externals as $inte) {
	$fw->linktable[weight][$inte[device]]=$inte[weight];
	$fw->linktable[dns][$inte[device]]=$inte[dns];
}

$int_primary = Network::getprimary();

// se nao tiver rota padrao para o primario, considero o primario como Off e pego um novo primario.
foreach ($ip_gateways as $int=>$gw) {
	if ($int==$int_primary) {
		$primary_gateway=1;
	}
}
if (!$primary_gateway) {
	clearstatcache();
	$nxoff_old = explode(",",file_get_contents(DIRTMP."nx_off.tmp"));
	array_push ($nxoff_old,$int_primary);
	file_put_contents(DIRTMP."nx_off.tmp",implode(",",$nxoff_old));
	clearstatcache();
}
// agora novamente...
$int_primary = Network::getprimary();
file_put_contents(DIRTMP."nx_primary",$int_primary);

foreach ($ip_gateways as $int=>$gw) {

	if ($int!=$int_primary) {
		// coloco pra remocao
		$var[device]=$int;
		$cmd .= conv::tplreplace($tplroute[gateway][remove][command],$var)."\n";
		
	}
	$fw->linktable[gateway][$int]=$gw[0];
}

$fw->linktable["global"][primary]= $int_primary;

$dns_primary = $fw->linktable[dns][$int_primary];

if (!$dns_primary) {
	$conf=new Conf("network");
	$dns_primary = $conf->get("network/dns/nameserver");
}

// DNS
$resolvconf = file_get_contents("/etc/resolv.conf.nx");
$resolvconf = str_replace("{nameserver}",$dns_primary,$resolvconf);

// vou precisar recriar todas as regras dos usuarios, dependendo do link q eles estavam

		// preciso apagar toda tabela de routeuser
		// depois recriar quem tem link OK
		// depois criar quem � orfao de link

// primeiro: apagar todas as rotas

// tem q ser antes pq depois o nx_unroute muda
//exec("/bin/sh ".NEXUS."/core/bin/scripts/exec.sh /bin/sh ".DIRTMP."/nx_unroute.*");


$old_weight = $fw->linktable[weight];

// segundo: recriar as rotas
foreach ($fw->linktable[gateway] as $int => $gw) {
	if (in_array(trim($int),$fw->nxoff)) {
		
		// como se ele nao existisse mais...
		//unset($fw->linktable[gateway][$int]);
		unset($fw->linktable[weight][$int]);
		
		if (!$fw->linktable[$int]) { continue; }
		
		foreach ($fw->linktable[$int] as $user => $ip) {
			// nesse caso eram usuarios q estavam em um link q parou..
			// entao eles vao ser rebalanceados
			$oldtime = $fw->linktable[$int][$user];
			unset($fw->linktable[$int][$user]);
			if (!$fw->createuser_route ($user,"1",1)) {
				// se retornar 0 eh pq nao posso muda-lo de link, restauro o linktable dele
				// se retornar 1, ele trocou o link, entao mantenho apagado o antigo
				$fw->linktable[$int][$user]=$oldtime;
			}
		}
		// depois a tabela deles vai morrer
		// - nao pode morrer pq pode ter usuarios onde o link foi forcado a permanecer, mesmo qdo off.
		//unset($fw->linktable[$int]);
	} 
	/* nao precisa pq quando chama o nx_route.* ele refaz todos
	 * else {
		// usuarios q nao tavam em um link q parou
		foreach ($fw->linktable[$int] as $user => $ip) {
			echo "createuser_route ($user,$gw)\n";
			$cmd .= $fw->createuser_route($user,$gw);
		}
	} */
}

// caso especial: somem todos os weights
if (count($fw->linktable[weight])==0) {
	$fw->linktable[weight][$int_primary] = $old_weight[$int_primary];
	// deixo apenas o weight da primaria
}

$fw->filelinktable();

if (trim($cmd)!="") {
	$cmd = $tplroute[pre][command]."\n".$cmd."\n".$tplroute[post][command];
}

file_put_contents(DIRTMP."/routes.sh",$cmd);
file_put_contents("/etc/resolv.conf",$resolvconf);

//echo $cmd;

exec("/bin/sh ".NEXUS."/core/bin/scripts/exec.sh /bin/sh ".DIRTMP."/routes.sh");
exec("/bin/sh ".NEXUS."/core/bin/scripts/exec.sh /bin/sh ".DIRTMP."/nx_external");

//exec("/bin/sh ".NEXUS."/core/bin/scripts/exec.sh /bin/sh ".DIRTMP."/nx_route.*");
//exec ("for i in /tmp/nx_route.*;do sh \$i;done");


record::msg_log("task_network finish","account");

?>
