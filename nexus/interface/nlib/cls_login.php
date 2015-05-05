<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 5, 2006					*
	*																*
	****************************************************************/

session_start();
define("IDLETIME",5000);
//define("IDLETIME",54000);

class Login {
		
		var $login = '';
		var $password = '';
		var $ip = '';
		var $file = '';
		var $time = '';
		var $login_auth = '';
		var $ip_auth = '';
		var $idletime_auth = '';
		var $arr_auth = '';
		var $debug = false;
		var $error = '';
		var $action = '';
		
		function __construct() {
			
			$this->file = DIRTMP."/auth.run";
			$this->time = time();
			//$this->debug = true;
			
		}
		
		function auth() {
			
			//if($this->action == "login" || $this->action == "login_expired"){
			//	$this->password = md5($this->password);
			//}
			
			//var_dump($this);
			//echo "SENHA: ".PASSWORD;
			
			$this->_debug("Validação do usuário",$this->debug);
			
			$login = "admin";
			
			$conn = new Conn();
			$ret = $conn->command(message::generate_function("CHECKROOT",$this->password));

			if($this->login == $login && strtoupper(trim($ret)) == "OK"){
			
				$this->_debug("Validou usuário",$this->debug);
				if($this->readAuth()){
					$this->createSession();
					return true;
				} else {
					return false;
				}
			} else {
				$this->_debug("Não validou usuário",$this->debug);
				$this->error = "login_error";
				return false;
			}
		}
		
		function readAuth(){
			
			$this->_debug("Leitura do arquivo auth.run",$this->debug);
			
			if(!file_exists($this->file)){
				$this->_debug("Criação do arquivo auth.run",$this->debug);
				fopen($this->file,"a+");
			}
			
			$file = trim(file_get_contents($this->file));
			$this->arr_auth = explode("\n",$file);
			//echo count($arr);
			
			$this->_debug("Parseamento do arquivo auth.run",$this->debug);
			
			$if = "";
			for($i = 0; $i < count($this->arr_auth); $i++){
				
				# explode usuários já logados
				$arr = explode(";",trim($this->arr_auth[$i]));
				
				# pega as informações dos logins ativos
				$this->login_auth 	= trim($arr[0]);
				$this->ip_auth 		= trim($arr[1]);
				$this->idletime_auth= trim($arr[2]);
				//print_r($_POST);
				//echo "<br>";
				//var_dump($this);
				if($this->login_auth == $this->login) {
					
					# Verifica se já está logado - se não tiver, escreve no arquivo
					if((($this->time - $this->idletime_auth) < IDLETIME) || $this->action == "login"){
						
						# Verifica se está logado com o mesmo IP
						if($this->ip == $this->ip_auth || $this->action == "have_auth"){
							
							$this->_debug("Alterou a hora do idletime",$this->debug);
				
							# atualiza o idletime
							$this->idletime_auth = $this->time;
							$this->ip_auth 		 = $this->ip;
							
						} else {
							
							$_SESSION['have_auth']['login'] 	= $this->login;
							$_SESSION['have_auth']['password'] 	= $this->password;
							
							$this->error = "have_auth";
							return false;
						
						}
						
					} else {
						
						$this->error = "login_expired";
						return false;
						
					}
					
					# atualiza o vetor atual do array
					$this->arr_auth[$i] = $this->login_auth . ";" . $this->ip_auth . ";" . $this->idletime_auth;
					$id = $i;
					break;
					
				} else {
					
					$this->login_auth = $this->login;
					$this->ip_auth = $this->ip;
					$this->idletime_auth = $this->time;
					$this->arr_auth[] = $this->login_auth . ";" . $this->ip_auth . ";" . $this->idletime_auth;
				
					
				}
				
				
				//$this->arr_auth[$i] = implode(";",$arr);
				
			}
			
			$this->_debug("Fim do perseamento do arquivo auth.run",$this->debug);
			
			$this->_debug("Escrevendo auth.run",$this->debug);
			
			$this->writeAuth();
			
			$this->_debug("Fim da escrita no arquivo auth.run",$this->debug);
			
			return true;
			
		}
		
		function writeAuth(){
			
			file_put_contents($this->file, trim(implode("\n",$this->arr_auth)));
			
		}
		
		
		function _debug($txt, $debug){
			
			if($debug){
				
				echo $txt."<br>";
				
			}
			
		}
		
		function createSession(){
			
			unset($_SESSION['have_auth']);
			
			$_SESSION['login']['login'] 	= $this->login;
			$_SESSION['login']['password'] 	= $this->password;
			
		}
		
		function logout() {
			unset($_SESSION['login']);
			unset($_SESSION['have_auth']);
			$this->cleanAuth();
		}
			
		function showLoginError(){
			echo "ERROR:".$this->error;
			//return;
			switch($this->error){
			
				case("login_error"):
					echo "<script>window.location = '/entrance/index.php?error=login_error'</script>";
					break;
				
				case("login_expired"):
					echo "<script>window.location = '/entrance/index.php?error=login_expired'</script>";
					break;
				
				case("have_auth");
					echo "<script>window.location = '/entrance/index.php?error=have_auth&ip=".rawurlencode($this->ip_auth)."&l=".rawurlencode($this->login)."'</script>";
					break;
			
			}
			
		}
		
		function cleanAuth () {
			unlink (DIRTMP."auth.run");	
			clearstatcache();
		}
			
		function autoauth () {
			
			$this->login 	 = $_SESSION['login']['login'];
			$this->password  = $_SESSION['login']['password'];
			$this->ip		 = $_SERVER['REMOTE_ADDR'];
			$this->action	 = "auth";
			
			if(!$this->auth()) {
				$this->showLoginError();
				return false;
			}
			return true;
		}
			
	}
?>