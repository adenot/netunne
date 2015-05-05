<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/

//echo base64_decode("aHR0cDovL3d3dy5vcmt1dC5jb20v");
//exit();
phpinfo();
exit();
//print_r(parse_ini_file("/NEXUS/nexus/interface/conf/settings.ini",1));
//exit();

require_once ("../common.php");


/*
 * 
 * ((\d|[1-9]\d|1\d\d|2[0-4][0-9]|25[0-5])\.){3}(\d|[1-9]\d|1\d\d|2[0-4][0-9]|25[0-5])
 * 
 */

$page = new Page("Teste de Pagina");
$page->open();

$frame = new Frame ("testframe");
$frame->title=_("New Frame");
//$frame->startminimized=1;

$form = new Form ("testform");
$form->action="test";

$form->itype="textbox";
$form->iname="firstname";
$form->ilabel=_("Name");
$form->ihelp=_("Enter name");
$form->ivalue="Allan";
$form->nextitem();

$form->itype="list";
$form->iname="uf";
$form->ilabel=_("Estado");
$form->ihelp=_("Enter state");
$form->ivalue="RJ";
$form->ivalues["RJ"]="Rio de Janeiro";
$form->ivalues["SP"]="São Paulo";
$form->nextitem();

$form->itype="multilist";
$form->iname="comidas";
$form->ilabel=_("Pratos Preferidos");
$form->ivalues["macarrao"]=_("Macarronada");
$form->ivalues["hamburger"]=_("Biggy Joe&Leos");
$form->ivalues["salada"]=_("Saladinha Campeã");
$form->ivalue="hamburger";
$form->ihelp=_("Selecione seus pratos preferidos");
$form->nextitem();

$frame->draw($form);


$frame2 = new Frame ("test2");
$frame2->title=_("New Frame 2");

$table = new Table ();
$data[]=array(_("Username"),_("Plan"),_("IP"),_("Info"),_("Network"));

$data[]=array("spike","basic01","192.168.100.41",croptext("Allan Denot;Console",7),"Internal 1");
$data[]=array("denot","basic01","192.168.100.42","","Internal 0");
$data[]=array("bart","advanced01","192.168.100.44","","Internal 0");
$data[]=array("eclipse","premium01","192.168.100.46","","Internal 0");
$data[]=array("mariola","basic01","192.168.100.48","","Internal 1");

$data[]=array("pacocao","basic01","192.168.100.49","","Internal 1");
$data[]=array("homer","basic01","192.168.100.40","","Internal 1");
$data[]=array("spike","basic01","192.168.100.41",croptext("Allan Denot;Console",7),"Internal 1");
$data[]=array("denot","basic01","192.168.100.42","","Internal 0");
$data[]=array("bart","advanced01","192.168.100.44","","Internal 0");

$data[]=array("eclipse","premium01","192.168.100.46","","Internal 0");
$data[]=array("mariola","basic01","192.168.100.48","","Internal 1");
$data[]=array("pacocao","basic01","192.168.100.49","","Internal 1");
$data[]=array("homer","basic01","192.168.100.40","","Internal 1");
$data[]=array("spike","basic01","192.168.100.41",croptext("Allan Denot;Console",7),"Internal 1");

$data[]=array("denot","basic01","192.168.100.42","","Internal 0");
$data[]=array("bart","advanced01","192.168.100.44","","Internal 0");
$data[]=array("eclipse","premium01","192.168.100.46","","Internal 0");
$data[]=array("mariola","basic01","192.168.100.48","","Internal 1");
$data[]=array("pacocao","basic01","192.168.100.49","","Internal 1");

$data[]=array("eclipse","premium01","192.168.100.46","","Internal 0");
$data[]=array("mariola","basic01","192.168.100.48","","Internal 1");

/*
unset($data);
$data[]=array(_("Username"),_("Plan"));
$data[]=array("spike","basic01");
$data[]=array("spike","basic01");
$data[]=array("spike","basic01");
$data[]=array("spike","basic01");
$data[]=array("spike","basic01");
*/
$table->data = $data;
$table->orderby = "ASC 0";
//$table->perpage = 4;

$table->size[0]=40;
$table->size[1]=60;
$table->size[2]=20;
$table->size[3]=20;
$table->size[4]=20;



$table->action="table.test";
$actions[]=array("edit",_("Edit"),"act_edit");
//$actions[]=array("edit",_("Edit"),"act_edit");

$actions[]=array("remove",_("Remove"),"act_remove");

$table->actions=$actions;

$frame2->draw($table);

//$frame2->open();

//$frame2->close();
?>
<script language="JavaScript">


function oi() {
	aaa.value=document.getElementById('multilist_hidden_comidas').value;
	var a1=multilist_names_comidas.join(";");
	alert(a1);
	alert(multilist_values_comidas.join(";"));
	
//document.getElementById('frame_status').style.display='none';
}
</script>
  <input type="button" name="name" onclick="oi();" value="value"/>
  <input type="text" id="aaa" name="aaa">
  <textarea id="txt" rows=10></textarea>

<?php
$page->close();

?>
