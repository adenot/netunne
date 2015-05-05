<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 05/10/2006					*
	*																*
	****************************************************************/

class plan {
	
	var $conf;
	var $obj;
	var $fwtpl;
	var $rules;
	
	function __construct () {
		$this->conf = new Conf("forward");
		$this->obj = new Object();
		$this->fwtpl = $this->conf->loadtpl("forward");
		
		$this->rules = $this->getrules();
	}
	
	function getacl($aclid) {
		if (!$aclid) { return; }
		
		$acl = $this->returnacl($aclid);
	
	// SERVICO
		if ($acl[service]) {
			$acl[servicetype]="list";
			$service = explode(".",str_replace("`","",$acl[service]));
			if (strtoupper($service[1])=="GROUP") {
				$service = "GROUP.".$service[2];
			} else {
				$service = $service[1];
			}
			$acl[service]=$service;
		} else if ($acl[proto]&&$acl[dport]) {
			$acl[servicetype]="custom";
			$acl[serviceport]=$acl[dport];
			$acl[serviceproto]=$acl[proto];
		} else if ($acl[block]||$acl[unblock]||$acl[ipblock]||$acl[ipunblock]) {
			$acl[servicetype]="proxy";
		} else if ($acl[rule]) {
			$acl[servicetype]="rule";
		} else {
			$acl[servicetype]="all";
		}
		
	// DESTINATION
	
		if ($acl[dst]) {
			$acl[dsttype]="custom";
			$dst = explode("/",$this->obj->get($acl[dst]));
			//print_r($acl);
			$acl[dstip]=$dst[0];
			$acl[dstmask]=$dst[1];
		} else {
			$acl[dsttype]="all";
		}
		
	// TIME
		if ($acl[timestart]||$acl[timestop]||$acl[days]) {
			$acl[timetype]="custom";
		} else {
			$acl[timetype]="all";
		}
		//print_r($acl);
		
	// PROXY
		if ($acl[block])
			$acl[urlblock][deny]=file_get_contents(DIRCONF."/proxy/".$acl[block]);
		if ($acl[unblock])
			$acl[urlblock][allow]=file_get_contents(DIRCONF."/proxy/".$acl[unblock]);
		if ($acl[ipblock]) 
			$acl[ipblock0][deny]=file_get_contents(DIRCONF."/proxy/".$acl[ipblock]);
		if ($acl[ipunblock]) 
			$acl[ipblock0][allow]=file_get_contents(DIRCONF."/proxy/".$acl[ipunblock]);
		if ($acl[ipblock0]) 
			$acl[ipblock]=$acl[ipblock0];
		
			
		return $acl;
	
	}
	function getplans() {
		$conf = new Conf("forward");
		$plans = xml::normalizeseq($conf->conf[forward][plans][plan]);
		
		$data[]=array(_("Plan Name"),_("Description"));
		
		foreach ($plans as $plan)
			$data[]=array($plan[name],croptext($plan[description],60));
		
		return $data;
	}
	
	function getplan($name) {

		$plans = xml::normalizeseq($this->conf->get("forward/plans/plan"));
		
		
		foreach ($plans as $plan)
			if (strtoupper($plan[name])==strtoupper(urldecode($name)))
				break;
	
	
		// tambem pego os acls e gero as tabelas
		$planacls = explode(",",$plan[acls]);
		
		$firstacl = $this->returnacl($planacls[0]);
		//print_r($firstacl);
		$plan[download]=$firstacl[download];
		$plan[upload]=$firstacl[upload];
		
		
		$acllimit= $this->returnlimit($plan[acllimit]);
		$limits = explode(" ",$acllimit[traffic]);
		$plan[downlimit]=round($limits[0] / 1024 / 1024);
		$plan[uplimit]=round($limits[1] / 1024 / 1024);
		
		
		$plan[dropdata][]=array("Service","Time","Destination");
		$plan[banddata][]=array("Service","Time","Max Speed");
		
		// o primeiro eh sempre o acl de banda maxima
		for ($i=1;$i<count($planacls);$i++) {
			$acl = $this->returnacl($planacls[$i]);
			if (!$acl) { continue; }
			//print_r($acl);
			$this->tservice($acl);
			$id = "<!--".$planacls[$i]."-->";
			if (array_key_exists("drop",$acl)||array_key_exists("rule",$acl)) {
				$plan[dropdata][]=array($this->tservice($acl).$id,$this->ttime($acl),$this->tdst($acl));
			} else {
				$plan[banddata][]=array($this->tservice($acl).$id,$this->ttime($acl),$this->tspeed($acl));
			}
		}
		
	
		return $plan;
	}
	
	function getrules () {
		if ($this->rules) { return $this->rules; }
		
		//print_r($this->fwtpl);
		
		$aclstemplate = xml::normalizeseq($this->fwtpl[aclstemplate][template]);

		for ($i=0;$i<count($aclstemplate);$i++) {
			$ret[$aclstemplate[$i][id]]=$aclstemplate[$i][name];
		}
		return $ret;
	}
	
	function returnacl ($id) {
		$acls = xml::normalizeseq($this->conf->get("forward/acls/acl"));
		
		foreach ($acls as $acl) {
			if ($acl[id]==$id) {
				return $acl;
			}
		}
	}
	
	function returnlimit ($id) {
		$limits = xml::normalizeseq($this->conf->get("forward/limits/limit"));
		
		foreach ($limits as $limit) {
			if ($limit[id]==$id) {
				return $limit;
			}
		}
	}
	
	function tservice ($acl) {
		//$fw = new Forward();
		//print_r($fw->returnservice($acl));
		
		if ($acl[sitelistname]) {
			return _("Sites");
		}
		
		if ($acl[service]) {
			$service = explode(".",str_replace("`","",$acl[service]));
			if ($service[1]=="GROUP") {
				$service = sprintf(_("%s (group)"),$service[2]);
			} else {
				$service = $service[1]; 
			}
			return sprintf(_("%s"),$service);
		} 
		if ($acl[proto]) {
			return sprintf(_("%s/%s"),$acl[dport],$acl[proto]);
		}
		return sprintf(_("all"));
	}
	function ttime ($acl) {
		if ($acl[timestart]||$acl[days]) {
			return sprintf(_("%s-%s %s"),$acl[timestart],$acl[timestop],$acl[days]);
		}
		return _("Always");	
	}
	
	function tspeed($acl) {
		return sprintf(_("%s Kbps / %s Kbps"),$acl[download],$acl[upload]);
	}
	function tdst($acl) {
		if ($acl[rule]) {
			return sprintf(_("Pre-defined Rule: %s"),$this->rules[$acl[rule]]);
		}
		if ($acl[sitelistname]) {
			return sprintf(_("List: %s"),$acl[sitelistname]);
		}
		if ($acl[dst]) {
			$dst = explode("/",$this->obj->get($acl[dst]));
			$dst = $dst[0];
		} else {
			$dst = _("All destinations");
		}
		if ($acl[src]) {
			$src = explode("/",$this->obj->get($acl[src]));
			$src = $src[0];
		} else {
			$src = "-";
		}
		return $dst;
	}
	function getservices() {
		//$ret[all]=_("All services");
		foreach ($this->obj->get("`SERVICE`") as $serv=>$v)
			$ret[$serv]=$serv;
		unset($ret["_num"]);
		
		foreach ($this->obj->get("`SERVICE.GROUP`") as $serv=>$v)
			$ret["GROUP.".$serv]=sprintf(_("group: %s"),$serv);
		unset($ret["GROUP._num"]);	
		
		
		//print_r($ret);
		//$ret[custom]=_("Custom...");
		return $ret;
	}
		
		
	
}

?>
