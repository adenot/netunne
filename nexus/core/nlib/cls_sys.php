<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 5, 2006					*
	*																*
	****************************************************************/



	class Sys  {
		
		/**
		 * changerootpass (pass) 
		 * altera a senha do root em shadow
		 * - nao precisa instanciar
		 */
		function changerootpass ($pass) {

			srand (mktime());  
			$salt = rand();

			$newpass = md5crypt ($pass, $salt);

			$ark = "/etc/shadow";
			$fd = fopen($ark,"r");
			$shadow = fread ($fd, filesize ($ark));
			fclose($fd);

			$linhas = explode ("\n",$shadow);
			for ($i=0;$i<count($shadow);$i++) {
				$tmp = explode(":",$shadow);
				if ($tmp[0]=="root") {
					$nlinha = $i;
				}
			}

			$linha = explode(":",$linhas[$nlinha]);
			$linha[1] = $newpass;
			$linha = implode(":",$linha);

			$linhas[$nlinha] = $linha;
			$shadow = implode("\n",$linhas);
			
			$fp = fopen ($ark,"w");
			fwrite ($fp, $shadow);
			fclose($fp);

		}
		
		/** 
		 * checkrootpass (pass)
		 * testa se a senha bate com a senha do root
		 * - nao precisa instanciar
		 */
		function checkrootpass ($pass) {
			// root:$1$14404027$lKo71A38VJcjJJ3uU8L9I/:13115:0:99999:7:::
			$ark = "/etc/shadow";
			$fd = fopen($ark,"r");
			$shadow = fread ($fd, filesize ($ark));
			fclose($fd);	
			$linhas = explode ("\n",$shadow);
			for ($i=0;$i<count($shadow);$i++) {
				$tmp = explode(":",$shadow);
				if ($tmp[0]=="root") {
					$nlinha = $i;
				}
			}
			$linha = explode(":",$linhas[$nlinha]);
			$rootpass = $linha[1];
			
			$tmp = explode("\$",$rootpass);
			$salt = $tmp[2];
			
			$pass = md5crypt($pass,$salt);
			
			if ($pass == $rootpass) { 
				return true;
			}
			return false;
			
		}
		
	}
	

?>
