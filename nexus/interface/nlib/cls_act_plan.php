<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 05/10/2006					*
	*																*
	****************************************************************/

	/*
	 * Esse codigo nao tah bom nao!
	 * fiz assim pq tou com pressa
	 * 
	 * o q pode ser melhorado:
	 * - funcao pra achar ID de plano, acl, limit
	 * - funcao pra adicionar/editar/remover acl, plano, limit
	 */

class act_plan {
	var $conf;
	var $actident;
	var $obj;
	
	function __construct($actident) {
		$this->conf = new Conf("forward");
		$this->obj = new Object();
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}
	
	function processplan($input) {	
		$plans = 	xml::normalizeseq($this->conf->conf[forward][plans][plan]);
		$acls = 	xml::normalizeseq($this->conf->conf[forward][acls][acl]);
		$limits = 	xml::normalizeseq($this->conf->conf[forward][limits][limit]);
		
		$total = count($plans);
		
		if (trim($input[name])=="") {
			return "NULLNAME";
		}
		$input[name] = str_replace(" ","_",trim($input[name]));
		
		if (!eregi("^[a-zA-Z0-9_-]+$",$input[name])) {
			return "INVALIDNAME";
		}
		
		$up = trim($input[upload]);
		$dw = trim($input[download]);
		if ($dw!=0 && $dw!="" && !eregi("^[0-9]+$",$dw)) {
			return "INVALIDSPEED";
		}
		if ($up!=0 && $up!="" && !eregi("^[0-9]+$",$up)) {
			return "INVALIDSPEED";
		}
		if (array_key_exists("upload",$input)) {
			if (intval($up)==0 || intval($dw)==0) {
				return "INVALIDSPEED";
			}
		}
		
		if ($input[editid]) {
			$editid=$input[editid];
			for ($i=0;$i<count($plans);$i++) {
				if (strtoupper($plans[$i][name])==strtoupper($input[editid]))
					break;
			}
			$editid=$i;
			
			$firstacl = explode(",",$plans[$editid][acls]);
			$firstacl = $firstacl[0];
			
			for ($i=0;$i<count($acls);$i++) {
				if ($acls[$i][id]==$firstacl)
					break;
			}
			$aclid=$i;
			
			$planlimit = $plans[$editid][acllimit];
			
			for ($i=0;$i<count($limits);$i++) {
				if ($limits[$i][id]==$planlimit) 
					break;
			}
			$limitid=$i;
			
			
			
		} else {
			$editid=$total;
			$newacl = $this->getfreeaclid();
			$plans[$editid][acls]=$newacl;
			
			$newlimit = $this->getfreelimitid();
			$plans[$editid][acllimit]=$newlimit;
			
			$limitid=count($limits);
			$limits[$limitid][id]=$newlimit;

			$plans[$editid][id]=$this->getfreeplanid();
			
			$aclid = count($acls);
			
			$acls[$aclid][id]=$newacl;
			
			$input[download]=0;
			$input[upload]=0;
			
			$input[downlimit]=0;
			$input[uplimit]=0;
			
		}
		if ($input[pppoe]==1) {
			$input[fixmac]=0;
		}
		
		
		$plans[$editid][name]		= $input[name];
		$plans[$editid][pppoe]		= $input[pppoe];
		$plans[$editid][forceauth]	= $input[forceauth];
		$plans[$editid][description]= $input[description];
		$plans[$editid][link]		= $input[link];
		$plans[$editid][linkfail]	= $input[linkfail];
		$plans[$editid][proxy]		= $input[proxy];
		$plans[$editid][fixmac]		= $input[fixmac];

		$acls[$aclid][download]		= intval($input[download]);
		$acls[$aclid][upload]		= intval($input[upload]);


		$input[downlimit] 			= intval($input[downlimit]) * 1024 * 1024;
		$input[uplimit] 			= intval($input[uplimit]) * 1024 * 1024;
		
		$limits[$limitid][traffic]	= $input[downlimit]." ".$input[uplimit];
		$limits[$limitid][action]	= "drop";


		$plans = conv::arrayclean($plans);
		$plans["_num"]=count($plans);
		$this->conf->conf[forward][plans][plan]=$plans;

		$acls["_num"]=count($acls);
		$this->conf->conf[forward][acls][acl]=$acls;

		$limits["_num"]=count($limits);
		$this->conf->conf[forward][limits][limit]=$limits;

		if (!$this->conf->write()) {
			record::wall($this->actident);
		}	
		
		if ($input[editid]) {
			record::act_log(_("Plan Edited"));
		} else {
			record::act_log(_("Plan Added"));
		}
		return $input[name];
	}
	
	function processacl ($input) {
		$acls = xml::normalizeseq($this->conf->conf[forward][acls][acl]);
		
		if ($input[editid]) {
			for ($i=0;$i<count($acls);$i++) {
				if ($acls[$i][id]==$input[editid]) {
					$edit=$i;
					break;
				}
			}
		}
		
		
		// SERVICE
		if ($input[servicetype]=="list") {
			$acl[service]="`SERVICE.".$input[service]."`";
		} else if ($input[servicetype]=="custom") {
			$input[serviceport]=intval($input[serviceport]);
			if ($input[serviceport]==0)
				return "INVALIDDPORT";

			if ($input[serviceport]<1 || $input[serviceport]>65535)
				return "INVALIDDPORT";

				
			$acl[dport]=$input[serviceport];
			$acl[proto]=$input[serviceproto];
		} else if ($input[servicetype]=="proxy") {
			// aqui vou receber: 
			// urlblock_allow, urlblock_deny
			// ipblock_allow, ipblock_deny
			
			
			$var[block]=$input[urlblock_deny];
			$var[unblock]=$input[urlblock_allow];
			$var[ipblock]=$input[ipblock_deny];
			$var[ipunblock]=$input[ipblock_allow];
			
			// testando se nao colocou um ip invalido
			$array_ipblock = explode("\n",trim($var[ipblock]));
			
			for ($i=0;$i<count($array_ipblock);$i++) {
				if (trim($array_ipblock[$i])=="") { continue; }
				
				$line = $i+1;
				$tmp = explode("/",$array_ipblock[$i]);
				unset($ip);unset($mask);
				$ip = trim($tmp[0]);
				$mask=trim($tmp[1]);
				
				if (!Net_ipv4::validateIP($ip)) 
					return "ERRORIPBLOCK_$line";
				
				if ($mask)
					if (!Net_ipv4::validateNetmask($mask))
						return "ERRORIPBLOCK_$line";
				
			}
			
			$array_ipunblock = explode("\n",trim($var[ipunblock]));
			
			for ($i=0;$i<count($array_ipunblock);$i++) {
				if (trim($array_ipunblock[$i])=="") { continue; }
				
				$line = $i+1;
				
				$tmp = explode("/",$array_ipunblock[$i]);
				$ip = trim($tmp[0]);
				$mask=trim($tmp[1]);
				
				if (!Net_ipv4::validateIP($ip)) 
					return "ERRORIPUNBLOCK_$line";

				if ($mask)
					if (!Net_ipv4::validateNetmask($mask))
						return "ERRORIPUNBLOCK_$line";
				
			}
			
			
			
			if (!$input[sitelistname]) {
				return "INVALIDSITELISTNAME";
			}
			
			$acl[sitelistname]=$input[sitelistname];
			 
			
			$types = array("block","unblock","ipblock","ipunblock");
			
			// primeiro limpo os antigos pra nao ficar mto lixo
			foreach ($types as $type) {
				if ($edit) 
					$acl[$type]=$acls[$edit][$type];
			
				$file = DIRSETPROXY.$acl[$type];	
				if (file_exists($file))
					unlink($file);
			
			
				if (!$acl[$type])
					$acl[$type]=conv::randkey(6).".txt";
					
				file_put_contents(DIRSETPROXY.$acl[$type],trim($var[$type]));
			}
						
			
		} else if ($input[servicetype]=="rule") {
				
			$acl[rule]=$input[rule];
			
		}
		
		// DESTINATION
		if ($input[dsttype]=="custom") {
			if (!Net_ipv4::validateIP($input[dstip]))
				return "INVALIDDST";

			$acl[dst]=$input[dstip]."/".$input[dstmask];
		}
		
		// TIME
		if ($input[timetype]=="custom") {
			$acl[timestart]=sprintf("%02d",$input[timestart_hour]).":".sprintf("%02d",$input[timestart_minute]);
			$acl[timestop]=sprintf("%02d",$input[timestop_hour]).":".sprintf("%02d",$input[timestop_minute]);
			
			$days = explode(",","Mon,Tue,Wed,Thu,Fri,Sat,Sun");
			
			$acl[days]=array();
			foreach ($input as $k=>$v)
				foreach ($days as $day)
					if ($k=="days_$day")
						$acl[days][]=$day;
			
			if (count($acl[days])==0)
				return "INVALIDDAYS";			
			
			$acl[days] = implode(",",$acl[days]);

		}
		
		// BAND/BLOCK
		
		if ($input[func]=="block") {
			$acl[drop]="";
		} else if ($input[func]=="band") {
			$input[download]=intval($input[download]);
			$input[upload]=intval($input[upload]);
			if ($input[download]==0 ||
				$input[upload]==0)
				return "INVALIDBAND";
			
			$acl[download]=$input[download];
			$acl[upload]=$input[upload];
		}
			
		//print_r($acl);

		// tenho q achar o plano pra colocar o acl no plano	
		$plans = xml::normalizeseq($this->conf->conf[forward][plans][plan]);
		for ($i=0;$i<count($plans);$i++) {
			if (strtoupper($plans[$i][name])==strtoupper($input[planname]))
				break;
		}
		$planid=$i;
		$planacls = explode(",",$plans[$planid][acls]);


		$total = count($acls);
		if ($edit) {
			$acl[id]=$input[editid];
			$acls[$edit]=$acl;
		} else {
			// NOVO
			$newid = $this->getfreeaclid();
			$acl[id]=$newid;
			
			$acls[$total]=$acl;
			$total = count($acls);

			$planacls[]=$newid;
		}
		
		
		
		$acls = conv::arrayclean($acls);
		$acls["_num"]=count($acls);
		$this->conf->conf[forward][acls][acl]=$acls;

		$planacls=implode(",",$planacls);
		

		// dependendo do tipo ele vai ter q ir pro final
		$planacls = $this->orderacl($planacls);
		//echo $planacls;

		$plans[$planid][acls]=$planacls;
		$plans["_num"]=count($plans);
		$this->conf->conf[forward][plans][plan]=$plans;




		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}	

		$this->conf->writeproxy();

		
		if ($edit) {
			record::act_log(_("Plan Rule Changed"));
		} else {
			record::act_log(_("Plan Rule Added"));
		}
		
		return true;
		
	}
	
	function removeacl ($input) {
		$list = $input[id];
		$list = explode("]n[",$list);
		$acls = xml::normalizeseq($this->conf->conf[forward][acls][acl]);
		$plans = xml::normalizeseq($this->conf->conf[forward][plans][plan]);

		$total = count($acls);
		
		for ($i=0;$i<count($list);$i++) {
			$id = explode("<!--",$list[$i]);
			$list[$i] = str_replace("-->","",$id[1]);
		}
		
		for ($i=0;$i<$total;$i++) {
			if (in_array($acls[$i][id],$list)) {
				//echo "removendo ".$users[$i][login];
				unset($acls[$i]);
			}
		}
		// tirando do plano
		for ($i=0;$i<count($plans);$i++) {
			if (strtoupper($plans[$i][name])==strtoupper($input[planname]))
				break;
		}
		$planid = $i;
		
		$pacls = $plans[$planid][acls];
		$pacls = explode(",",$pacls);
		foreach ($pacls as $k=>$v) {
			if (in_array($v,$list)) {
				unset($pacls[$k]);
			}
		}
		$plans[$planid][acls]=implode(",",$pacls);
		
	
		$acls = conv::arrayclean($acls);
		$acls["_num"]=count($acls);
		$this->conf->conf[forward][acls][acl]=$acls;

		$plans["_num"]=count($plans);
		$this->conf->conf[forward][plans][plan]=$plans;

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		record::act_log(sprintf(_("Rules from plan %s removed"),$input[planname]));

		return true;

	}
	
	function removeplan($input) {
		$list = $input[id];
		$list = explode("]n[",$list);
		
		
		
		$acls = xml::normalizeseq($this->conf->conf[forward][acls][acl]);
		$plans = xml::normalizeseq($this->conf->conf[forward][plans][plan]);
		$limits = xml::normalizeseq($this->conf->conf[forward][limits][limit]);
	
		$aclremove=array();
		$planremove=array();
		$limitremove=array();
		
		$aclremoveid=array();
		$limitremoveid=array();
		
		for ($i=0;$i<count($plans);$i++) {
			if (in_array($plans[$i][name],$list)) {
				$aclremoveid = array_merge($aclremoveid,explode(",",$plans[$i][acls]));
				$limitremoveid[] = $plans[$i][acllimit];
				
				$planremove[]=$i;
			}
		}
		
		foreach ($aclremoveid as $aclid)
			for ($i=0;$i<count($acls);$i++)
				if ($acls[$i][id]==$aclid)
					$aclremove[]=$i;

		foreach ($limitremoveid as $limitid)
			for ($i=0;$i<count($limits);$i++)
				if ($limits[$i][id]==$limitid)
					$limitremove[]=$i;

		
		foreach ($aclremove as $i)
			unset ($acls[$i]);
			
		foreach ($planremove as $i)
			unset ($plans[$i]);
		
		foreach ($limitremove as $i) 
			unset ($limits[$i]);

		$acls = conv::arrayclean($acls);
		$acls["_num"]=count($acls);
		$this->conf->conf[forward][acls][acl]=$acls;

		$plans = conv::arrayclean($plans);
		$plans["_num"]=count($plans);
		$this->conf->conf[forward][plans][plan]=$plans;

		$limits = conv::arrayclean($limits);
		$limits["_num"]=count($limits );
		$this->conf->conf[forward][limits][limit]=$limits;

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		record::act_log(sprintf(_("Plan(s): %s removed"),implode(", ",$list)));
		
		return true;

	}
	
	function getfreeaclid () {
		$acls = xml::normalizeseq($this->conf->conf[forward][acls][acl]);

		$tmp = array();
		foreach ($acls as $acl) {
			$tmp[$acl[id]]=1;
		}
		
		for ($i=1;$i<65535;$i++) {
			if (!array_key_exists($i,$tmp)) 
				return $i;
		}
	}
	function getfreeplanid () {
		$plans = xml::normalizeseq($this->conf->conf[forward][plans][plan]);

		$tmp = array();
		foreach ($plans as $plan) {
			$tmp[$plan[id]]=1;
		}
		
		for ($i=1;$i<65535;$i++) {
			if (!array_key_exists($i,$tmp)) 
				return $i;
		}
	}
	function getfreelimitid () {
		$limits = xml::normalizeseq($this->conf->conf[forward][limits][limit]);

		$tmp = array();
		foreach ($limits as $limit) {
			$tmp[$limit[id]]=1;
		}
		
		for ($i=1;$i<65535;$i++) {
			if (!array_key_exists($i,$tmp)) 
				return $i;
		}
	}
	
	function orderacl ($planacls) {
		$planacls = explode(",",$planacls);
		$acls = xml::normalizeseq($this->conf->conf[forward][acls][acl]);
		
		// se for um acl de drop, ele vai pro final
		// se for um acl de banda, ele vai pra antes dos de drop
		foreach ($acls as $k=>$v)
			if (array_key_exists("drop",$v))
				$drop[]=$v[id];
		
		// separando
		// tiro o primeiro
		$first=array();
		$first[0] = array_shift($planacls);
		
		$acldrop=array();
		$aclband=array();
		
		// separo os outros
		for ($i=0;$i<count($planacls);$i++) {
			if (in_array($planacls[$i],$drop)) {
				$acldrop[]=$planacls[$i];
			} else {
				$aclband[]=$planacls[$i];
			}
		}
		$planacls = array_merge($first,$aclband,$acldrop);
		
		return implode(",",$planacls);
	}
	
	
}

?>
