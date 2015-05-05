<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Dez 21, 2006					*
	*																*
	****************************************************************/

include "../common.php";

$myid = $_POST[id];

$url_online = $_SERVER["PHP_SELF"]."?act=getmenu&type=customer_online";
$url_offline= $_SERVER["PHP_SELF"]."?act=getmenu&type=customer_offline";
$url_conline =$_SERVER["PHP_SELF"]."?act=getmenu&type=credit_online";
$url_coffline=$_SERVER["PHP_SELF"]."?act=getmenu&type=credit_offline";



header('Content-Type: text/html; charset=ISO-8859-1');


switch ($_GET[act]) {
	
	case "getmenu":
		$nxonline = @parse_ini_file(DIRTMP."/nx_user.online");
		foreach ($nxonline as $key=>$value)
			$onlinelist[]=$key;
	
		if ($_GET[type]=="customer_online") {
			$online=$onlinelist;
		} else if ($_GET[type]=="customer_offline") {
			$conf = new Conf("forward");
			$users = xml::normalizeseq($conf->get("forward/users/user"));

		//print_r($users);

			foreach ($users as $user) {
				if (in_array(trim($user[login]),$onlinelist)) { continue; }
				$offline[]=$user[login];
			}
		
		}
		
			
	
		include DIRHTML."/html_explorer_menu.php";
		break;
	case "getcontent":
		/* DADOS OBTIDOS:
		 * - numero de conexoes
		 * - se est‡ online ou nao
		 * - tempo idle
		 * - quando se conectou
		 * - ultima velocidade medida (?)
		 */
	
		$user = $_GET[user];
	
		$nxonline = @parse_ini_file(DIRTMP."/nx_user.online");
		if (array_key_exists($user,$nxonline))
			$status = "online";
		else 
			$status = "offline";
		
		$conn_num = intval(shell_exec("cat /proc/net/ip_conntrack|grep $ip|wc -l"));
	
		
	
	break;
		
}

?>
