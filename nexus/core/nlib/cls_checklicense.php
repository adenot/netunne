<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 23/03/2006					*
	*																*
	****************************************************************/



class Checklicense {
	var $lic;
	var $ossl;
	var $blow;
 	var $glue = "ALQ7P2V6ALD1LLP001AAP"; // se mudar aqui, tem q mudar lah (index.php) e no cls_license
 	var $cpuid;
 	var $userid;
 	var $out;
 	var $now;
 	var $msg;
 	var $last_open_license;
 	
 	var $datenow;
 	
 	var $obj_lic;
 	
	function __construct () {
		clearstatcache();
		$this->lic = @file_get_contents(DIRDATA."/user.crt");
		$this->privatekey = @file_get_contents(DIRDATA."/user.pvk");
		
		$this->obj_lic = new License();
		
		$this->cpuid = $this->obj_lic->gen_cpuid(); //poderia ser uma funcao do common?
		$conf = xml::loadxml("info.xml");
		if ($conf[info][user]) 
			$this->userid=$conf[info][user];
		
		// verifico a hora 3 vezes:
		// 1) funcao time
		// 2) executo date +%s
		// 3) pego o btime entro de /proc/stat e somo com /proc/uptime
		// pode haver uma variacao entao deixei 5 segundos de threshold
		
		$now1 = time();
		$now2 = trim(shell_exec("date +%s"));
		$now3_1=explode(" ",shell_exec("cat /proc/stat|grep btime"));
		$now3_1=trim($now3_1[1]);
		$now3_2=explode(" ",shell_exec("cat /proc/uptime"));
		$now3_2=trim($now3_2[0]);
		$now3_2=round($now3_2);
		$now3=$now3_1+$now3_2;
		
		//echo "$now1 $now2 $now3";
		
		$int = 5;
		
		if (abs($now1-$now2)>$int)
			$this->now=0;
		else if (abs($now1-$now3)>$int)
			$this->now=0;
		else 
			$this->now=$now1;
			
		//echo " ".$this->now."\n";
		
	}
	
	function open_license ($filecert=0) {
		if ($this->out) { return $this->last_open_license; }
		
		/* Roteiro:
		 * 1) explode pelo glue
		 * 2) vou ter: [0]=pkg_x e [1]=pkg2_x
		 * 3) decrypta pelo cpuid e uudecode (pkg2_x) = pkg2
		 * 4) pkg2 = ekey
		 * 5) decrypta pelo privkey+ekey e base64_decode(pkg_x) = pkg
		 * 6) explode pkg pelo glue
		 * 7) vou ter: [0]=out, [1]=userident, [2]=expire, [3]=datestart e [4]=now 
		 */
		
		$this->last_open_license=false;

		if (strlen(trim($this->lic))==0) {
			return false;
		}
		if (strlen(trim($this->privatekey))==0) {
			return false;
		}
		
		//echo "LIC(".$this->lic.")\n";
		
		if (substr($this->lic,0,3)=="_V2") {
			$this->lic = str_replace("_V2","",trim($this->lic));
			$pkg3 = explode($this->glue,$this->lic);
			$pkg_x = $pkg3[0];
			$pkg2_x = $pkg3[1];
			
			$pkg2 = $this->obj_lic->Decrypt($pkg2_x,$this->cpuid);
			$pkg2 = convert_uudecode($pkg2);
			
			$version=2;
		} else {
			$pkg3 = explode($this->glue,$this->lic);
			$pkg_x = $pkg3[0];
			$pkg2_x = convert_uudecode($pkg3[1]);
			
			$this->blow = new Crypt_Blowfish($this->cpuid);	
			$pkg2 = $this->blow->decrypt($pkg2_x);
		}
		
		//echo "MD5:".md5($pkg2)."\n";
		
		
		$this->ossl = new OpenSSL();
		
		//echo "PKG2:\n".$pkg2."\n";

		// FALTA: tenho q verificar se o pkg2 tah incorreto � pq
		// o cpuid eh diferente
		// :: fiz isso abaixo, verificando o count do pkg
		// :: mas precisa testar, pq provavelmente vai dar erro no decrypt abaixo
		
		$ekey = $pkg2;
//		echo "EKEY:".$ekey;

		@$this->ossl->set_privatekey($this->privatekey, false, md5($this->userid));
		@$this->ossl->decrypt(base64_decode($pkg_x), $ekey);
		$pkg = @$this->ossl->get_plain();
		
		$pkg = explode($this->glue,$pkg);

		//print_r($pkg);
		
		
		if (count($pkg)!=6) { return false; }
		
		$this->out= unserialize($pkg[0]);
		$userident=	$pkg[1];
		$dateexpire=$pkg[2];
		$datestart=	$pkg[3];
		$datenow=	$pkg[4];
		$cert=		$pkg[5];
		
		if ($filecert==1) {
			$cf = new Conf("info");
			$cf->conf[info][cert]=$cert;
			$cf->write();
			unset($cf);
		}
		

		/*
		 * VERIFICO:
		 * 1) CPUID Diferente (jah verificou pois a chave do blowfish eh o cpuid)
		 * 2) DATA errada
		 * 3) Licensa Expirada
		 * 4) Usuario diferente
		 * 5) Now ou datestart maior que agora
		 * Caso algum desse itens se confirme, retorno FALSE
		 * senao TRUE 
		 */
		
		/*
		 * Essa funcao pode ser chamada varias vezes para obter as informacoes da licenca,
		 * entao 
		 */
		
		
		//echo "USERIDENT: $userident\nDATEEXPIRE: $dateexpire\nDATESTART: $datestart\nDATENOW: $datenow\n";
		
		if ($userident != $this->userid) {
			//echo "error 1";
			record::msg_log("Registry Expired, reason 1","license");
			return false;
		}
		
		if ($dateexpire < $this->now) {
			//echo "error 2";
			record::msg_log("Registry Expired, reason 2","license");
			return false;
		}
		
		// diferenca entre o horario do servidor e do cliente
		// COMENTARIO: TENHO QUASE CERTEZA Q ISSO NAO FUNCIONA
		// POR ISSO DESABILITEI
		
		//$nowdiff = $datenow - $this->now;
		//if (($datestart+$nowdiff) > $this->now) {
			//echo "error 3";
		//	return false;
		//}
		// se a diferenca entre a hora do servidor e do cliente for maior q 
		// 1 dia entao tem algo errado
		//if (($datenow - $this->now) > 86400) {
		//	return false;
		//}
		// VOU VERIFICAR NA HORA Q PECO A LICENSA PRO SERVIDOR


		//print_r($this->out);

		//echo $this->out;
		
		$this->last_open_license=TRUE;
		return TRUE;
	}
	
	/*****
	 * checkout (var)
	 * retorna:
	 * 		true: caso a variavel nao exista
	 * 		false: caso a licensa esteja expirada ou o valor da variavel seja false mesmo
	 * 		num/string: o valor da variavel
	 */
	function checkout($var) {
		//echo "CHECKOUT $var";
		$res = $this->open_license();
		if ($res==false) {
			$this->msg=_("Invalid Registry");
			return false;
		}
		$out = $this->out;
		if (!$out[$var]) {
			return true;
		}
		return $out[$var]; 

		
	}
	
}

?>
