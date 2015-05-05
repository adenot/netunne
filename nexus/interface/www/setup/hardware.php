<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Fev 18, 2008					*
	*																*
	****************************************************************/

	require_once ("../common.php");

	include "fnc_server.php";

	$page = new Page (_("Hardware Info"));
	$page->open();
	
	$hwinfo = server::gethwinfo();
		

	$useinfo[$i][title]=_("CPU");
	$useinfo[$i][desc]=sprintf(_("Number of processors detected: %s\nModel: %s\nSpeed: %s MHz\nCache: %s"),$hwinfo[cpu][cpus],$hwinfo[cpu][model],$hwinfo[cpu][cpuspeed],$hwinfo[cpu][cache]);
	$i++;
	$useinfo[$i][title]=_("Memory");
	$useinfo[$i][desc]=sprintf(_("RAM Total: %s MB"),$hwinfo[mem]);
	$i++;
	
	$f = new Framelist();
	$f->title = _("Information");
	$f->open=1;
	$f->data = $useinfo;
	$f->draw();
	
	$in = new Framebutton();
	$in->title=_("Realtime CPU graph (Firefox required)");
	$in->draw();
	
?>
<embed src="/_engine/graph_cpu.svg"
		width="582" height="200" type="image/svg+xml"
		style="margin-bottom:25px;margin-top:-35px;
		"
	/>
<?php

	$in = new Framebutton();
	$in->title=_("Realtime Memory graph (Firefox required)");
	$in->draw();
	
?>
<embed src="/_engine/graph_mem.svg"
		width="582" height="200" type="image/svg+xml"
		style="margin-bottom:25px;margin-top:-35px;
		"
	/>
<?php

	$page->close();

?>
