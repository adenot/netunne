<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 02/07/2006					*
	*																*
	****************************************************************/


class Npak {
	
	var $npaklist;
	
	
	function autoinstall () {
		/*
		 * ROTEIRO:
		 * baixo a lista,
		 * pego a versao atual
		 * listo os pacotes com a versao maior q atual	
		 * instalo os pacotes
		 * coloco como nova versao a versao do maior pacote
		 */
		 
		record::msg_log("Starting auto update","npak"); 
		$ver = $this->getversion();

		// obtenho a lista e salvo no wall atual
		//record::wall($GLOBALS[wall],$this->getlist());
		
		// parseio a lista
		$parseret = $this->parselist();
		
		if ($parseret == false)
			return false;

		$list0 = $this->npaklist[npak];
		
		//print_r($list0);
		
		foreach ($list0 as $l0) {
			if ($l0["Product"]==PRODID) 
				$list[$l0["Version"]] = $l0;
		}
		ksort($list,SORT_NUMERIC);
		unset($list0);
		
		foreach ($list as $v=>$data) {
			$ver = $this->getversion();
			if ($ver<$v) {
				//echo "instalando ".$data[Package]."\n";
				$this->install($data[Package]);
				$updated=1;
			}
			
		}
		
		if (!$updated) 
			record::msg_log("Nothing to update","npak");
			
		record::msg_log("End of auto update","npak");
	}
	
	/***
	 * getversion
	 * retorna um int com o numero da versao
	 * nao precisa instanciar o objeto
	 */
	function getversion () {
		$verfile = DIRCONF."version.ini";
		if (!@file_exists($verfile)) {
			//$version[version]=0;
			//write_ini_file($verfile,$version);
			//clearstatcache();
			return 0;
		}
		$ver = @parse_ini_file($verfile);
		return $ver[version];
	}
	function updateversion ($version) {
		clearstatcache();
		$verfile = DIRCONF."version.ini";
		if (!file_exists($verfile)) {
			$ver = parse_ini_file($verfile);
		}
		$ver[version]=$version;
		write_ini_file($verfile,$ver);
		clearstatcache();
	}
		 
	
	function download($file,$url) {
		//echo "downloading $file $url";	
		exec("/usr/bin/wget -O $file --progress=dot $url");
		
	}
	
	function getlist () {
		$url[] = "http://www.console.com.br/center/npak/";
		$url[] = "http://www2.console.com.br/center/npak/";
		$url[] = "http://www.netunne.com/center/npak/";
		$url[] = "http://www2.netunne.com/center/npak/";
		$url[] = "http://puc.console.com.br/center/npak/";
		$url[] = "http://puc2.console.com.br/center/npak/";		

		$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

		for ($i=0;$i<count($url);$i++) {
			unset($curl_error);
			unset($curl_errno);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url[$i]."npak.list");
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			
			$result=curl_exec ($ch);
			$curl_error = curl_error($ch);
			$curl_errno = curl_errno($ch);
			$curl_info  = curl_getinfo($ch);
			curl_close($ch); 

			if ($curl_errno==0) { 
				if ($curl_info['http_code']==200) {
					$res .= "INFO('Connection Established')\n"; 
					break;
				} else {
					$curl_error = "Invalid page";
				}
			}
			
			$res .= "ALERT('$curl_error, trying next server (#$i)... ',NEXTSERVER)\n";
			
		}
		if (trim($result)=="") {
			$res .= "ERROR('All servers failed! Please check your connection!',CONNERROR)";
			return $res;
		} else {
			
			$result = $url[$i]."\n".$result;
			file_put_contents(DIRTMP."npak.list",$result);clearstatcache();
			return message::generate_function("result",$result);
		}

		
		
	}
	
	function parselist ($file="default") {
		if ($this->npaklist&&$file=="default") 
			return $this->npaklist;

		clearstatcache();
		if ($file=="default") {
			$file = DIRTMP."npak.list";
			$withurl=1;
		}
		$nlist=@file_get_contents($file);
		if (!$nlist) {
			return false;
		}
		
//      print_r($nlist);
        if ($nlist=="0") { return 0; }

		if ($withurl==1) {
			$tmp = explode("\n",$nlist);
			$url = $tmp[0];
			unset($tmp[0]);
			$nlist = implode("\n",$tmp);
		}
        $nlist = explode("\n\n",$nlist);
        for ($i=0;$i<count($nlist);$i++) {
                // cada pacote tem um espaco de 2 enters entre eles
                $plist=explode("\n",$nlist[$i]);
                for ($j=0;$j<count($plist);$j++) {
                        if (trim($plist[$j])=="") { continue; }
                        $temp = explode(":",$plist[$j]);
                        $temp[1] = str_replace($temp[0].":","",$plist[$j]);
                        if ($ret[$i][$temp[0]]) {
                                $ret[$i][$temp[0]].="\n".trim($temp[1]);
                        } else {
                                $ret[$i][$temp[0]]=trim($temp[1]);
                        }
                }
        }
        $aret[npak] = $ret;
        $aret[url]  = $url;
        
        $this->npaklist = $aret;
        return $aret;
		
	}
	
	
	
	function install($pack,$file="no") {

		
		/* ob_start();
		print_r($nlist);
		file_put_contents("/tmp/saida11",ob_get_contents());
		ob_end_clean();
		print_r($nlist[npak]); */
		
		if ($file=="no") {
			$nlist = $this->parselist();
			
			foreach ($nlist[npak] as $npak) {
				if ($pack==$npak[Package]) {
					$file = $npak[Filename];
					$md5  = $npak[MD5sum];
					$version = $npak[Version];
				}
			}
			$url = $nlist[url];
			
			for ($i=1;$i<5;$i++) {
				if (md5_file(DIRTMP.$file)!=$md5) {
					record::wall($GLOBALS[wall],"INFO(\"".sprintf(_("Downloading..."),$url,$i)."\")\n");
					record::msg_log("Downloading {".$url.$file."}, tentative {".$i."}","npak");
					
					$this->download(DIRTMP.$file,$url.$file);
				}
			}
			
			if (md5_file(DIRTMP.$file)!=$md5) {
				record::wall($GLOBALS[wall],"INFO(\""._("Cannot download file! Check your connection")."\")\n");
				record::msg_log("Cannot download {".$url.$file."}","npak");
	
				return;
			}
			
		} 
			
		record::wall($GLOBALS[wall],"INFO(\""._("Installing...")."\")\n");
		record::msg_log("Installing {$pack} {$file}","npak");
		
		if (file_exists(DIRTMP."/npak/$pack/")) {
			exec ("rm -rf ".DIRTMP."/npak/$pack/");
		}
		
		exec ("mkdir -p ".DIRTMP."/npak/");
		exec ("tar -C ".DIRTMP."/npak/ -xzvpf ".DIRTMP.$file);
		
		if (!file_exists(DIRTMP."/npak/$pack/install.sh")) {
			record::wall($GLOBALS[wall],"ERROR(\"".sprintf(_("Not a valid Npak file!"),$pack)."\")\n");
			record::msg_log("Not a valid Npak file","npak");
			return;
		}
		
		exec ("sh ".DIRTMP."/npak/$pack/install.sh");
		
		record::wall($GLOBALS[wall],"INFO(\"".sprintf(_("%s Installed"),$pack)."\")\n");
		record::msg_log("Package {".$pack."} installed","npak");
		
		if ($file!="no") {
			$info = $this->parselist(DIRTMP."/npak/$pack/info");
			$version = $info[npak][0][Version];
			ob_start();
			print_r($info);
			file_put_contents("/tmp/saida3",ob_get_contents());
			ob_end_clean();

		}
		
		$this->updateversion($version);
				
		
	}
	
	
	
}

?>
