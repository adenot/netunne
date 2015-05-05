<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 20/06/2006					*
	*																*
	****************************************************************/

// funcoes de leitura do sistema operacional

/* informacoes que precisam ser lidas e tratadas:
 * 		utilizacao do cpu
 * 		utilizacao do hd
 * 		servicos que estao rodando
 * 		visualizar os arquivos do netphoto
 * 		restartar um servico (apenas em caso de problemas, por padrao todos devem estar ativos)
 * 		ler o dmesg a procura de hardware ou de problemas
 * 	
 * Essa classe eh apenas uma abstracao, todas as informacoes devem ser obtidas
 * perguntando ao core.
 * 
 * Obtencao de dados obtidos pelo core:
 * 		os graficos (data/graph)
 * 		logs do core (?)
 * 		totais dos usuarios (data/user)
 * 		database
 */
 
/* Essas funcoes serao alteradas quando o nexus for 100% cliente-servidor
 * 
 * a classe object vai precisar ver se estah no core ou na interface antes de
 * realizar certas acoes, como o host.interface.ethX
 */
 
 
class Os {

	function dmesgboot() {
		$file = "/var/log/dmesg-boot.log";
		if (file_exists($file)) 
			return file_get_contents($file);
			
		return false;
	}
	function found_networkcards() {
		$dmesg = explode("\n",Os::dmesgboot());
		
		$cmdip = new cmd_Ip();
		$iplink = $cmdip->interfaces(1);
		//print_r($iplink);
		foreach ($iplink as $eth => $data) {
			if ($data[type]=="ether") {
				$dmesg[]=$eth.": MAC ".$data[mac];
			}
		}
		
		foreach ($dmesg as $d) {
			if (ereg("^(eth[0-9]+)(.*)\$",$d,$reg)) {
				$tmp = str_replace($reg[1].":","",$d);
				$tmp = str_replace("registered as","",$tmp);
				$tmp = trim($tmp);
				
				if (!$ret[$reg[1]]) {
					// se jah tiver, nao incluo,
					// pra pegar sempre a primeira coisa q achou
					$ret[$reg[1]]=$tmp;
				}

			}
		}
		//print_r($ret);
	
		
		
		return $ret;
	}
	
	
	
}
 
?>
