<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 21/02/2006					*
	*																*
	****************************************************************/

function out_ok() {
	return "OK ";
}
function out_fail() {
	return "FAIL ";
}
function out_invalidlicense() {
	return message::generate_function("SERVERERROR","INVALIDLICENSE");
}

?>
