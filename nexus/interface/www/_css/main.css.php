<?php
	include "../common.php";

?>
    
    /****************** BODY ******************/
    
    select {
    	visibility: hidden;
    }
    hr {
    	/* tem tambem em html_if_userlog e html_if_log */
    	background-color: #000000;
    	border-width: 1px;
    	border-style: solid;
    	border-color: #000000;
    	height:1px;
    }
    
    html,body {
		height: 100%;
		vertical-align:top;
		padding:0;
		margin:0;
		border:0;
		background-color: #D6F1B9;
	}
    #pagetitle {
		position:absolute;
		
		height: 19px;
		width: 224px;
		left: 194px;
		top: 50px;
		background-color: #B1D38C;
		text-align: center;
		font-family: Verdana;
		font-size: 13px;
		font-weight: bold;
    }
    #btn_apply {
		position:absolute;
		
		height: 24px;
		overflow:hidden;
		/* width: 224px; */
		left: 525px;
		top: 46px;
		background-color: #B1D38C;
		text-align: center;
		font-family: Verdana;
		font-size: 13px;
		font-weight: bold;
		cursor:pointer;
    }
    
	/*
    #menu {
		position: absolute;
		top: 106px;
		left:0;
		width:153px;

    }
    #menu ul {
		list-style:none;
		width:100%;
		padding:0px;
		margin:0px;
		float: left;
    }
    #menu ul li {
  		font-family: Verdana;
		font-size: 9px;
		text-align: right;
		
		margin-bottom: 5px;
		height: 21px;
		width: 153px;
		background-color: white;
		background-image: url(<?=DIRIMG?>menu-item.jpg);
		background-repeat: no-repeat;
		overflow:hidden;
    }
    #menu a {

    	float:right;
		top: 4px;
		left: 10px;
		width: 153px;
		height: 21px;
		text-decoration: none;
		color: white;
		
		padding: 4px 35px 5px 10px;
    }
	*/

    td.menuItem {
  		font-family: Verdana;
		font-size: 9px;
		text-align: right;
		margin-bottom: 5px;
		height: 21px;
		width: 153px;
		background-image: url(<?=DIRIMG?>menu-item.gif);
		background-repeat: no-repeat;
		padding-right:35px;
		cursor:pointer;
		/* tava em '.menuItem a' */
		top: 4px;
		left: 10px;
		text-decoration: none;
		color: white;
		/*overflow:hidden;*/
    }
	.menuSubItemDisabled {
		display:none;
	}
	.menuSubItemEnabled {}
	.menuSubItem {
		font-family: Verdana;
		font-size: 9px;
		text-align: right;
		margin-bottom: 5px;
		height: 19px;
		width: 153px;
		color:#FFFFFF;
		padding-right:20px;
		cursor:pointer;
		
	}
    tr.menuItemSep {
		height: 1px;
    }
	#informacoes ul#menuItens li.fechado ul {
		display:none;
	}
	#informacoes ul#menuItens li.aberto {background:#F0F0F0 url(/imagens/fundo-historico.gif) no-repeat -30px -50px;}
    
    #body {
    	margin: 20px 0px 20px 30px;
    }
    
    #tooltip {
    	/* height: 15px;*/
    	border:1px solid black;
    	position:absolute;
    	visibility:hidden;
    	font-family: Verdana;
    	font-size:10px;
    	color:black;
    	background-color:white;
    	vertical-align:middle;
    	padding: 3px 3px 3px 3px;
    	z-index:2;
    }
    #tooltipshadow {
    	/*height: 15px;*/
    	background-color: #000000;
    	position:absolute;
    	visibility:hidden;
    	filter:alpha(opacity=40);
    	opacity: 0.4;
    	-moz-opacity:0.4;
    	z-index:1;
    }

	.table_main {
		padding:0;margin:0;border:0;
    	width:100%;
    	height:100%;
	}
	.tablecell_header {
    	background-color: #D6F1B9; 
		background-image: url(<?=DIRIMG?>header-bg.jpg);
		background-repeat: repeat-x;
		height:79px;
		width:100%;
	}
	.tablecell_sidebar {
		width:153px;
		background-color: #D6F1B9;
		background-image: url(<?=DIRIMG?>sidebar-bg.jpg);
		background-repeat: repeat-y;
	}
	.tablecell_body {
		width:626px;
	}
	
	#blackout {
		position:absolute;
		z-index:99;
		/*
		filter:alpha(opacity=70);
		opacity: 0.7;
		-moz-opacity:0.7;
		*/
		top:0px;left:0px;width:100%;height:100%;
		background-color:white;
		visibility:hidden;
	}
	#blackout2 {
		position: absolute;
		z-index:9999;
		left:91px;
		top: 90px;
	}
	
	
	/****************** FRAME ******************/
	
	div.frame_status {
		width:246px;
		display:none;
		position:relative;
	}
	
	.frame_title_text, .frame_title_text_minimized  {
		padding: 3px 15px 4px 25px;
		font-family: Verdana;
		font-size:13px;
		font-weight: bold;
		color: #FFFFFF;
		
		/*
		background-color: #1E3560;
		background-image: url(<?=DIRIMG?>frame_title_back.gif);
		*/
		float:left;
	}
	a.frame_title_text, a.frame_title_text_minimized {
		text-decoration: none;
		color: #FFFFFF;
	}
	a.frame_title_text:hover {
		text-decoration: overline;
	}
	a.frame_title_text_minimized:hover {
		text-decoration: underline;
	}
	
	.frame_greenbutton {
		text-align: center;
		font-family: Verdana;
		font-size: 9px;
		color: #FFFFFF;
		width: 98px;
		height: 22px;
		background-image: url(<?=DIRIMG?>frame_greenbutton.gif);
		background-color: #1E3560;
		margin:0;
		border:0;	
	}
	

	/****************** FRAMEBUTTON ******************/
	.framebutton_text   {
		padding: 1px 15px 1px 15px;
		font-family: Verdana;
		font-size:13px;
		font-weight: bold;
		color: #FFFFFF;
		float:left;
	}
	.framebutton_div {
		z-index:999;
		vertical-align:middle;
	}
		


	/****************** FORM ******************/

	.tablecell_form_help {
		padding: 5px 3px 5px 10px;
		
		background-color:#707786;
	}
	.tablecell_form_space {
		padding: 0px 0px 0px 0px;
		margin: 0;
		
		background-color:#1E3560;
	}
	.tablecell_form_item {
		padding: 5px 0px 5px 10px;
		
		background-color:#707786;
	}
	.tablecell_form_label, .tablecell_form_label A:link, .tablecell_form_label A:visited {
		padding: 5px 10px 5px 10px;
		font-weight: bold;
		text-align:right;
		color: #FFFFFF;
		background-color:#707786;
	}
	.table_form {
		font-family: Verdana;
		font-size:11px;
		
		background-color: #A4A7AE;
		margin:0;
		padding:0;
	}
	
	/****************** ITENS ******************/
	
	.list {
		width:239px;
		font-family: Verdana;
		font-size:12px;
	}
	.listdiv_edited {
		border-color: #FF6600;
	}
	.listdiv {
		width:239px;
		border-width:1px;
		border-style:solid;
	}
	.listdiv_normal {
		border-color:#A4A7AE;
	}
	
	
	.selectdiv_edited {
		border-color: #FF6600;
	}
	.selectdiv {
		/* width:239px;*/
	
		border-width:1px;
		border-style:solid;
	}
	.selectdiv_normal {
		border-color:#A4A7AE;
	}
	.select_all_div {
		/*width:237px;*/
		border-width:1px;
		border-style:solid;
		border-color:#A4A7AE;
		background-color: #ffffff;
	}
	.select_all_div_edit {
		background-color: #FFF4BA;
	}
	.select_textbox {
		/*width:217px;*/
		border:0px;
		padding: 2px 2px 2px 3px;
		cursor:default;
	}
	.select_textbox_edit {
		background-color: #FFF4BA;
	}
	
	.select_all_item {
		width:100%;
		font-family: Verdana;
		font-size:9px;
		padding: 3px 2px 3px 2px;
		cursor:pointer;
	}
	.select_all_item_selected {
		width:100%;
		font-family: Verdana;
		font-size:9px;
		padding: 3px 2px 3px 2px;
		cursor:pointer;
		background-color: #A4A7AE;
	}
	.select_all_div_edit {
		background-color: #FFF4BA;
	}

	
	
	.multilistdiv_edited {
		border-color: #FF6600;
	}
	.multilistdiv {
		width:239px;
	
		border-width:1px;
		border-style:solid;
	}
	.multilistdiv_normal {
		border-color:#A4A7AE;
	}
	.multilist_box {
		width:237px;
		height:60px;
		border: 1px solid #A4A7AE;
		background-color: #ffffff;
		overflow-y:auto;
		overflow-x:hidden;
	}
	.multilist_all_div {
		width:237px;
		border-width:1px;
		border-style:solid;
		border-color:#A4A7AE;
		background-color: #ffffff;
	}
	.multilist_all_div_edit {
		background-color: #FFF4BA;
	}
	.multilist_all {
		width:237px;
		font-family: Verdana;
		font-size:12px;
	}
	.multilist_all_item {
		width:100%;
		font-family: Verdana;
		font-size:9px;
		padding: 3px 2px 3px 2px;
		cursor:pointer;
	}
	.multilist_all_item_selected {
		width:100%;
		font-family: Verdana;
		font-size:9px;
		padding: 3px 2px 3px 2px;
		cursor:pointer;
		background-color: #A4A7AE;
	}
	.multilist_all_item_clicked {
		width:100%;
		font-family: Verdana;
		font-size:9px;
		padding: 3px 2px 3px 2px;
		cursor:pointer;
		background-color: #526FB3;
		color: #ffffff;	
	}
	.multilist_textbox {
		width:217px;
		border:0px;
		padding: 2px 2px 2px 3px;
	}
	.multilist_textbox_edit {
		background-color: #FFF4BA;
	}
	
	.duallist {
		width:122px;
		height:80px;
		font-family: Verdana;
		font-size: 9px;
		border: 1px solid #5C626E;
	}
	.duallist_title {
		font-family: Verdana;
		font-size: 9px;
		color: #ffffff;
		background-color: #5C626E;
		padding: 2px 0px 2px 0px;
		text-align: center;
	}		
	.textarea {
		width:241px;
		height:55px;
		border-style:solid;
		border-color:#A4A7AE;
		border-width:1px;
		padding:3px 3px 3px 3px;
		font-family: Verdana;
		font-size:11px;
	}
	.textarea_big {
		width:390px;
		height:200px;
		border-style:solid;
		border-color:#A4A7AE;
		border-width:1px;
		padding:3px 3px 3px 3px;	
		font-family: Verdana;
		font-size:11px;
	}
	.textarea_big_caption {
		font-family: Verdana;
		font-size:9px;
		
	}
	.textbox {
		width:241px;
		height:22px;
		border-style:solid;
		border-color:#A4A7AE;
		border-width:1px;
		padding:3px 3px 3px 3px;	
		font-family: Verdana;
		font-size:12px;
	}
	.textbox_edit {
		background-color: #FFF4BA;
	}
	.textbox_edited {
		border-color: #FF6600;
		/*background-color: #FFFFFF;*/
	}
	.textbox_preprocess {
		height:22px;
		padding:6px 3px 3px 6px;	
		font-family: Verdana;
		font-size:12px;
		color:#FFFFFF;
	}
	.txtfromnow {
		width:30px;
		height:22px;
		_height:20px;
		/*
		border-style:solid;
		border-color:#A4A7AE;
		border-width:1px;
		margin-top:1px; */
		
		/* padding:0px 0px 0px 0px; */
		font-family: Verdana;
		font-size:12px;
	}
	.listfromnow {
		height:20px;
		_height:22px; /* IE HACK */
		width:90px;
		/* border:1px solid white; */
		font-family: Verdana;
		font-size:12px;
	}
	.fromnowdiv {
		border-color:#A4A7AE;
		background-color:#A4A7AE;
		width:239px;
		border-width:1px;
		border-style:solid;
		vertical-align:middle;
	}
	.filebox {
		height:22px;
		border-style:solid;
		border-color:#A4A7AE;
		border-width:1px;
		padding:3px 3px 3px 3px;	
		font-family: Verdana;
		font-size:12px;
	}

	/****************** TABLE ******************/
	
	.table_frame {
		border:0px;
		width:100%;
		margin:0px;
		padding:0px;
		background-color:#A4A7AE;
	}
	.table_table {
		border:0px;
		width:100%;
		margin:0px;
		padding:0px;
		/*background-color:#707786; */
		border: 1px solid #707786;
	}
	.table_cell_header {
		font-family: Verdana;
		font-size: 11px;
		font-weight: bold;
		border-left: 1px solid #A4A7AE;
		padding: 2px 2px 2px 4px;
		cursor:default;
		color:#ffffff;
		background-color:#707786;
	}
	.table_cell_header_actions {
		/* border-left: 1px solid #A4A7AE; */
		background-color:#707786;		
	}

	.table_cell {
		font-family: Verdana;
		font-size: 9px;
		font-weight: normal;
		padding: 2px 2px 2px 4px;
		cursor: default;
	}
	tr.table_tr_over {
		background-color: #9196A1;
		color: #ffffff;
		/*
		border-top: 1px solid #FFF4BA;
		border-bottom: 1px solid #FFF4BA;
		*/
	}
	.table_tr_1 { 
		background-color: #B7BDC7;
	}		
	.table_tr_2 {
		background-color: #A9AFBA;
		border: 1px solid #A9AFBA;	
	}
	.explorer_menu_item {
		font-family: Verdana;
		font-size: 11px;
		font-weight: normal;
		padding: 2px 2px 2px 2px;
		cursor: pointer;
		background-color:rgb(112, 119, 134);
		color: #ffffff;
	}
	.explorer_menu_subitem {
		font-family: Verdana;
		font-size: 9px;
		font-weight: normal;
		padding: 2px 2px 2px 12px;
		cursor: pointer;
		background-color:rgb(112, 119, 134);
		text-decoration:underline;
		color: #ffffff;
	}
	