<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 20/06/2006					*
	*																*
	****************************************************************/


//include_once "common.nx";

class Conf {
	var $conf;
	var $originalconf;
	var $xmllist;
	var $ident;
	
	var $conn;
	
	function Conf ($xmllist=XMLLIST) { 
		include_once "common.nx";
		
		if ($xmllist==false) { return; }
		
		
		$xmllist = explode(";",$xmllist);
		foreach ($xmllist as $x => $xml) {
				$xmllist[$x]="$xml.xml";
				
				// senao existe, pula esse
				if (!file_exists(DIRCONF.$xmllist[$x])) {
					unset ($xmllist[$x]);
					continue;
				}
				
		}
		
		$this->conn = new Conn();
		
		$xmllist = implode(";",$xmllist);
		
		$this->xmllist = $xmllist;
		$this->loadconf();
		$this->originalconf = $this->conf;
	}
	function loadconf($array=false) {
		if ($array!=false) {
			$this->conf=$array;
			return;
		}
		if (!is_writable(DIRSET)) {
			//$conn = new Conn();
			$this->conn->command(message::generate_function("NORMALIZE"));
		}
		
		if (!$this->conf) {
			$xmllist1=explode(";",$this->xmllist);
			foreach ($xmllist1 as $k => $xml) {
				//if (!file_exists(DIRSET.$xml)
				//	||filemtime(DIRSET.$xml)<filemtime(DIRCONF.$xml)				
				// verifica se o q tah no CONF eh diferente do SET
				if (md5_file(DIRCONF.$xml)!=@md5_file(DIRSET.$xml)) {
					// se for, copia do CONF pro SET
					copy(DIRCONF.$xml,DIRSET.$xml);
				}
					
				$xmllist1[$k]=DIRSET.$xml;
				
			}
			$xmllist1 = implode(";",$xmllist1);

			// vou abir na verdade o SET

			$this->conf = xml::loadxml ($xmllist1);
		}
	}
	function loadtpl($tpl) {
		return xml::loadxml(DIRTPL."/$tpl.xml.tpl");
		
	}
	
	function printconf() {
		print_r($this->conf);
	}
	
	function get($key) {
		$key = explode("/",$key);
		$ar = $this->conf;
		foreach ($key as $k) {
			if ($ar[$k]) {
				$ar = $ar[$k];
			} else {
				return FALSE;
			}
		}
		return $ar;
	}
	function set($key,$nob_value) {
		$key = explode("/",$key);
		
		foreach ($key as $k)
			$ekey .= "['$k']";
			
		$nob_conf = $this->conf;
			
		// para obfuscador:
		// nob_* nao podem mudar de nome
		eval ("\$nob_conf".$ekey." = \$nob_value;");
		
		$this->conf = $nob_conf;
		
		//print_r($this->conf);
		/*
		$ar = array();
		for ($i=count($key)-1;$i>=0;$i--) {
			$ar[$key[$i]]=$ar;
			unset($ar[$key[$i+1]]);

			if ($i==count($key)-1)
				$ar[$key[$i]]=$value;
		}

		//print_r($ar);

		$nar = multimerge($this->conf,$ar);
		print_r($nar);
		*/
	}

	function attrecwrite(&$conf) {
		$ret=array();
		$sret="";
		if (is_array($conf)&&$conf["_attributes"]) {
			$ret = $conf["_attributes"];
			$conf["_attributes"] = "NULL";
		}
		if (is_array($ret)) {
			foreach ($ret as $k => $v)
				$sret .= " $k=\"$v\"";
		}	
		return html_entity_decode($sret);
	}
	
	function recwrite ($conf,$pk="",$c=0) {
		$c++;
		$t = str_repeat("\t",$c);
		if (!is_array($conf))
			return $t."".$conf;

		foreach ($conf as $k => $v) {
			if (is_array($v)) {
				if ($v["_num"]) {
					//print_r($v);
					for ($i=0;$i<count($v)-1;$i++) {
					//foreach ($v as $i => $vi) {
						//if ($i=="_num") { continue; }
						$att = $this->attrecwrite($v[$i]);
						$ret .= "$t<$k$att>\n";
						$ret .= $this->recwrite($v[$i],$k,$c);
						$ret .= "\n$t</$k>\n";
					}
				} else {
					$att = $this->attrecwrite($v);
					// se o valor for vazio (tag vazia)
					// ou entao se soh tiver um tag e esse for _attibutes
					// eh pq tambem tah vazio, entao coloca no formato:
					// <tag attibutos />
					if (
						(trim($v)=="")
							||
						(
							(count($v)==1)&&($v["_attributes"])
						)
					   )
						{
						$ret .= "$t<$k$att />\n";
					} else {
						$ret .= "$t<$k$att>\n";
						$ret .= $this->recwrite($v,$k,$c)."\n";
						$ret .= "$t</$k>\n";
					}
				}
			} else {
				if ($v!="NULL") { 
					$v = html_entity_decode($v);
					$ret .= "$t<$k>$v</$k>\n";
				}
			}	
		}
		return $ret;
	}
	/**********
	 * retorna true ou false, conforme o resultado do copyxml
	 * o resultado pode ser acessado por this->ident
	 */
	function write () {
		if (!$this->ident) {
			$this->ident = conv::randkey();
		}
		
		foreach (explode(";",$this->xmllist) as $xml) {
			$tmp = explode(".",$xml);
			$section = $tmp[0];
			
			$finalxml = $this->recwrite($this->conf[$section]);
			$finalxml = "<$section>\n".$finalxml."</$section>";

			

			if (ININTERFACE==1) {
				$originalxml = $this->recwrite($this->originalconf[$section]);
				$originalxml = "<$section>\n".$originalxml."</$section>";
			
				@file_put_contents(DIRTMP."undo-".$this->ident."-$section.xml",$originalxml);
			}
			if ($this->writefinalxml($section,$finalxml)==false)
				return false;
			
		}
		return true;
	}
	function writefinalxml ($section,$finalxml) {

		//echo $finalxml;
		if (INCORE==1) {
			@file_put_contents(DIRCONF.$section.".xml",$finalxml);
			return;
		}
		//$conn = new Conn();
		file_put_contents(DIRTMP."$section.xml",$finalxml);
		if (!@file_put_contents(DIRSET."$section.xml",$finalxml)) {
			$this->conn->command(message::generate_function("NORMALIZE"));
			file_put_contents(DIRSET."$section.xml",$finalxml);
			clearstatcache();
		}
		if (ININTERFACE==1) { 
			$out=$this->conn->command(message::generate_function("COPYXML",DIRTMP,$section));
		}
		unlink(DIRTMP."$section.xml");
		
		// de qualquer jeito eu salvo a saida no wall
		record::wall($this->ident,$out);
		
		// se for um fail, eu retorno FALSE
		// ai a proxima etapa vai disponibilizar pro usuario o wall
		if (message::is_function($out,"FAIL")) {
			return false;
		}
		return true;
		
	}
	
	function writeproxy () {
		// nao vou fazer nada por enquanto
		
		
		//$conn = new Conn();
		$this->conn->command(message::generate_function("COPYPROXY"),$this->actident);
		//$conn->command(message::generate_function("MERGE","proxy"),$this->actident);
		
	}
	
	function undo($ident) {
		//echo $ident;
		$this->ident = $ident;
		foreach (explode(";",$this->xmllist) as $xml) {
			//$this->conf = xml::loadxml ($this->xmllist);
			$tmp = explode(".",$xml);
			$section = $tmp[0];
			$tmpfile = DIRTMP."undo-$ident-$section.xml";
			//echo $tmpfile;
			if (file_exists($tmpfile)) {
				$orig = @file_get_contents($tmpfile);
				if ($this->writefinalxml($section,$orig)==false)
					return false;
			}
			
		}
		record::act_log(_("Last operation undone"));
		return true;
	}

	/****
	 * copyxml (section)
	 * copia o xml pro local valido
	 * nao precisa instanciar
	 */
	function copyxml($section) {
		
		$this->conn->command(message::generate_function("COPYXML",DIRSET,$section));
	}	
	
	function merge($ident) {
		//$conn = new Conn();
		
		$merge_what="nonetwork";
		$xmllist1 = explode(";",XMLLIST);
		if (file_exists(DIRTMP."nx_networkchanged")) {
			$merge_what="all";
			unlink(DIRTMP."nx_networkchanged");
		}
		
		/*
		foreach ($xmllist1 as $section) {
			if (file_exists(DIRTMP."nx_networkchanged")) {
				// se nao mudou o network, nao vou mergea-lo, jah que
				// vai causar interrupcao na conexao adsl e nos pppoe
				if ($section=="network")
					$merge_what="all";
				$conn->command(message::generate_function("COPYXML",DIRSET,$section));
			}
		}
		*/	
		

		record::wall($ident,
			message::generate_function("INFO",_("Applying Configs..."))."\n");
		record::clean_act_log();
		$this->conn->command(message::generate_function("MERGE",$merge_what),$ident);
	}
	function discard($ident) {
		$xmllist1 = explode(";",$this->xmllist);
		record::wall($ident,
			message::generate_function("INFO",_("Discarding Changes..."))."\n");
		foreach ($xmllist1 as $k=>$xml) {
			copy(DIRCONF.$xml,DIRSET.$xml);
		}
		
		record::clean_act_log();
		record::wall($ident,
			message::generate_function("OK")."\n/EOF/\n");
	}
	
	function backup() {
		$time = time();
		exec ("cp ".DIRSET."settings.ini ".DIRCONF);
		exec ("cd ".NEXUS."/core/;tar -czvpf ".NEXUS."/core/data/backupconf/conf-$time.tgz conf");
		return NEXUS."/core/data/backupconf/conf-$time.tgz";
	}
	function restore($file) {
		if (!file_exists($file)) {
			return false;
		}
		
		$xmllist1 = explode(";",XMLLIST);


		$tmp = explode("/",$file);
		exec ("mkdir -p ".DIRTMP."/restoretmp");
		exec ("rm -fr ".DIRTMP."/restoretmp/*");
		exec ("cp $file ".DIRTMP."/restoretmp/");
		$filename = $tmp[count($tmp)-1];
		exec ("cd ".DIRTMP."/restoretmp/;tar xzvpf $filename");
		exec ("cd ".DIRTMP."/restoretmp/;rm -rf $filename");
		
		if (!file_exists(DIRTMP."/restoretmp/conf/")) {
			return false;
		}
		
		foreach ($xmllist1 as $section) {
			// salva o undo
			if (!file_exists(DIRTMP."/restoretmp/conf/$section.xml")) {
				continue;
			}
			
			exec ("cp -a ".DIRCONF."/$section.xml ".DIRTMP."undo-".$this->ident."-$section.xml");
			
			// joga o backup por cima
			exec ("cp -a ".DIRTMP."/restoretmp/conf/$section.xml ".DIRSET."/$section.xml");
			
			$this->copyxml($section);
		}
		
		// precisa copiar tambem a pasta do proxy, dentro de conf:
		exec ("cp -a ".DIRTMP."/restoretmp/conf/proxy/ ".DIRSET."/");
		$this->copyxml("dirproxy");
		
		
		return true;
	}
	
	function backupdata($file) {
		exec ("cd ".NEXUS."/core/;tar czvphf $file data");
	}
	
	function deletedata () {
		// nao posso deletar a pasta user
		// pq contem os totais dos usuarios e guests
	}
	
	/* touchconf
	 * normaliza as datas dos arquivos, deve ser executado apos uma mudan�a de hora
	 * ou reinicio do sistema
	 * nao precisa instanciar a classe
	 */	
	function touchconf () {
		exec ("touch ".DIRSET."/*");
		exec ("touch ".DIRCONF."/*");
	}
	
}

//$t=new Conf("network");
//$tmp[0]="aaaaaa";
//$tmp[1]="bbbbbb";
//$t->set("network/interfaces/interface",$t->conf[network][interfaces]["interface"]);

//print_r($t->conf);
//$t->printconf();
//echo $t->write();

//print_r( xml::loadxml ("objects.xml;objects-static.xml"));

?>
