<?php
$csv=file_get_contents("cadastro.csv");

$csv=str_replace("\",\"","@XA@",$csv);
$csv=str_replace("\"","",$csv);

$csv=explode("@XA@",$csv);

function concerta_data ($data) {
	if (trim($data)=="") { return "null"; }
	$data = explode("/",$data);
	
	if (trim($data[2])=="") { return "null"; }
	if (trim($data[1])=="") { return "null"; }
	if (trim($data[0])=="") { return "null"; }
	
	if (($data[2])&&(strlen($data[2])==2))
		$data[2]="19".$data[2];
	
	$data[1]=sprintf("%02s",  $data[1]);
	$data[0]=sprintf("%02s",  $data[0]);
	return "'".$data[2]."-".$data[1]."-".$data[0]."'";
	
}

function parser($arq){
	$arq2 = $arq;
	$arq2 = str_replace("E-mail (*):","E-mail",$arq2);
	$arq2 = str_replace("Nome (*):","Nome Completo",$arq2);
	$arq2 = str_replace("Nome *","Nome Completo",$arq2);
	$arq2 = str_replace("E-mail *","E-mail",$arq2);
	$arq2 = str_replace("Endereço*","Endereço",$arq2);
	$arq2 = str_replace("CEP*","CEP",$arq2);
	$arq2 = str_replace("Estado*","Estado",$arq2);
	$arq2 = str_replace("Nome da Instituição*","Nome da Instituição",$arq2);


	
	$line = explode("\n",$arq2);
	echo "entrou $arq\n";
	$campos = explode(";","Nome Completo;E-mail;Data de Nascimento;Endereço;CEP;Nome da Instituição;Estado");
	for ($i=0;$i<count($line);$i++) {
		$line[$i]=trim($line[$i]);
		$tmp=explode(":",$line[$i]);
		if (in_array(trim($tmp[0]),$campos)) { 
			$array[trim($tmp[0])]=trim(str_replace("'"," ",$tmp[1]));
		}
	}
	
	foreach ($array as $k => $v) {
		foreach ($campos as $ck => $cv) {
			if ($k==$cv) {
				$arr[$ck]=$v;
			}
		}
	}
	if (count($arr)==0) { return; }
	for ($i=0;$i<count($campos);$i++)
		if (!$arr[$i]) 
			$arr[$i]="";

			
			
	ksort($arr);
	print_r($arr);
	return $arr;

}

$sql0="INSERT INTO newsletter_mail(cd_site_newsletter_mail,nome_newsletter_mail, email_newsletter_mail, ".
"nascimento_newsletter_mail,endereco_newsletter_mail,  cep_newsletter_mail, ".
"instituicao_newsletter_mail, estado_newsletter_mail) ".
"VALUES\n\t";
$sql="";
for ($i=0;$i<count($csv);$i=$i+7) {
	$corpo = $csv[$i+1];
	$res = parser($corpo);
	$sql .= "$sql0 ".
	"(53,'".$res[0]."', '".$res[1]."', ".concerta_data($res[2]).",".
	"'".$res[3]."', '".$res[4]."', '".$res[5]."', '".$res[6]."' );\n";
	
}

file_put_contents("saida.sql",$sql);

?>