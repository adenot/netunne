<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 26/08/2007					*
	*																*
	****************************************************************/

	require_once ("../common.php");
	
	$page = new Page(_("System Graphs"));
	$page->open();

	$login = $_GET[login];
	
	if (conv::startwith("eth",$login)) {
		$refer="/network/index.php";
		$query="if=$login";
	}
	
	
	$b = new Frameback($refer);
	$b->draw();
	
	$in = new Framebutton();
	$in->title=sprintf(_("Realtime graph for %s (Firefox required)"),$login);
	$in->draw();
	
	
?>
<embed src="/_engine/graph_if.svg?<?=$query?>"
		width="582" height="300" type="image/svg+xml"
	/>
<?php
	$page->close();
?>