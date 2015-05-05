<?php

// common.php
// FUNCOES GLOBAIS
//
//

/* esse arquivo vai ser usado tambem na interface (copiado pra lah)
 * entao os defines eu vou usar os definidos no common da interface
 */ 

//echo "entrando core_common";

define ("PRODID","1"); // id do produto, passado para a central de licensa

DEFINE("OPEN_SSL_CONF_PATH", "/etc/ssl/openssl.cnf");//point to your config file
DEFINE("OPEN_SSL_CERT_DAYS_VALID", 365);//1 year
DEFINE("OPEN_SSL_IS_FILE", 1);

include_once DIRNLIB."minixml/minixml.inc.php";

define ("PRODNAME","Netunne");
define ("PRODCLASS","Provider");
define ("VERSION","1.0");

$kversion = trim(exec("uname -r"));

if ($kversion == "2.6.23") { 
	define ("VERSION","1.1");
}

if (file_exists("/etc/squid3/")) {
	define ("PROXY","SQUID");
} else if (file_exists("/etc/oops/")) {
	define ("PROXY","OOPS");
}

if (!function_exists("write_ini_file")) {
	/******
	 * write_ini_file(path,assoc_array)
	 */
	function write_ini_file($path, $assoc_array)
	{
	   $content = '';
	   $sections = '';
	
	   foreach ($assoc_array as $key => $item)
	   {
	       if (is_array($item))
	       {
	           $sections .= "\n[{$key}]\n";
	           foreach ($item as $key2 => $item2)
	           {
	               if (is_numeric($item2) || is_bool($item2))
	                   $sections .= "{$key2} = {$item2}\n";
	               else
	                   $sections .= "{$key2} = \"{$item2}\"\n";
	           }     
	       }
	       else
	       {
	           if(is_numeric($item) || is_bool($item))
	               $content .= "{$key} = {$item}\n";
	           else
	               $content .= "{$key} = \"{$item}\"\n";
	       }
	   }     
	
	   $content .= $sections;
	
	   if (!$handle = fopen($path, 'w'))
	   {
	       return false;
	   }
	  
	   if (!fwrite($handle, $content))
	   {
	       return false;
	   }
	  
	   fclose($handle);
	   return true;
	}
}


class master_record {
	function dmesg_log ($msg) {
		shell_exec("echo \"$msg\" >> /var/log/dmesg-tmp.log");
	}
	function msg_log ($msg,$domain="null") {
		$date = date("M d H:i:s");
		//shell_exec("echo \"$date $domain $msg\" >> ".DIRLOG."actions.log");	
		file_put_contents(DIRLOG."actions.log","$date $domain $msg\n",FILE_APPEND);
	}
	function in_log($msg) {
		$date = date("M d H:i:s");
		//shell_exec("echo \"$date $msg\" >> ".DIRLOG."input.log");	
		file_put_contents(DIRLOG."input.log","$date $msg\n",FILE_APPEND);
	}	
	function out_log($msg) {
		$date = date("M d H:i:s");
		//shell_exec("echo \"$date $msg\" >> ".DIRLOG."output.log");	
		file_put_contents(DIRLOG."output.log","$date $msg\n",FILE_APPEND);
	}	
	function pid_log ($pid,$msg) {
		$date = date("M d H:i:s");
		$pid=escapeshellcmd($pid);
		shell_exec("echo \"$date $msg\" >> ".DIRLOGLISTENER."$pid.log");	
	}
	function wall_out ($pid,$array) {
		$array = serialize($array);
		$pid=escapeshellcmd($pid);
		shell_exec("echo \"$array\" >> ".DIRWALL."$pid.wall");
	}
	function wall($ident,$msg="") {
		if (trim($ident)=="") { return; }
		
		if ($msg==""&&!file_exists(DIRWALL."$ident.log"))
			return;
			
		if ($msg!="") {
			//shell_exec("echo \"$msg\" >> ".DIRWALL."$ident.log");
			file_put_contents(DIRWALL."$ident.log",$msg,FILE_APPEND);
			return $msg;
		} else {
			$msg = file_get_contents(DIRWALL."$ident.log");
			return $msg;
		}
	}
}

class master_message {
	/************************************************
	 * is_function (res, function)
	 * array - caso existe a funcao no res
	 * false - caso contrario
	 */
	function is_function ($res,$function) {
		if (!ereg("([A-Za-z0-9]+)\s*.*",$res,$vars)) {
			return FALSE;
		}
		$func = $vars[1];
		$attr = trim(ereg_replace("^".$func,"",$res));
		//echo "ATTR:".$attr;
		$attr=str_replace("(","",$attr);
		$attr=str_replace(")","",$attr);
		$vars=explode(",",$attr);
		$vars2[0]=strtoupper($func);
		foreach($vars as $v) {
			// tira as aspas em volta dos argumentos
			$vars2[]=str_replace(array("\"","'"),"",$v);
		}
		if (array_shift($vars2)==strtoupper($function)) {
			return $vars2;
		} 
		return FALSE;
	}
	/************
	 * has_function (texto, funcao)
	 * se a funcao existir no texto, retorna o array com o conteudo dela
	 * se nao existir, retorna FALSE
	 */
	function has_function ($res,$function) {
		$function = strtoupper($function);
		if (preg_match("/$function\\(([\\w\\W]*)\\)/",$res,$tmp0)==0)
			return false;
		//return($tmp0);
		
		$tmp1 = $tmp0[1];
		$tmp2 = explode("\",\"",$tmp1);
		for($i=0;$i<count($tmp2);$i++)
			$tmp2[$i] = str_replace("\"","",$tmp2[$i]);
		return $tmp2;
	}
	/*************************************
	 * witch_function (res)
	 * retorna:
	 * ret[0]=nome da funcao
	 * ret[1]=array dos dados da funcao
	 */
	function witch_function ($res) {
		if (preg_match("/(\\w+)\\s*\\(([\\w\\W\\d]*)\\)/",$res,$tmp)==0) {
			$ret[0]=trim($res);
			return $ret;
		}
			
		$ret[0]=strtoupper($tmp[1]);
		$tmp2 = explode("\",\"",$tmp[2]);
		
		for($i=0;$i<count($tmp2);$i++) {
			//$tmp2[$i] = str_replace("\"","",$tmp2[$i]);
			$tmp2[$i] = str_replace("\")","",$tmp2[$i]);
			$tmp2[$i] = str_replace("(\"","",$tmp2[$i]);
			$tmp2[$i] = str_replace("\"","",$tmp2[$i]);
		}
		
		$ret[1]=$tmp2;
		return $ret;
		
	}


	/***************************************************************
	 * alert (msg)
	 * recebe uma mensagem que retornar� como um alerta para o usu�rio
	 */
	function alert ($msg) {
		// por enquanto soh echoa
		// depois vai ser repassado para o pid do perl
		echo $msg;
		return $msg;
		
	}
	
	/***************************************************************
	 * input_function (str)
	 */
	function input_function ($str) {
		//echo $str;
		// \\((.+)\\)
		ereg("([A-Za-z0-9]+)\s*.*",$str,$vars);
		$func = $vars[1];
		$attr = trim(ereg_replace("^".$func,"",$str));
		//echo "ATTR:".$attr;
		$attr=str_replace("(","",$attr);
		$attr=str_replace(")","",$attr);
		$vars=explode(",",$attr);
		$vars2[0]=strtoupper($func);
		foreach($vars as $v) {
			// tira as aspas em volta dos argumentos
			$vars2[]=str_replace(array("\"","'"),"",$v);
		}
		return message::input_map($vars2);
	}
	
	function generate_function () {
		$args = func_get_args();
		$func = array_shift($args);
		foreach ($args as $k=>$v) 
			$args[$k]="\"".$v."\"";
		$ret = strtoupper($func)."(".implode(",",$args).")";
		return $ret;
	}
	
	function input_map($array,$usermap=0) {
		$map[servererror]="connid,msg,code";
		$map[info]="connid,info";
		$map[lic]="connid,license";
		$map[requestlicense]="NULL";
		$map[checklicense]="NULL";
		$map[merge]="service,param";
		$map[cmd]="c";
		$map[checkuser]="login,pass,ip,mac";
		$map[checkguest]="key,ip,mac";
		$map[copyxml]="path,xml";
		$map[wall]="wall";
		$map[npak]="action,file";
		$map["date"]="date,zone";
		$map[shutdown]="mode,when";
		$map[checkroot]="pass";
		$map[changeroot]="pass";
		$map[backup]="";
		$map[restore]="file";
		$map[enableinterface]="int";
		$map[timezone]="tz";
		$map[ping]="host";
		$map[traceroute]="host";
		$map[fastauth]="ip,mac";
		$map[disconnect]="login";
		$map[pppoedisconnect]="ip,int";
		$map[installproxy]="";
		$map[cleanproxy]="";
		$map[forceupdate]="";
		$map[changepass]="username,password,newpassword";
		$map[mergeconf]="service";
		
		if ($usermap==0)
			$map=$map[strtolower($array[0])];
		else
			$map=$usermap;
			
		$map=explode(",",$map);
		$i=1;
		foreach ($map as $k=>$v) {
			$ret[$v]=$array[$i];
			$i++;
		}
		$ret[func]=trim($array[0]);
		return $ret;
	
	}
}

class master_sysinfo {
	/*
	 * funcao obsoleta, prefira utilizar o objeto MAC.ethX
	 */
	function macaddress ($eth=0) {
	        $res = shell_exec("/sbin/ifconfig eth$eth");
	        $res = explode("\n",$res);
	        $res = $res[0];
	        $res = sscanf($res,"%s%s%s%s%s%s%s%s%s%s%s%s%s%s");
	        for ($i=0;$i<count($res);$i++) {
	                if (substr($res[$i],0,2)=="HW") {
	                        return $res[$i+1];
	                }
	        }
	        return 0;
	}
	
	/**
	 * retorna a utilizacao da particao de dados
	 * modo=0 => percentagem ocupada
	 * modo=1 => bytes livres
	 * modo=2 => bytes ocupados
	 *
	 * @param int $mode
	 * @return int
	 */
	function datadiskuse($mode=0) {
		$datadisk = trim(file_get_contents("/etc/nexusdatadisk"));
		$datadir = "/dev/$datadisk";
		$df = exec("df $datadir");
		
		$tmp = sscanf($df,"%s%s%s%s%s%s%s");
		
		$bytes_used = $tmp[2];
		$bytes_free = $tmp[3];
		
		$use = $tmp[4];
		$use = str_replace("%","",$use);
		
		if ($mode==0)
			return intval($use);
		if ($mode==1)
			return $bytes_free;
		if ($mode==2)
			return $bytes_used;
		
			
	}
	function sysdiskuse() {
		$df = exec("df /");
		
		$tmp = sscanf($df,"%s%s%s%s%s%s%s");
		
		$use = $tmp[4];
		$use = str_replace("%","",$use);
		return intval($use);
	}
	
}

class master_conv {

	/***
	 * startwith (start , text)
	 * true/false se start tah no comeco de text
	 */
	function startwith($start,$text) {

		if (strtoupper(substr($text,0,strlen($start)))==strtoupper($start)) {
			return TRUE;
		} 
		return FALSE;
	}


	function randkey($size=8) {
		return substr(md5(uniqid(rand(), true)),0,$size);
	}
	
	function formatdate($unixtime) {
		return date(_("m/d/Y h:i:sa"),$unixtime);
	}
	function formatdatefile($unixtime) {
		return date(_("Y-m-d-H\hi"),$unixtime);
	}
	function formatmonth($num) {
		$months = explode("|",_("January|February|March|April|May|June|July|August|September|October|November|December"));
		$num--;
		return $months[$num];
	}
	
	function arraymerge($array1="",$array2="") {
		if (!is_array($array2))
			return $array2;
			
		if (!is_array($array1))
			return $array1;
		
		if (func_num_args()==2) 
			return array_merge($array1,$array2);
		else {
			$args = func_get_args();
			$array = array();
			foreach ($args as $arg) {
				if (is_array($arg))
					$array = array_merge($array,$arg);
			}
			return $array;
		}
	}

	/** 
	 * tplreplace(tpl,array)
	 * EXEMPLO
	 * ENTRADA:
	TPL:
	aaaaaaaaaaaa
		{aaa} asap {apb}
	aaaaaaaaaa aaaaaa
		aaaaaaaaaa {aab}
	{aac}aaaaaa{aac}
		
	ARRAY:
	$arr[aab]="AAB";
	$arr[aaa][0][apb]="APB1";
	$arr[aaa][1][apb]="APB2";
	$arr[aac]="ACC"; // se esse nao existir, nao imprime a linha
	
	echo conv::tplreplace($tpl,$arr);
	
	 * SAIDA:
	aaaaaaaaaaaa
	         asap APB1
	         asap APB2
	aaaaaaaaaa aaaaaa
	        aaaaaaaaaa AAB
	aaaaaaAAC
	*/
	function tplreplace($tpl,$array) {
	        //echo "recebido: $tpl";print_r($array);
	        // pego a primeira variavel {var} da linha, se nao existir, apago a linha
	        $tpl = html_entity_decode($tpl);
	        
	        $lines = explode("\n",$tpl);
	        for ($i=0;$i<count($lines);$i++) {
	        	// se comeca com {xxx}
	        	//echo "TESTING: ".$lines[$i]."\n";
	        	if (ereg("^\\{([a-zA-Z0-9_-]+)\\}",trim($lines[$i]),$tmp)) {
	        		// tmp[0]={chave}
	        		// tmp[1]=chave
	        		
	        		//print_r($tmp);
	        		
	        		// tiro a chave do inicio
	        		$tmptpl = str_replace($tmp[0],"",$lines[$i],&$count);
	        		
	        		if ($count>1) {
	        			// eh o caso 3, onde o parametro era opcional
	        			// {xxx} aaaaaaa{xxx}
	        			
	        			//echo "caso3";
	        			
	        			if (!$array[$tmp[1]]) { 
	        				unset($lines[$i]);
	        				continue;
	        			}
	        			
	        			// tirando o primeiro {xxx}
	        			$lines[$i] = preg_replace('/{'.$tmp[1].'}/',"",$lines[$i],1);
	        			$lines[$i] = preg_replace('/{'.$tmp[1].'}/',$array[$tmp[1]],$lines[$i]);
	        			
	        			continue;
	        		}
	        		//print_r($array);
	        		$n=0;
	        		// verifico se existe um array com a chave como chave (?)
	        		if (is_array($array[$tmp[1]])) {
	        			//echo "achou ".$tmp[1];
	        			// se existe, vou pegando os parametros
		        		for ($a=0;$a<count($array[$tmp[1]]);$a++) {
		        			$newline[$n]=$tmptpl;
		        			foreach ($array[$tmp[1]][$a] as $k => $v) {
		        				$newline[$n]=str_replace("{".$k."}",$v,$newline[$n]);
		        			}
							$n++;
		        		}
		        		if (!$newline) { $newline=array(); }
	        			$lines[$i]=implode("\n",$newline);
	        		} else {
	        			// se nao existe o array, some a linha
	        			if (trim($tmptpl)!="")
	        				unset($lines[$i]);
	        		
	        		}
	        	}
	        }
	        $tpl = implode("\n",$lines);
	        
	        foreach ($array as $k => $v) {
	                $tpl = str_replace("{".$k."}",$v,$tpl);
	        }
	        //echo $tpl."\n";
	        return $tpl;
	}
	
	
	
	//////////////////////////////////////////////////
	// dataseek
	//
	// procura por dados em banco de dados especificado
	// no dsn do core.xml
	// se um xml tiver um tag especial (pdata=nome da tabela)
	// entao puxa os dados
	function dataseek ($array) {
		global $pdata;
		if (!$pdata)
			$pdata = new pdata();
	
		foreach ($array as $k => $v) {
			if (is_array($v)&&$v[pdata]) { 
				// abre o banco e joga o resultado em $v
				$table = $v[pdata];
				$pres = $pdata->pdo->query("SELECT * FROM $table;");
				$res = $pres->fetchAll(PDO::FETCH_ASSOC);
				$array[$k]=$res;
				$array[$k][_num]=count($res);
			} else if (is_array($v))  {
				$array[$k] = conv::dataseek($v);
			}
		}
		return $array;
		
	}
	
	/**************************************************************
	 * arrayclean ($array)
	 * refaz o array numerico sem os valores nulos
	 * [1]=a
	 * [2]=
	 * [3]=b
	 * vira:
	 * [1]=a
	 * [2]=b
	 */
	 function arrayclean ($array) {
	 	foreach ($array as $v)
	 		if (trim($v)!="")
	 			$newarray[]=$v;
	 	return $newarray;
	 }
	
	//print_r(xml::loadxml("users.xml"));
	
	function cleanout($out) {
	
			$out = str_replace("\\\n"," ",$out);
			$out = str_replace("\t","",$out);
			$out = ereg_replace("[ ]{2,}"," ",$out); // troca 2 ou mais espacos por 1 espaco
			$out = ereg_replace("[\n]{2,}","\n",$out);
			return html_entity_decode($out);
	
	}
	
	function ipsum ($ip,$sum) {
		$iplong = ip2long($ip);
		return long2ip($iplong+$sum);
	}
}

class master_xml {
	
	//////////////////////////////////////////////////
	// xml::loadxml
	//
	// carrega um xml pra um array
	// pode receber varios (separados por ;)
	// que serao concatenados
	//
	function loadxml ($file) {

		$file = explode(";",$file);
		
		$xmlDoc = new MiniXMLDoc();
		for ($i=0;$i<count($file);$i++) {
			if (!(strstr($file[$i],"/")))
				$file[$i]=DIRCONF.$file[$i];
			
			if (file_exists($file[$i].".user"))
				$file[$i]=$file[$i].".user";
				
			if (!file_exists($file[$i])) { continue; }
				
			$xml1 = file_get_contents($file[$i]);
			$xmlDoc->fromString($xml1);
		}
		return conv::dataseek($xmlDoc->toArray());				
	}
	
	/*********************
	 * xml::getxmlval(var,array)
	 * pega um valor podendo estar direto ou dentro de atributo
	 * retorna false se nao existir o valor
	 */
	function getxmlval ($val,$ar) {
		if ($ar[$val]) {
			return $ar[$val];
		} else if ($ar["_attributes"][$val]) {
			return $ar["_attributes"][$val];
		} else {
			return FALSE;
		}
	}
	//////////////////////////////////////////////////
	// getcoreconfig
	//
	// retorna um array com as variaveis do core.xml
	//
	function getcoreconfig () {
		$xmlDoc = new MiniXMLDoc();
		$xmlDoc->fromFile(NEXUS.'/core/conf/core.xml');
		$conf = $xmlDoc->toArray();
		return($conf);
	}
	
	//////////////////////////////////////////////////
	// xml::normalizeseq ($array) 
	//
	// normaliza uma sequencia XML vinda do minixml 
	//
	function normalizeseq($array) {
		//echo "seq:";print_r($array);
	        if (is_integer($array['_num'])) {
	                unset($array['_num']);
	                if ($array['_attributes']) {
	                        foreach($array['_attributes'] as $k => $v) {
	                                $array[$k]=$v;
	                        }
	                        unset($array['_attributes']);
	                }
	                return ($array);
	        } else {
		    	// preciso ver se jah existe uma sequencia
		    	if ($array[0]) {
		    		return $array;
	        	} else {
	            	$narray=array($array);
	            	if ($narray[0]=="") { return array(); }
		            return $narray;
	        	}
	        }
	}
}
/*
$tpl = "aaaaaaaaaaaa
{aaa}asap {apb}
aaaaaaaaaa aaaaaa
aaaaaaaaaa {aab}";
	$arr[aab]="AAB";
	//$arr[aaa]="";
	//$arr[aaa][0][apb]="APB1";
	//$arr[aaa][1][apb]="APB2";
	
	echo master_conv::tplreplace($tpl,$arr);
*/
?>
