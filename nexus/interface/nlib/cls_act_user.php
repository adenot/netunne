<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 24/07/2006					*
	*																*
	****************************************************************/

class act_guestconfig {
	var $conf;
	var $actident;
	
	function __construct($actident) {
		$this->conf = new Conf("forward");
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}
	
	function process($input) {
		// esperados: user/userkey
		
		if ($input[idle]) {
			$this->conf->set("forward/guestconfig/idle",$input[idle]);
		}
		$this->conf->set("forward/guestconfig/plan",$input[plan]);
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		Setting::save("guest","keysize",$input[keysize]);
		
		record::act_log(_("Credited Customer settings changed"));
		return true;
	}
	
}

class act_useredit {
	
	var $actident; 
	var $conf;

	function __construct($actident=0) {
		$this->conf = new Conf("forward");
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}
	
	function export_csv() {
		$users = xml::normalizeseq($this->conf->conf[forward][users][user]);
		$plans = xml::normalizeseq($this->conf->conf[forward][plans][plan]);
		foreach ($plans as $plan)
			$plannames[$plan[id]]=$plan[name];

		// login, details, nome do plano, ip, macs, pass, status
		$csv=array();
		
		$tmp[]=_("Login");
		$tmp[]=_("Details");
		$tmp[]=_("Plan Name");
		$tmp[]=_("IP");
		$tmp[]=_("MAC");
		$tmp[]=_("Password");
		$tmp[]=_("Status");
		$csv[]=implode(";",$tmp);unset($tmp);
		
		foreach ($users as $user) {
			$tmp[]=$user[login];
			$tmp[]=$user[details];
			$tmp[]=$plannames[$user[plan]];
			$tmp[]=$user[ip];
			$tmp[]=$user[macs];
			$tmp[]=$user[pass];
			$tmp[]=strval(intval($user[disabled]));
			$csv[]="\"".implode("\";\"",$tmp)."\"";unset($tmp);
		}
		$csvtxt = implode("\n",$csv);
		$csvfile= DIRTMP."nx_user_csv.".conv::randkey(4);
		file_put_contents($csvfile,$csvtxt);
		return $csvfile;
	}
	
	function disconnect ($input) {
		$list = $input[id];
		$list = explode("]n[",$list);
		$list_to_disconnect=implode(",",$list);
		
		$this->disable($list_to_disconnect);
		
		$conn = new Conn();
		$conn->command(message::generate_function("DISCONNECT",$list_to_disconnect),$this->actident);
		
		return true;
	}
	
	function remove ($input) {		
		$list = $input[id];
		$list = explode("]n[",$list);
		$users = xml::normalizeseq($this->conf->conf[forward][users][user]);

		$total = count($users);
		

		

		for ($i=0;$i<$total;$i++) {
			if (in_array($users[$i][login],$list)) {
				//echo "removendo ".$users[$i][login];
				//$list_to_remove[]=$users[$i][login];
				unset($users[$i]);
			}
		}
		
		
		$list_to_remove=implode(",",$list);
		
		$conn = new Conn();
		$conn->command(message::generate_function("DISCONNECT",$list_to_remove),$this->actident);
		

		$users = conv::arrayclean($users);
		$users["_num"]=count($users);
		//print_r($users);

		$this->conf->conf[forward][users][user]=$users;

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		

		
		//record::act_log(sprintf(_("Customer(s) %s removed"),implode(", ",$list)));

		return true;

	}
	
	function disable($login) {
		$list_to_disable = explode(",",$login);
		
		$users = xml::normalizeseq($this->conf->conf[forward][users][user]);
		//$plans = xml::normalizeseq($this->conf->conf[forward][plans][plan]);
		
		for ($i=0;$i<count($users);$i++) {
			if (in_array(trim($users[$i][login]),$list_to_disable)) {
				$users[$i][disabled]=1;
			}
		}

		//$user[disabled]=1;

		//$users[$intnum]=$user;
		$users["_num"]=count($users);
		$this->conf->conf[forward][users][user]=$users;

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
	}

	function enable($input) {
		$list_to_enable = $input[id];
		$list_to_enable = explode("]n[",$list_to_enable);
		
		//print_r($list_to_enable);
		
		$users = xml::normalizeseq($this->conf->conf[forward][users][user]);

		for ($i=0;$i<count($users);$i++) {
			if (in_array(trim($users[$i][login]),$list_to_enable)) {
				unset($users[$i][disabled]);
			}
		}

		$users["_num"]=count($users);
		$this->conf->conf[forward][users][user]=$users;

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
	}

	function process ($input) {
		
		$users = xml::normalizeseq($this->conf->conf[forward][users][user]);
		$plans = xml::normalizeseq($this->conf->conf[forward][plans][plan]);
		
		for ($i=0;$i<count($users);$i++) {
			if (trim($users[$i][login])==trim($input[login])) {
				$intnum = $i;
				$user = $users[$i];
				break;
			}
		}
		
		if (trim($input[macs])!="") {
			if (!eregi("^([0-9A-F]{2}:){5}[0-9A-F]{2}$",trim($input[macs]))) {
				return "INVALIDMAC";
			}
		}
		
		if (isset($intnum)&&$input[newuser]=="yes") {
			return "LOGINEXISTS";
		}
		if (trim($input[login])=="") {
			return "LOGINNULL";
		}
		if (!eregi("^[a-zA-Z0-9]+$",trim($input[login]))) {
			return "INVALIDLOGIN";
		}
		
		
		if ($input[plan]) {
			foreach ($plans as $plan)
				if ($plan[id]==$input[plan])
					break;
			if ($plan[pppoe]==1) 
				if (trim($input[ip])=="")
					return "IPREQUIRED";
		} else {
			return "PLANREQUIRED";
		}
		
		
		if (trim($input[ip])!="") {
			if (!Net_ipv4::validateIP($input[ip])) {
				return "INVALIDIP";
			}
		}
		
		
		if (!isset($intnum)) {
			$intnum = count($users);
		} 
		
		if (!$user) {
			$user[login]=$input[login];
		}
		$user[details]=$input[details];
		$user[pass]=$input[pass];
		$user[plan]=$input[plan];
		$user[msg]=$input[msg];
		$user[ip]=$input[ip];
		$user[macs]=$input[macs];
		if (intval($input[status])>=1) {
			$user[disabled]=$input[status];
		} else {
			unset($user[disabled]);
		}
		
		// padroes
		//$user[noauth]=1;

		$users[$intnum]=$user;
		$users["_num"]=count($users);
		$this->conf->conf[forward][users][user]=$users;

		ob_start();
		print_r($this->conf->conf);
		//var_dump($input);
		$tmp = ob_get_contents();
		ob_end_clean();
		file_put_contents("/tmp/saida0-".$this->actident,$tmp);	

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		if ($input[newuser]=="yes")
			record::act_log(sprintf(_("Customer %s added"),$input[login]));
		else
			record::act_log(sprintf(_("Customer %s changed"),$input[login]));
			
		return true;
	}
	
	
	
	
	
}

class act_guestedit {
	
	var $actident; 
	var $conf;

	function __construct($actident) {
		$this->conf = new Conf("forward");
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}


	function remove ($input) {
		$list = $input[id];
		$list = explode("]n[",$list);
		
		
		$guests = xml::normalizeseq($this->conf->conf[forward][guests][guest]);

		$total = count($guests);
		
		for ($i=0;$i<$total;$i++) {
			if (in_array($guests[$i][key],$list)) {
				unset($guests[$i]);
			}
		}
		$guests = conv::arrayclean($guests);
		$guests["_num"]=count($guests);
		//print_r($guests);

		$this->conf->conf[forward][guests][guest]=$guests;


		ob_start();
		//print_r($this->conf->conf);
		//var_dump($input);
		print_r($users);
		$tmp = ob_get_contents();
		ob_end_clean();
		file_put_contents("/tmp/saida0-".$this->actident,$tmp);	

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		
		//$this->conf->copyxml("forward");
		//record::act_log(sprintf(_("Prepaid Customer(s) %s removed"),implode(", ",$list)));

		return true;

	}
	


	function process ($input) {
		
		$guests = xml::normalizeseq($this->conf->conf[forward][guests][guest]);

/*
		ob_start();
		//print_r($this->conf->conf);
		var_dump($input);
		$tmp = ob_get_contents();
		ob_end_clean();
		file_put_contents("/tmp/saida1-".$this->actident,$tmp);	
*/

		for ($i=0;$i<count($guests);$i++) {
			if (trim($guests[$i][key])==trim($input[key])) {
				$intnum = $i;
				$guest = $guests[$i];
				break;
			}
		}
		if (isset($intnum)&&$input[newguest]=="yes") {
			return "KEYEXISTS";
		}
		if (trim($input["expire_txt"])==""&&trim($input[timelimit])=="") {
			return "NOTIME";
		}
		
		if (!isset($intnum)) {
			$intnum = count($guests);
		} 
		
		if (!$guest) {
			$guest[key]=$input[key];
		}
		$guest[timelimit]=$input[timelimit] * 60;
		$guest[description]=$input[description];
		$guest[plan]=$input[plan];
		
		
		if (trim($input["expire_txt"])!="")
			$guest[expire]=Form::fromnow($input["expire_txt"],$input["expire_list"]);
					
		$guests[$intnum]=$guest;
		$guests["_num"]=count($guests);
		$this->conf->conf[forward][guests][guest]=$guests;

		ob_start();
		print_r($this->conf->conf);
		//var_dump($input);
		$tmp = ob_get_contents();
		ob_end_clean();
		file_put_contents("/tmp/saida0-".$this->actident,$tmp);	

		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		/*
		if ($input[newguest]=="yes")
			record::act_log(sprintf(_("Prepaid Customer %s added"),$input[key]));
		else
			record::act_log(sprintf(_("Prepaid Customer %s changed"),$input[key]));
		*/
		//$this->conf->copyxml("forward");
		
		record::act_log(_("Credit added"));
		
		return true;
	}
}

class act_publishedit {
	var $actident; 
	var $conf;

	function __construct($actident) {
		$this->conf = new Conf("forward");
		$this->conf->ident = $actident;
		$this->actident = $actident;
	}
	function process($input) {
		$pubs = xml::normalizeseq($this->conf->conf[forward][publishs][publish]);
	
		for ($i=0;$i<count($pubs);$i++) {
			if (trim($pubs[$i][dport])==trim($input[dport])) {
				$intnum = $i;
				$pub = $pubs[$i];
				break;
			}
		}
		if (isset($intnum)&&$input[newpublish]=="yes") {
			return "DPORTEXISTS";
		}
		// dest pode ser um login ou um ip
		// primeiro testo se eh um ip, se for, OK
		// senao eu testo se o usuario existe
		if (Net_ipv4::validateIP($input[newip])) {
			$newip=$input[newip];
		}
		if (Forward::validuser($input[newip])) {
			$newip="`HOST.USER.".$input[newip]."`";
		} 
		
		if (!$newip) {
			return "INVALIDDEST";
		}
		$input[dport]==intval($input[dport]);

		if (intval($input[dport])>65535||intval($input[dport])<2) {
			return "INVALIDPORT";
		}
		
		$res_ports=array(80,3080,443);
		if (in_array($input[dport],$res_ports)) {
			return "RESERVEDPORT";
		}
		
		
	
		if (!isset($intnum)) {
			$intnum = count($pubs);
		} 
		
		if (!$pub) {
			$pub[dport]=$input[dport];
		}
		$pub[description]=	$input[description];
		$pub[newdport]=		$input[newdport];
		$pub[proto]=		$input[proto];
		$pub[newip]=		$newip;
		

		$pubs[$intnum]=$pub;
		$pubs["_num"]=count($pubs);
		$this->conf->conf[forward][publishs][publish]=$pubs;
		
		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		if ($input[newpublish]=="yes")
			record::act_log(sprintf(_("Publish Server port %s added"),$input[dport]));
		else
			record::act_log(sprintf(_("Publish Server port %s changed"),$input[dport]));
			
		return true;
	}
	


	function remove ($input) {
		$list = $input[id];
		$list = explode("]n[",$list);
		
		
		$publishs = xml::normalizeseq($this->conf->conf[forward][publishs][publish]);

		$total = count($publishs);
		
		for ($i=0;$i<$total;$i++) {
			if (in_array($publishs[$i][dport],$list)) {
				unset($publishs[$i]);
			}
		}
		$publishs = conv::arrayclean($publishs);
		$publishs["_num"]=count($publishs);
		//print_r($guests);

		$this->conf->conf[forward][publishs][publish]=$publishs;


		if (!$this->conf->write()) {
			return record::wall($this->actident);
		}
		record::act_log(sprintf(_("Published Server(s) port(s) %s removed"),implode(", ",$list)));
			
		return true;

	}
	
}

?>
