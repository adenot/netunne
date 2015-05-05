<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 14/03/2007					*
	*																*
	****************************************************************/


class user {
	
	var $conf;
	var $obj;
	
	var $users;
	var $plans;
	
	var $online;
	
	function __construct () {
		$this->conf = new Conf("forward");
		$this->obj = new Object();
		
		$this->users = xml::normalizeseq($this->conf->get("forward/users/user"));
		$this->plans = xml::normalizeseq($this->conf->get("forward/plans/plan"));
		$this->guests = xml::normalizeseq($this->conf->get("forward/guests/guest"));
		
		$this->getonline();
	}	
	
	function getusertable () {
		
		for($i = 0; $i < count($this->plans); $i++) {
			$plan[$this->plans[$i]['id']] = $this->plans[$i]['name'];
		}
	
		$data[]=array("",_("Login"),_("Details"),_("IP"),_("Plan"));
	
		for($i = 0; $i < count($this->users); $i++){
			$status=$this->getstatus($this->users[$i]['login']);
			
			if (is_array($status)) {
				$status_msg = sprintf(_("User is online now since<BR> %s<BR>on link %s"),conv::formatdate($status[time]),$status["int"]);
				$status_icon="<img src='".DIRIMG."user_online.gif' onmouseover=\"tooltip_show('$status_msg');\" onmouseout='tooltip_hide();'>";
			} else {
				$status_icon="";
			}
			if ($this->users[$i][disabled]>0) 
				$this->users[$i][login]="<s>".$this->users[$i][login]."</s>";
			
			$data[] = array($status_icon,$this->users[$i]['login'],$this->users[$i]['details'],$this->users[$i]['ip'],$plan[$this->users[$i]['plan']]);
		}
		
		return $data;
		
		
	}
	
	function getguesttable () {
		
		$data[]=array("",_("Key"),_("Description"),_("Total"),_("Used"),_("Expiration"));
	
		foreach ($this->guests as $guest) {
			$status=$this->getstatus("guest.".$guest["key"]);
			if (is_array($status)) {
				$status_msg = sprintf(_("User is online now since %s on link %s"),conv::formatdate($status[time]),$status[int]);
				$status_icon="<img src='".DIRIMG."user_online.gif' onmouseover=\"tooltip_show('$status_msg');\" onmouseout='tooltip_hide();'>";
			} else {
				$status_icon="";
			}
			
			if ($guest[timelimit]==""||$guest[timelimit]==0||!$guest[timelimit]) {
				$guestlimit=_("Unlimited");
			} else {
				$guestlimit = $guest[timelimit] / 60;
			}
			if ($guest[expire]!="") {
				$expire = conv::formatdate($guest[expire]);
			} else {
				$expire = _("No expiration");
			}
			
			$guesttotal = round(forward::guesttotal($guest[key]) / 60);
			
			$status=_("valid");
			if ($guest[expire]!=0&&$guest[expire]<time()) {
				$status="";
				$expire = "<span style='color:red;'>".$expire."</span>";
			}
			if (is_int($guestlimit)) {
				if ($guesttotal!=0&&$guesttotal>=$guestlimit) {
					$status="";
					$guesttotal = "<span style='color:red;'>".$guesttotal."</span>";
				} 
				$guestlimit = $guestlimit." min";
			}
			if ($status=="")
				$status = "<span style='color:red;'>"._("expired")."</span>";
				
	
			$data[] = array($status_icon,$guest[key],$guest[description],$guestlimit,$guesttotal." min",$expire);

			
		}
		
		return $data;
		
	}
	
	function getstatus($login) {
		if (!$this->online) { return 0; }
		foreach ($this->online as $user) {
			if ($user[login]==$login) {
				return $user;
			}
			
		}
		return 0;
		
	}
	
	
	function getonline() {
		$linktable = @parse_ini_file(DIRTMP."nx_linktable",1);

		if (!$linktable[weight]) { $this->online=array(); return; }
		foreach ($linktable[weight] as $int => $weight) {
			if (!$linktable[$int]) { continue; }
			foreach ($linktable[$int] as $useron => $timelogin) {
				$tmp[time]=trim($timelogin);
				$tmp[login]=trim($useron);
				$tmp[int]=trim($int);
				
				$users[] = $tmp;
				unset($tmp);
			}
		}
		$this->online = $users;
	}
}

?>
