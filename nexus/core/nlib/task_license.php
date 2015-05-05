<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 26/09/2006					*
	*																*
	****************************************************************/

$GLOBALS[CONF] = parse_ini_file('/etc/nexus/path');
define ("NEXUS",$GLOBALS[CONF]["NEXUS"]);

include_once NEXUS."/core/nlib/common.nx";

record::msg_log("Running task_licence...","account");

/* obtenho a licensa */
$a = new License();
$a->request_license();

/* testo a validade */
$checklic = new Checklicense();
$open_license = $checklic->open_license();

if ($open_license==false) {
	record::dmesg_log("Invalid license");
	//exec("echo \"Invalid license\" >> /var/log/dmesg-tmp.log");
}

record::msg_log("task_licence finish","account");

?>
