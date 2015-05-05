<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 24/11/2006					*
	*																*
	****************************************************************/

class backup {
	function getbackupconf() {
		$bkp[]=array(_("Created on"));
		
		$i=0;
		if ($handle = opendir(DIRDATA."/backupconf/")) {

			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
		       	
					$tmp = intval(str_replace(array("conf-",".tgz"),"",$file));	
					
					if ($tmp==0) { continue; }
					
					$bkp[]=array("<!--".sprintf("%020d",  $tmp)."-->".conv::formatdate($tmp));
					//$bkp[]=array($tmp);
		
				}
			}
			closedir($handle);
		}
		return $bkp;
	}
}

?>
