<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 05/02/2008					*
	*																*
	****************************************************************/


if (constant("PROXY")=="SQUID") {
	
	if (defined("INCORE")) {
		@unlink(DIRTPL."/proxy.xml.tpl");
		@exec("ln -s ".DIRTPL."/proxysquid.xml.tpl ".DIRTPL."/proxy.xml.tpl");
	}
	
	class Proxy {
		
		var $proxytpl;
		var $fwtpl;
		var $conf;
		var $confobj;
		var $obj;
		var $out;
		var $newproxy=false;
		
		var $config_out;
		var $internal_out;
		var $init_out;
		var $cache_out;
		var $reload_out;
		
		var $acls;
		var $aclstemplate;
		var $plans;
		var $users;
		var $out_acls;
		var $out_aclgroups;
		
		var $last_seq;
		
		var $def_names;
		var $def_values;
		
		var $ret;
		
		function openproxyxml () {
			if ($this->conf) { return; }
			$this->conf = $this->confobj->conf;
			
		}
		function openproxytpl () {
			$this->proxytpl = xml::loadxml(DIRTPL."/proxysquid.xml.tpl");
		}
		
		function openfwtpl () {
			if (!$this->fwtpl) 
				$this->fwtpl = xml::loadxml(DIRTPL."/forward.xml.tpl");
		}
		
		function writeproxyxml () {
			$this->confobj->conf = $this->conf;
			$conf->write();
		}
		
		function __construct () {
			include_once "common.nx";
			
			$this->confobj = new Conf("info;proxy;forward");
			$this->obj = new Object();
			
			$this->openproxyxml();
			$this->openproxytpl();
			$this->openfwtpl();
			
			$this->plans 	= xml::normalizeseq($this->conf[forward][plans][plan]);
			$this->acls 	= xml::normalizeseq($this->conf[forward][acls][acl]);
			$users 			= xml::normalizeseq($this->conf[forward][users][user]);
	
			$this->aclstemplate = xml::normalizeseq($this->fwtpl[aclstemplate][template]);
			
			$this->users=array();
			
			foreach ($users as $k=>$user) {
				$this->users[$user[plan]][]=$user;
				if ($users[$k]) { unset ($users[$k]); }
			}
			if (is_array($users) && count($users)>0)
				unset($users);
			
			//print_r($this->users);
			
			$this->def_names	=array("memcache","cache","object");
			$this->def_values	=array(32,200,5);
		}
		
		function fileout() {
	
			foreach ($this->config_out as $int => $config)
				file_put_contents(DIROUT."/proxy/squid-$int.conf",$config."\n");
	
			foreach ($this->cache_out as $int => $config)	
				file_put_contents(DIROUT."/proxy/cache-$int.init",$config);
	
			file_put_contents(DIROUT."/proxy/squid.reload",$this->reload_out);
	
			file_put_contents(DIROUT."/proxy/squid.init",$this->init_out);
			
	
		}
		
		function install () {
			$install_cmd = explode("\n",$this->proxytpl[install]);
			
			foreach ($install_cmd as $cmd) {
				$cmd = str_replace("\$NEXUS",NEXUS,trim($cmd));
				$ret .= "Executando $cmd\n";
				$ret .= shell_exec(html_entity_decode($cmd))."\n";
			}
			$this->ret = $ret;
			return file_exists("/etc/squid3/");
		}
		
		function clean() {
			
			$cmd = "sh ".NEXUS."/core/bin/scripts/exec.sh /bin/bash /etc/squid3/cache.init clean";
			$ret = shell_exec(html_entity_decode($cmd))."\n";
	
			$cmd = "sh ".NEXUS."/core/bin/scripts/exec.sh /bin/bash /etc/squid3/squid.init";
			$ret = shell_exec(html_entity_decode($cmd))."\n";
			
			return $ret;
			
		}
	
		/*
		function returnacl($aclnum) {
			$acls = $this->acls;
			for ($i=0;$i<count($acls);$i++) {
				if ($acls[$i][id]==$aclnum) {			
					if ($acls[$i][block]||$acls[$i][unblock]||$acls[$i][ipblock]||$acls[$i][ipunblock])
						return $acls[$i];
				}
					
			}
			return false;
		}
		*/
	
		function returnacl($aclnum) {
			$acls = $this->acls;
			for ($i=0;$i<count($acls);$i++) {
				if ($acls[$i][id]==$aclnum) 
					return $this->returnacltemplate($acls[$i]);
			}
			return -1;
		}
		
		function returnacltemplate($aclrule) {
			if (!$aclrule[rule]) { return $aclrule; }
			
			$ruleid = $aclrule[rule];
			
			$aclstemplate = $this->aclstemplate;
			for ($i=0;$i<count($aclstemplate);$i++) {
				if ($aclstemplate[$i][id]==$ruleid) {
					$acltemplate=$aclstemplate[$i];
					break;
				}
			}
			if (!$acltemplate) { return $aclrule; }
			
			$acls = xml::normalizeseq($acltemplate[acls][acl]);
			$files0= xml::normalizeseq($acltemplate[files][file]);
			
			foreach ($files0 as $file) {
				$files[$file[name]]=$file[content];
				$filename = conv::randkey(6).".txt";
				file_put_contents(DIRCONF."/proxy/".$filename,trim($file[content]));
				$files[$file[name]]=$filename;
			}
			
			$ret=array();
			foreach ($acls as $acl) {
				foreach ($acl as $rule_key=>$rule_value) {
					foreach ($files as $file_key=>$file_name) {
						$count=0;
						$acl[$rule_key] = str_replace("{".$file_key."}",$file_name,$rule_value,$count);
						if ($count>0) { break; }
					}
				}
				$ret[]=$acl+$aclrule;
			}
			
			return $ret;
		}
	
		
		function merge_plan ($plan) {
			//if (!$plan[proxy][enabled]) { return; }
			
	
	/*
			$var=array();
			$var[acl_denys];
			foreach ($acllist as $acllist0) {
				$tmp[acllist]=$acllist0;
				$var[acldenys][]=$tmp;
			}
			*/
			
			if (!$this->users[$plan[id]]) {
				return;
			}
			
			// AGORA A LISTA DE IPS CONTIDOS NESSE PLANO
			/*
			 * A RESPONSABILIDADE DESSA LISTA EH DO CLS_FORWARD
			foreach ($this->users[$plan[id]] as $user) { 
				if (trim($user[ip])!="")
					$iplist[]=$user[ip];
			}
			*/
			
			$iplist_file = DIRTMP."nx_proxy_".$plan[id]."_iplist";
			
			//file_put_contents($iplist_file,implode("\n",$iplist));
			
			
			// mais um acl, agora eh o q vai pro network_acl do plano
			$var2[aclname]="NETWORK".$plan[id];
			$var2[acltype]="src";
			$var2[aclvalue]="\"".$iplist_file."\"";
			$this->out_acls[]=$var2;
			
			$acliplist=$var2[aclname];
	
			
			// preciso gerar os acls, jogar pro this->acls, depois criar o "group"
		
			if (trim($plan[acls])!="") 
				$planacls = explode(",",$plan[acls]);
			else 
				$planacls = array();
			
			$seq=$this->last_seq;
			
			foreach ($planacls as $k=>$v) {
				$planacls[$k]=$this->returnacl($v);
			}
			//print_r($planacls);	
			
			/*
			$aclstemplate = explode(",",$plan[aclstemplate]);
			$planaclstemplate=array();
			foreach ($aclstemplate as $aclid) {
				$planaclstemplate[]=$this->returnacltemplate($aclid);
			}
			
			
			
			//print_R($planaclstemplate);
	
			$aclstemplate=array();
			foreach ($planaclstemplate as $acltemplate) {
				if (count($acltemplate)==0) { continue; }
				$aclstemplate = array_merge($this->processacltemplate($acltemplate,array()),$aclstemplate);
			}
			
			$planacls = array_merge($planacls,$aclstemplate);
			
			*/
	
			$seq=0;
			foreach ($planacls as $planacl) {
				$this->processacl($planacl,$acliplist,$seq);
			}
			
			$var3[acl_list]="NETWORK".$plan[id];
			$var3[action]="allow";
			$this->out_aclgroups[]=$var3;
			
			$this->last_seq=$seq;
			
			return;
			
			/*
			foreach ($planacls as $planacl) {
	
				//$planacl=$this->returnacl($planacl);
				
				if (is_bool($planacl) && $planacl==false) { continue; }
				
				$seq++;
				$var=array();
				$acls_ipblock=array();
				$acls_urlblock=array();
				$acls_time=array(); // vai entrar no final dos dois acima
				$acllist=array();
				
				$acls_ipblock[]=$acliplist;
				$acls_urlblock[]=$acliplist;
				
				if ($planacl[ipblock]) {
					$var[aclname]="IPBLOCK".$seq;
					$var[acltype]="dst";
					$var[aclvalue]="\"".DIRCONF."/proxy/".$planacl[ipblock]."\"";
					
					$acls_ipblock[]=$var[aclname];
					
					$this->out_acls[]=$var;
					
				} 
				if ($planacl[ipunblock]) {
					$var[aclname]="IPUNBLOCK".$seq;
					$var[acltype]="dst";
					$var[aclvalue]="\"".DIRCONF."/proxy/".$planacl[ipunblock]."\"";
					
					// igual acima, soh que negando [!]
					$acls_ipblock[]="!".$var[aclname];
					
					$this->out_acls[]=$var;
					
				}
				if ($planacl[block]) {
					$var[aclname]="BLOCK".$seq;
					$var[acltype]="url_regex";
					$var[aclvalue]="\"".DIRCONF."/proxy/".$planacl[block]."\"";
					
					$acls_urlblock[]=$var[aclname];
					
					$this->out_acls[]=$var;
					
				}
				if ($planacl[unblock]) {
					$var[aclname]="UNBLOCK".$seq;
					$var[acltype]="url_regex";
					$var[aclvalue]="\"".DIRCONF."/proxy/".$planacl[unblock]."\"";
					
					$acls_urlblock[]="!".$var[aclname];
					
					$this->out_acls[]=$var;
					
				}
				if (($planacl[timestart]&&$planacl[timestop]) || $planacl[days]) {
					$var[aclname]="TIME".$seq;
					
					$var[acltype] = "time";
					
					$var[aclvalue]="";
					
					if ($planacl[days]) {
						$var[aclvalue]=substr($planacl[days],0,1)." ";
						$days = explode(",",$planacl[days]);
	#               S - Sunday
	#               M - Monday
	#               T - Tuesday
	#               W - Wednesday
	#               H - Thursday
	#               F - Friday
	#               A - Saturday
	
						$squid_days[Sun]="S";
						$squid_days[Mon]="M";
						$squid_days[Tue]="T";
						$squid_days[Wed]="W";
						$squid_days[Thu]="H";
						$squid_days[Fri]="F";
						$squid_days[Sat]="A";
						
						foreach ($days as $d) 
							$newdays[]=$squid_days[$d];
							
						$newdays=implode("",$newdays);
						
						$var[aclvalue]=$newdays." ";
						
						
					}
					if ($planacl[timestart]) {
						$var[aclvalue].=$planacl[timestart]."-".$planacl[timestop];
					}
					
					$acls_time[]=$var[aclname];
					
					$this->out_acls[]=$var;
				}
				
			// URL
				if (count($acls_urlblock)>1) {
					echo "acl urlblock";
					$acls = array_merge($acls_urlblock,$acls_time);
					$var3[acl_list]=implode(" ",$acls);
					$var3[action]="deny";
					$this->out_aclgroups[]=$var3;
				}
			// IP
				if (count($acls_ipblock)>1) {
					echo "acl ipblock";
					$acls = array_merge($acls_ipblock,$acls_time);
					$var3[acl_list]=implode(" ",$acls);
					$var3[action]="deny";
					$this->out_aclgroups[]=$var3;
				}
	
				
			}
			
			$var3[acl_list]="NETWORK".$plan[id];
			$var3[action]="allow";
			$this->out_aclgroups[]=$var3;
			
			$this->last_seq=$seq;
			
			return;
			*/
			//conv::tplreplace($this->proxytpl[group],$var);
			
			
		}
		
		function processacl ($planacl,$acliplist,&$seq) {
			
				
			//$planacl=$this->returnacl($planacl);
			
			if ($planacl[0]) {
				// quando eh um ACL de RULE, ele tem varios acls dentro de 1, entao recursivamente eu passo por eles
				for ($i=0;$i<count($planacl);$i++) {
					$this->processacl($planacl[$i],$acliplist,$seq);
				}
				return;
			}
			if (!($planacl[block]||$planacl[unblock]||$planacl[ipblock]||$planacl[ipunblock])) {
				return;
			}
			
			if (is_bool($planacl) && $planacl==false) { return; }
			
			$seq++;
			$var=array();
			$acls_ipblock=array();
			$acls_urlblock=array();
			$acls_time=array(); // vai entrar no final dos dois acima
			$acllist=array();
			
			$acls_ipblock[]=$acliplist;
			$acls_urlblock[]=$acliplist;
			
			if ($planacl[ipblock]) {
				$var[aclname]="IPBLOCK".$seq;
				$var[acltype]="dst";
				$var[aclvalue]="\"".DIRCONF."/proxy/".$planacl[ipblock]."\"";
				
				$acls_ipblock[]=$var[aclname];
				
				$this->out_acls[]=$var;
				
			} 
			if ($planacl[ipunblock]) {
				$var[aclname]="IPUNBLOCK".$seq;
				$var[acltype]="dst";
				$var[aclvalue]="\"".DIRCONF."/proxy/".$planacl[ipunblock]."\"";
				
				// igual acima, soh que negando [!]
				$acls_ipblock[]="!".$var[aclname];
				
				$this->out_acls[]=$var;
				
			}
			if ($planacl[block]) {
				$var[aclname]="BLOCK".$seq;
				$var[acltype]="url_regex";
				$var[aclvalue]="\"".DIRCONF."/proxy/".$planacl[block]."\"";
				
				$acls_urlblock[]=$var[aclname];
				
				$this->out_acls[]=$var;
				
			}
			if ($planacl[unblock]) {
				$var[aclname]="UNBLOCK".$seq;
				$var[acltype]="url_regex";
				$var[aclvalue]="\"".DIRCONF."/proxy/".$planacl[unblock]."\"";
				
				$acls_urlblock[]="!".$var[aclname];
				
				$this->out_acls[]=$var;
				
			}
			if (($planacl[timestart]&&$planacl[timestop]) || $planacl[days]) {
				$var[aclname]="TIME".$seq;
				
				$var[acltype] = "time";
				
				$var[aclvalue]="";
				
				if ($planacl[days]) {
					$var[aclvalue]=substr($planacl[days],0,1)." ";
					$days = explode(",",$planacl[days]);
	#               S - Sunday
	#               M - Monday
	#               T - Tuesday
	#               W - Wednesday
	#               H - Thursday
	#               F - Friday
	#               A - Saturday
	
					$squid_days[Sun]="S";
					$squid_days[Mon]="M";
					$squid_days[Tue]="T";
					$squid_days[Wed]="W";
					$squid_days[Thu]="H";
					$squid_days[Fri]="F";
					$squid_days[Sat]="A";
					
					foreach ($days as $d) 
						$newdays[]=$squid_days[$d];
						
					$newdays=implode("",$newdays);
					
					$var[aclvalue]=$newdays." ";
					
					
				}
				if ($planacl[timestart]) {
					$var[aclvalue].=$planacl[timestart]."-".$planacl[timestop];
				}
				
				$acls_time[]=$var[aclname];
				
				$this->out_acls[]=$var;
			}
			
		// URL
			if (count($acls_urlblock)>1) {
				echo "acl urlblock";
				$acls = array_merge($acls_urlblock,$acls_time);
				$var3[acl_list]=implode(" ",$acls);
				$var3[action]="deny";
				$this->out_aclgroups[]=$var3;
			}
		// IP
			if (count($acls_ipblock)>1) {
				echo "acl ipblock";
				$acls = array_merge($acls_ipblock,$acls_time);
				$var3[acl_list]=implode(" ",$acls);
				$var3[action]="deny";
				$this->out_aclgroups[]=$var3;
			}
	
			
		}
		
		function processacltemplate($acltemplate,$var) {
			$acls = xml::normalizeseq($acltemplate[acls][acl]);
			$files0= xml::normalizeseq($acltemplate[files][file]);
			
			foreach ($files0 as $file) {
				$files[$file[name]]=$file[content];
				$filename = conv::randkey(6).".txt";
				file_put_contents(DIRCONF."/proxy/".$filename,trim($file[content]));
				$files[$file[name]]=$filename;
			}
			
			foreach ($acls as $acl) {
				if (!($acl[block]||$acl[unblock]||$acl[ipblock]||$acl[ipunblock])) {
					continue;
				}
				foreach ($acl as $rule_key=>$rule_value) {
					
					foreach ($files as $file_key=>$file_name) {
						$count=0;
						$acl[$rule_key] = str_replace("{".$file_key."}",$file_name,$rule_value,$count);
						if ($count>0) { break; }
					}
				}
				$ret[]=$acl;
			}
			
			return $ret;
			
		}
		
		/**
		 * reload
		 * nao precisa instanciar
		 * chama o script q recarrega o proxy
		 *
		 */
		function reload () {
			$cmd = "sh ".NEXUS."/core/bin/scripts/exec.sh /bin/bash /etc/squid3/squid.reload";
			$ret = shell_exec(html_entity_decode($cmd))."\n";
		}
		
		function merge() {
			// definicoes:
			$def_names = $this->def_names;
			$def_values= $this->def_values;
			
			if (!$this->conf[proxy]) { return; }
	
			for ($i=0;$i<count($def_names);$i++) {
				if (!$this->conf[proxy]) {
					$this->conf[proxy][$def_names[$i]] = $def_values[$i];		
				} else {
					if (intval($this->conf[proxy][$def_names[$i]])==0) {
						$this->conf[proxy][$def_names[$i]] = $def_values[$i];
					}
				}
			}
	
			$var[memcachesize] 		= $this->conf[proxy][memcache];
			$var[cachesize]			= $this->conf[proxy][cache];
			$var[objectsize]		= $this->conf[proxy]["object"];
			
	
			foreach ($this->plans as $plan) {
				$group_cnf .= $this->merge_plan($plan)."\n";
			}
			
				
			$var[group]=$group_cnf; //merge_plan nao retorna nada, entao group nao existe
			$var[acls]=$this->out_acls;
			$var[httpaccess]=$this->out_aclgroups;
			
			$internal_network 	= $this->obj->get("`NETWORK.INTERFACE.INTERNAL`");
			$externals			= $this->obj->getinterfaces("external");
			
			//tamanho da particao precisa ser maior q o tamanho do cache
			$var[cachesize2]=intval($var[cachesize]) * 1.15;
			$var[cachesize2]=ceil($var[cachesize2]);
					
			$i=0;
			foreach ($externals as $external) {
				$var[int]=$external[device];
				$var[ints][$i][gw]=$this->obj->get("`HOST.GATEWAY.".$var[int]."`");
				$var[ints][$i][intnum]=substr($var[int],3);
				$var[ints][$i][int]=$external[device];
				$var[ints][$i][intnum]=substr($var[int],3);
				$i++;
				$var[intnum]=substr($var[int],3);
				$this->config_out[$external[device]] = conv::tplreplace($this->proxytpl[config],$var);
	
				$this->cache_out[$external[device]] = conv::tplreplace($this->proxytpl[cache],$var);
			}
			
			//$this->config_out = conv::tplreplace($this->proxytpl[config],$var);
		
			$internalnetwork = array_pop($internal_network);
			
			$net = Net_ipv4::parseAddress($internalnetwork);
			$bitmask=$net->bitmask;
			$network=$net->network;
			
			$var[internalnetwork] = $network."/".$bitmask;
			
			$this->internal_out = conv::tplreplace($this->proxytpl[internal],$var);
			
			$this->init_out = conv::tplreplace($this->proxytpl[init],$var);
			$this->init_out = str_replace("\$NEXUS",NEXUS,$this->init_out);
			
			$this->reload_out = conv::tplreplace($this->proxytpl[reload],$var);
			
			
			$this->fileout();
			
			// caminho para os erros
			if ($this->conf[info][lang]=="pt_BR") {
				$squidlang = "Portuguese";
			} else {
				$squidlang = "English";
			}
			shell_exec("rm -fr /etc/squid3/errors");
			shell_exec("ln -s /usr/share/squid3/errors/$squidlang /etc/squid3/errors");
			
			
			return "OK";
		}
	}
	
	
} else {
	
	if (defined("INCORE")) {
		@unlink(DIRTPL."/proxy.xml.tpl");
		@exec("ln -s ".DIRTPL."/proxyoops.xml.tpl ".DIRTPL."/proxy.xml.tpl");
	}
	
	class Proxy {
		
		
		// OOOOOOOOOOOOOOOOOOOOOOOOOOOOOPS
		
		var $proxytpl;
		var $conf;
		var $confobj;
		var $obj;
		var $out;
		var $newproxy=false;
		
		var $config_out;
		var $internal_out;
		var $init_out;
		var $cache_out;
		
		var $def_names;
		var $def_values;
		
		var $ret;
		
		function openproxyxml () {
			if ($this->conf) { return; }
			$this->conf = $this->confobj->conf;
			
		}
		function openproxytpl () {
			$this->proxytpl = xml::loadxml(DIRTPL."/proxyoops.xml.tpl");
		}
		
		function writeproxyxml () {
			$this->confobj->conf = $this->conf;
			$conf->write();
		}
		
		function __construct () {
			include_once "common.nx";
			
			$this->confobj = new Conf("proxy");
			
			$this->openproxyxml();
			$this->openproxytpl();
			
			$this->def_names	=array("memcache","cache","object");
			$this->def_values	=array(32,200,5);
		}
		
		function fileout() {
			file_put_contents(DIROUT."/proxy/ips_internal",$this->internal_out);
			file_put_contents(DIROUT."/proxy/oops.init",$this->init_out);
			
			foreach ($this->config_out as $int => $config)
				file_put_contents(DIROUT."/proxy/oops-$int.cfg",$config."\n");
			
		}
		
		
		function install () {
			$install_cmd = explode("\n",$this->proxytpl[install]);
			
			foreach ($install_cmd as $cmd) {
				$cmd = str_replace("\$NEXUS",NEXUS,trim($cmd));
				$ret .= "Executando $cmd\n";
				$ret .= shell_exec(html_entity_decode($cmd))."\n";
			}
			$this->ret = $ret;
			return file_exists("/etc/oops/");
		}
		
		function clean() {
			$install_cmd = explode("\n",$this->proxytpl[clean]);
			
			foreach ($install_cmd as $cmd) {
				$cmd = str_replace("\$NEXUS",NEXUS,trim($cmd));
				$ret .= "Executando $cmd\n";
				$ret .= shell_exec(html_entity_decode($cmd))."\n";
			}
			
			return $ret;
			
		}
		
		function reload () {
			return;
		}
		
		function merge() {
			// definicoes:
			$def_names = $this->def_names;
			$def_values= $this->def_values;
			
			if (!$this->conf[proxy]) { return; }
			
			if ($this->conf[proxy]["new"]==1) 		{ return "OK"; }
			if ($this->conf[proxy]["disabled"]==1) 	{ return "OK"; }
			
			for ($i=0;$i<count($def_names);$i++) {
				if (!$this->conf[proxy]) {
					$this->conf[proxy][$def_names[$i]] = $def_values[$i];		
				} else {
					if (intval($this->conf[proxy][$def_names[$i]])==0) {
						$this->conf[proxy][$def_names[$i]] = $def_values[$i];
					}
				}
			}
	
			$var[memcachesize] 		= $this->conf[proxy][memcache];
			$var[cachesize]			= $this->conf[proxy][cache];
			$var[objectsize]		= $this->conf[proxy]["object"];
			
			$obj = new Object();
			
			$internal_network 	= $obj->get("`NETWORK.INTERFACE.INTERNAL`");
			$externals			= $obj->getinterfaces("external");
			
			$i=0;
			foreach ($externals as $external) {
				$var[int]=$external[device];
				$var[ints][$i][gw]=$obj->get("`HOST.GATEWAY.".$var[int]."`");
				$var[ints][$i][intnum]=substr($var[int],3);
				$i++;
				$var[intnum]=substr($var[int],3);
				$this->config_out[$external[device]] = conv::tplreplace($this->proxytpl[config],$var);
			}
		
			$internalnetwork = array_pop($internal_network);
			
			$net = Net_ipv4::parseAddress($internalnetwork);
			$bitmask=$net->bitmask;
			$network=$net->network;
		
			$var[internalnetwork] = $network."/".$bitmask;
			
			$this->internal_out = conv::tplreplace($this->proxytpl[internal],$var);
			
			$this->init_out = conv::tplreplace($this->proxytpl[init],$var);
			
			$this->fileout();
			
			return "OK";
		}
	}
		
	
}
	

?>
