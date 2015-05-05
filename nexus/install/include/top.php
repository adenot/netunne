<?php
session_start();

define("PRODNAME","Netunne");
define("NEXUS","/NEXUS/nexus/");

if (file_exists("util.php")) {
	require_once("util.php");
} else {
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/util.php");
}

$lang = $_SESSION['lang'];

//$lang="pt_BR";

$terms = "terms.en.txt";

if ($lang=="pt_BR") {
	putenv("LANG=$lang");
	setlocale(LC_ALL, $lang);
	bindtextdomain("messages", NEXUS."/core/locale/"); 
	textdomain("messages");
	$terms = "terms.pt_BR.html";
} else {
	$lang="en";
}



//$step=5;

$step1=_("1. welcome");
$step2=_("2. terms");
$step3=_("3. harddisk");
$step4=_("4. settings");
$step5=_("5. install");

$varstep_a = "step".$step."_a";
$varstep= "step".$step;
$$varstep_a = $$varstep;
$$varstep="";
$varstep_b = "step".$step."_b";
$$varstep_b = "#DFDFDF";


?>

<HTML>
<HEAD>
<title><?=sprintf(_("%s Installation"),PRODNAME)?></title>
<style>
.button-right {
	background-image: url(images/button_right.png);
	border-top-width:0px;
	border-bottom-width:0px;
	border-left-width:0px;
	border-right-width:0px;
	width: 89px;
	height: 23px;
	font-family: Verdana; 
	font-size: 12px;
}
.button-left {
	background-image: url(images/button_left.png);
	border-top-width:0px;
	border-bottom-width:0px;
	border-left-width:0px;
	border-right-width:0px;
	width: 89px;
	height: 23px;
	font-family: Verdana; 
	font-size: 12px;
}
input {
	border-style:solid;
	border-width:1px;
	border-color:#CFCFCF;
}
select {
	border-style:solid;
	border-width:1px;
	border-color:#CFCFCF;
}	

.title {
	font-family: Verdana; 
	font-size: 16px;
}
.description {
	font-family: Verdana; 
	font-size: 12px;
	color: #444444;
}
.attention {
	font-family: Verdana; 
	color:red;
	font-weight:bold;
	font-size: 12px;
}
.attention1 {
	font-family: Verdana; 
	color:#FF6600;
	font-weight:bold;
	font-size: 12px;
}
.smalldescription {
	font-family: Verdana; 
	font-size: 10px;
}	
	
	
</style>
</head>
<body bgcolor="#4D6E2B">
<br>
<!-- tabela master -->
<table width=682 cellspacing=0 cellpadding=0 align=center height=549 border=0>
  <tr>
    <td colspan="4" height=42><img src="images/top.png"></td>
  </tr>
  <tr height=110>
    <td width=32><img src="images/left1.png"></td>
    <td width=227><img src="images/logo.png"></td>
    <td width=390 bgcolor="#FFFFFF">
<table border="0" width="100%" cellspacing=0 cellpadding=0 align=center >
      <tr height=62>
        <td><img src="images/installation.png"></td>
      </tr>
      <tr height=46>
        <td><table height=42 cellspacing=0 cellpadding=0 
        			align=center width="100%" border=0
        			style="font-family: Verdana; font-size: 10">
          <tr height=23>
          <? if ($step==1) { ?>
          	<td width=12><img src="images/step_left.png"></td>
          <? } else { ?>
            <td></td>
          <? } ?>
            <td bgcolor="<?=$step1_b?>" align=center><?=$step1_a?></td>
            <td width=2 bgcolor="#FFFFFF"></td>
            <td bgcolor="<?=$step2_b?>" align=center><?=$step2_a?></td>
            <td width=2 bgcolor="#FFFFFF"></td>
            <td bgcolor="<?=$step3_b?>" align=center><?=$step3_a?></td>
            <td width=2 bgcolor="#FFFFFF"></td>
            <td bgcolor="<?=$step4_b?>" align=center><?=$step4_a?></td>
            <td width=2 bgcolor="#FFFFFF"></td>
            <td bgcolor="<?=$step5_b?>" align=center><?=$step5_a?></td>
          <? if ($step==5) { ?>
          	<td width=12><img src="images/step_right.png"></td>
          <? } else { ?>
            <td></td>
          <? } ?>
          </tr>
          <tr height=23>
          <? if ($step!=1) { ?>
          	<td width=12><img src="images/step_left.png"></td>
          <? } else { ?>
            <td bgcolor="#DFDFDF"></td>
          <? } ?>
            <td bgcolor="#DFDFDF" align=center><?=$step1?></td>
            <td width=2 bgcolor="#FFFFFF"></td>
            <td bgcolor="#DFDFDF" align=center><?=$step2?></td>
            <td width=2 bgcolor="#FFFFFF"></td>
            <td bgcolor="#DFDFDF" align=center><?=$step3?></td>
            <td width=2 bgcolor="#FFFFFF"></td>
            <td bgcolor="#DFDFDF" align=center><?=$step4?></td>
            <td width=2 bgcolor="#FFFFFF"></td>
            <td bgcolor="#DFDFDF" align=center><?=$step5?></td>
          <? if ($step!=5) { ?>
          	<td width=12><img src="images/step_right.png"></td>
          <? } else { ?>
            <td bgcolor="#DFDFDF"></td>
          <? } ?>
          </tr>
        </table>
        </td>
      </tr>
      <tr height=2>
        <td bgcolor="#DFDFDF"></td>
      </tr>
    </table>
    </td>
    <td width=33><img src="images/right1.png"></td>
  </tr>
  <tr height=361>
    <td width=32><img src="images/left2.png"></td>
    <td width=617 colspan="2" bgcolor="#FFFFFF">
    	<table width=617 cellspacing=0 cellpadding=0 align=center height=361 border=0>
    		<tr height=307>
    			<td><div style="height:307px;width:617px;overflow-y:auto;overflow-x:hidden">
