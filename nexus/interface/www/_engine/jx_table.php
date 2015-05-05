<?php
/****************************************************************
*																*
* 			Console Tecnologia da Informação Ltda				*
* 				E-mail: contato@console.com.br					*
* 				Arquivo Criado em 12/06/2006					*
*																*
****************************************************************/

include "../common.php";

$name 		  = $_POST["name"];
$order 		  = $_POST["order"];
$page 		  = $_POST["page"];
$search_value = trim($_POST["search_value"]);
$search_field = trim($_POST["search_field"]);
$page 		  = $_POST["page"];

$data 		= $_SESSION["table_$name"][data];
$perpage 	= $_SESSION["table_$name"][perpage];
$multiactions = $_SESSION["table_$name"][multiactions];
$actions 	= $_SESSION["table_$name"][actions];
$size 		= $_SESSION["table_$name"][size];
$nocheck 	= $_SESSION["table_$name"][nocheck];
$linkid 	= $_SESSION["table_$name"][linkid];

$startat = ($page-1) * $perpage;

# if search field is not empty
if(!empty($search_value)){
	
	# title
	$newData[0]=$data[0];
	
	# loop of data
	for($i = 1; $i < count($data); $i++){
		
		# search for value in data[$i]
		if(strstr($data[$i][$search_field],$search_value) != ''){
					
			$newData[] = $data[$i];
			
		}
			
	}
	
	$data = $newData;
	
}

# Page 1 
if ($page==0) {
	$perpage = count($data)-1;
	$page=1;
	$startat=0;
}

if ((count($data)-1)<$perpage) {
	$perpage=count($data)-1;
}


function data_order ($data,$order) {

	ob_start();
	print_r($data);
	file_put_contents("/tmp/saida4",ob_get_contents());
	ob_end_clean();

	$order=explode(" ",$order);

	if (count($order)==2) {
		//$data2 = $data;
		$data0 = array_shift($data);
		$data = reorganize($data,trim($order[1]),trim($order[0]));
		array_unshift($data,$data0);
	}
	return $data;

}

function data_page ($data,$page,$perpage,$search_value='', $search_field='') {
	$newdata=array();
	$newdata[0]=$data[0];

	for ($i=($page*$perpage)-$perpage+1; $i<=($page*$perpage);	$i++) {
		
		if (!empty($data[$i]))
				$newdata[]=$data[$i];
			else
				$newdata[]="EMPTY";
		
	}
	return $newdata;
}

# total of data
$totPag = ceil(count($data) / $perpage);

$data = data_order($data,$order);
$data = data_page($data,$page,$perpage,$search_value,$search_field);



header('Content-Type: text/html; charset=ISO-8859-1');


echo $data;
echo "#####";
echo $totPag;
echo "#####";
echo $name;
echo "#####";

include DIRHTML."html_table_content.php";


?>
