<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 21/02/2006					*
	*																*
	****************************************************************/

//include_once "common.nx";


class License {
/*
 * - Criptografado usando a chave publica:
 * - - IN (um array serializado de variaveis), vai pro banco
 * - - cpuid, vai pro banco, mas antes verifica se mudou e avisa
 * - - mac, mesmo do cpuid
 * - - certificado, mesmo do cpuid
 * - Criptografada com a Key do cliente
 * - - Private Key
 * - Sem criptografia
 * - - UserID 
 */
 	var $userid; 		// info.xml
 	var $cpuid;			// calculado na hora
 	var $mac;			// calculado na hora
 	var $cert;			// info.xml (quando recebe a licensa, atualiza no info.xml)
 	var $in;			// info.xml
 	var $userkey;		// info.xml
 	var $lang;			// info.xml
 	var $pass;
 	var $ossl;
 	var $publickey;
 	var $privatekey;
 	var $blow;
 	var $obj;
 	var $conf;
 	var $glue = "ALQ7P2V6ALD1LLP001AAP"; // se mudar aqui, tem q mudar lah (index.php) e no checklicense tb


	function __construct () {
		$this->ossl = new OpenSSL();
		$this->obj  = new Object();
		
	}

	function gen_keys() {

		$this->ossl->set_privkeypass($this->pass);
		$this->ossl->do_csr();
		$this->privatekey=$this->ossl->get_privatekey();
		$this->publickey=$this->ossl->get_publickey();
		file_put_contents("/tmp/req3",$this->publickey);
	}
	
	function load_data() {
		$this->conf = xml::loadxml("info.xml");
		if ($this->conf[info][user]) 
			$this->userid=$this->conf[info][user];
		if ($this->conf[info][userkey]) 
			$this->userkey=$this->conf[info][userkey];
		if ($this->conf[info][cert]) 
			$this->cert=$this->conf[info][cert];
		if ($this->conf[info][in]) 
			$this->in=$this->conf[info][in];
		if ($this->conf[info][lang]) 
			$this->lang=$this->conf[info][lang];
		$this->cpuid=$this->gen_cpuid();
		$this->mac  =$this->gen_mac();
		$this->pass =md5($this->userid);
	}
	
	function gen_cpuid() {

		// talvez colocando lspci -m nao deve sofrer alteracoes
		// antes de implementar isso, podemos fazer uma "pesquisa" usando o _in_ dos netunnes
		// pra saber se altera
		$proccat = shell_exec("lspci -m");
		$mac = $this->gen_mac();
		
		// DEIXEI SOH O MAC !! VEREMOS..
		$cpuid = substr(md5($mac),0,40);
		return $cpuid;
	}
	
	function gen_mac() {
		// qual interface devo pegar o mac?
		// eth0..? todas?
		// por enquanto vou pegar soh da eth0
		$mac = $this->obj->get("`MAC.eth0`");
		return $mac;
	}
	
	function request_license () {
		
		// antes preciso setar o mac, cpuid, pass, cert e in
		$this->load_data();
		
		if ($this->userid==""||$this->userkey=="")
			return message::generate_function("FAIL",_("ID and Key cannot be null"));
		
		// gerando private e public keys
		$this->gen_keys();
		
		$this->blow = new Crypt_Blowfish($this->userkey);
		
		$request = $this->gen_request();
		//echo "REQUEST:".$request."\nENDREQUEST\n";
		
		$result = $this->send_request($request);
		echo $result;
		return $this->parse_result($result);
	}
	
	/*
	 * O parse_result vai receber o resultado, q pode conter:
	 * - msgs de erro
	 * - informacoes q o servidor queira passar para o cliente
	 * - connid
	 * - licensa 
	 */
	function parse_result ($result) {
		file_put_contents("/tmp/lic1",$result);
		
		$result = explode("\n",$result);
		for ($i=0;$i<count($result);$i++) {
			if (trim($result[$i]=="")) { continue; }
			$res = message::input_function($result[$i]);
			if  ($res[func]=="LIC") {
				$lic = $res[license];
				$result[$i]=message::generate_function("OK",_("Registry Received Succefully"))."\n\n";
				file_put_contents(DIRDATA."/user.crt",rawurldecode($lic));
				file_put_contents(DIRDATA."/user.pvk",$this->privatekey);
				clearstatcache();
				// apenas para gravar o CERT no xml
				$lic = new Checklicense();
				$lic->open_license(1);
				unset($lic);
			}
		}
		$result = implode("\n",$result);
		return conv::cleanout($result);
	}
	
	function send_request ($request) {
		
		file_put_contents("/tmp/req1",$request);
		
		$url[] = "http://console1.locaweb.com.br/center/license/index2.php";
		$url[] = "https://www.netunne.com.br/center/license/index2.php";
		//$url[] = "https://www.netunne.com/center/license/index.php";
		//$url[] = "https://www.netunne.net/center/license/index.php";
		$url[] = "https://www.console.com.br/center/license/index.php";
		//$url[] = "https://www2.console.com.br/center/license/index.php";
		$url[] = "https://www.neolinux.com.br/center/license/index.php";
		//$url[] = "https://www2.neolinux.com.br/center/license/index.php";
		$url[] = "https://puc.console.com.br/center/license/index.php";
		//$url[] = "https://puc2.console.com.br/center/license/index.php";
		
		$urlrequest = urlencode($request);
		//echo "URLREQUEST:".$urlrequest."\nENDURLREQUEST\n";
		$time = time();
		$params = "time=$time&request=$urlrequest&prodid=".PRODID;
		$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
		file_put_contents("/tmp/req2",$request);
		//echo "PARAMS:$params\n";
		for ($i=0;$i<count($url);$i++) {
			unset($curl_error);
			unset($curl_errno);
			
			record::msg_log("Trying to connect in ".$url[$i],"license");
			
			$ch = curl_init();
			
			/*
			$this_header = array(
			   "MIME-Version: 1.0",
			   "Content-type: text/html; charset=utf8",
			   "Content-transfer-encoding: text"
			);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			 * 
			 */
			
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
			curl_setopt($ch, CURLOPT_URL,$url[$i]);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_TIMEOUT, 300);
			curl_setopt($ch, CURLE_OPERATION_TIMEOUTED, 300);
			
			$result=curl_exec ($ch);

			$curl_error = curl_error($ch);
			$curl_errno = curl_errno($ch);
			curl_close($ch); 
			//echo "SAIDA:$result\n";
			//echo "ERROR:$curl_errno\n";
			//print_r(curl_getinfo($ch));
			
			if (substr_count($result,"INFO")==0&&substr_count($result,"Welcome")==0) {
				$curl_errno=404;
				$curl_error="Invalid URL Request";
				$result="";
			}
			
			
			if ($curl_errno==0) { 
				$res .= message::generate_function("INFO","Connection Established")."\n\n"; 
				record::msg_log("Connected to ".$url[$i]." !","license");
				break;
			}
			
			record::msg_log("Cannot connect: $curl_error, trying next server (#$i)...","license");
			$res .= message::generate_function("ALERT","$curl_error, trying next server (#$i)... ","NEXTSERVER")."\n\n";
			
		}
		if (trim($result)=="") {
			$res .= message::generate_function("ERROR",_("All tries failed! Please check your connection!"),"CONNERROR")."\n\n";
			record::msg_log("All servers failed!","license");
			return conv::cleanout($res);
		} else {
			return conv::cleanout($result);
		}
	}
	
	function gen_request () {
        
		$pkg[0]=$this->in; // jah tah serializado no info.xml
		$pkg[1]=$this->cpuid;
		$pkg[2]=$this->mac;
		$pkg[3]=$this->cert;
		$pkg[4]=time();
		//$pkg[5]="meiaquatro".convert_uuencode($this->publickey);
		$pkg[5]=$this->publickey;
		$pkg[6]=$this->userkey; // isso eh novo (soh quando NOCRYPT)
		$pkg = implode($this->glue,$pkg);
		
		//$pkg_x = $this->blow->encrypt($pkg);
		//$pkg_x = "NOBLOW".$this->Encrypt($pkg,$this->userkey);
		$pkg_x = "NOCRYPT".base64_encode($pkg);
		
		//echo "PKGX:".$pkg_x."\n";

		$pkg2[0]=$pkg_x;
		$pkg2[1]=$this->userid;
		$pkg2[2]=$this->lang;
				
		$pkg2 = implode($this->glue,$pkg2);

		file_put_contents("/tmp/lic1",$pkg2);

		return $pkg2;
		
	}	
	
	function Encrypt($string, $key)
	{
		$result = '';
		for($i=1; $i<=strlen($string); $i++)
		{
			$char = substr($string, $i-1, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		return $result;
	}
	
	function Decrypt($string, $key)
	{
		$result = '';
		for($i=1; $i<=strlen($string); $i++)
		{
			$char = substr($string, $i-1, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		return $result;
	}
}

//$a = new License();
//$a->request_license();
//print_r(input_map(input_function("ERROR ('oi',KEYERROR)")));

//$a = new CheckLicense();
//$a->open_license();

/*

CLASS OpenSSL

A wrapper class for a simple subset of the PHP OpenSSL functions. Use for public key encryption jobs.

=== Includes source code from many contributors to the PHP.net manual ===

....usage examples below....

Alex Poole 2005

php ~at~ wwwcrm.com

*/

class OpenSSL{

    var $privatekey;    //resource or string private key
    var $publickey;        //ditto public
    var $plaintext;
    var $crypttext;
    var $ekey;            //ekey - set by encryption, required by decryption
    var $privkeypass;    //password for private key
    var $csr;            //certificate signing request string generated with keys
    var $config;
    
    function OpenSSL(){
        $this->config = array("config" => OPEN_SSL_CONF_PATH);
    }
    
    function readf($path){
        //return file contents
        $fp=fopen($path,"r");
        $ret=fread($fp,8192);
        fclose($fp);
        return $ret;
    }
    
    //privatekey can be text or file path
    function set_privatekey($privatekey, $isFile=0, $key_password=""){
        
        if ($key_password) $this->privkeypass=$key_password;
        
        if ($isFile)$privatekey=$this->readf($privatekey);
        
        $this->privatekey=openssl_get_privatekey($privatekey, $this->privkeypass);
    }
    
    //publickey can be text or file path
    function set_publickey($publickey, $isFile=0){
        
        if ($isFile)$publickey=$this->readf($publickey);
        
        $this->publickey=openssl_get_publickey($publickey);
    }
    
    function set_ekey($ekey){
        $this->ekey=$ekey;
    }
    
    function set_privkeypass($pass){
        $this->privkeypass=$pass;
    }
    
    function set_plain($txt){
        $this->plaintext=$txt;
    }
    
    function set_crypttext($txt){
        $this->crypttext=$txt;
    }
    
    function encrypt($plain=""){
    
        if ($plain) $this->plaintext=$plain;
        
        openssl_seal($this->plaintext, $this->crypttext, $ekey, array($this->publickey));
        
        $this->ekey=$ekey[0];
    }
    
    function decrypt($crypt="", $ekey=""){
    
        if ($crypt)$this->crypttext=$crypt;
        if ($ekey)$this->ekey=$ekey;
        
        openssl_open($this->crypttext, $this->plaintext, $this->ekey, $this->privatekey);
    }
    
    function do_csr(
                    $countryName = "UK",
                    $stateOrProvinceName = "London",
                    $localityName = "Blah",
                    $organizationName = "Blah1",
                    $organizationalUnitName = "Blah2",
                    $commonName = "Joe Bloggs",
                    $emailAddress = "openssl@domain.com"
                    ){
                    
        $dn=Array(
                    "countryName" => $countryName,
                    "stateOrProvinceName" => $stateOrProvinceName,
                    "localityName" => $localityName,
                    "organizationName" => $organizationName,
                    "organizationalUnitName" => $organizationalUnitName,
                    "commonName" => $commonName,
                    "emailAddress" => $emailAddress
                    );
        $privkey = openssl_pkey_new($this->config);
        $csr = openssl_csr_new($dn, $privkey, $this->config);
        $sscert = openssl_csr_sign($csr, null, $privkey, OPEN_SSL_CERT_DAYS_VALID, $this->config);
        openssl_x509_export($sscert, $this->publickey);
        openssl_pkey_export($privkey, $this->privatekey, $this->privkeypass, $this->config);
        openssl_csr_export($csr, $this->csr);
    }
    
    function get_plain(){
        return $this->plaintext;
    }
    
    function get_crypt(){
        return $this->crypttext;
    }
    
    function get_ekey(){
        return $this->ekey;
    }
    
    function get_privatekey(){
        return $this->privatekey;
    }
    
    function get_privkeypass(){
        return $this->privkeypass;
    }
    
    function get_publickey(){
        return $this->publickey;
    }
}


//USAGE
/*
$pass="zPUp9mCzIrM7xQOEnPJZiDkBwPBV9UlITY0Xd3v4bfIwzJ12yPQCAkcR5BsePGVw
RK6GS5RwXSLrJu9Qj8+fk0wPj6IPY5HvA9Dgwh+dptPlXppeBm3JZJ+92l0DqR2M
ccL43V3Z4JN9OXRAfGWXyrBJNmwURkq7a2EyFElBBWK03OLYVMevQyRJcMKY0ai+
tmnFUSkH2zwnkXQfPUxg9aV7TmGQv/3TkK1SziyDyNm7GwtyIlfcigCCRz3uc77U
Izcez5wgmkpNElg/D7/VCd9E+grTfPYNmuTVccGOes+n8ISJJdW0vYX1xwWv5l
bK22CwD/l7SMBOz4M9XH0Jb0OhNxLza4XMDu0ANMIpnkn1KOcmQ4gB8fmAbBt";

$ossl = new OpenSSL;

$ossl->set_privkeypass($pass);

//create a key pair
$ossl->do_csr();
echo "Generated certificate signing request<br><br>";


$privatekey=$ossl->get_privatekey();
echo "Private Key is:<BR><BR><TEXTAREA ROWS=20 COLS=75>".HTMLENTITIES($privatekey)."</TEXTAREA>";


$publickey=$ossl->get_publickey();
echo "<br><br>Public Key is:<br><br><TEXTAREA ROWS=20 COLS=75>".HTMLENTITIES($publickey)."</TEXTAREA><br><br>";


//wipe clean and start again
unset($ossl);
$ossl = new OpenSSL;

//get just the public key
$ossl->set_publickey($publickey);

$testtext="<b>I am secret</b>";

echo "Testing with ".$testtext."<br><br>";
//encrypt some text
$ossl->encrypt($testtext);


//get the encrypted text
$crypt=$ossl->get_crypt();

echo "Encrypted text is:<input size=65 value=\"".htmlentities($crypt)."\"><br><br>";

//get the envelope key also needed to decrypt the encrypted text
$ekey=$ossl->get_ekey();

echo "Envelope Key is: <input size=65 value=\"".htmlentities($ekey)."\"><br><br>";

//wipe clean and start again
unset($ossl);
$ossl = new OpenSSL;

//get the private key
$ossl->set_privatekey($privatekey, false, $pass);

$ossl->decrypt($crypt, $ekey);

echo "Text decrypted again to: ".$ossl->get_plain();
*/
?>
