<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 20, 2006					*
	*																*
	****************************************************************/
	
	require_once ("../common.php");
	
	$page = new Page(_("Customer Login"));
	$page->open();
	
	conv::include_all_fnc();

	
	/*
	 * variaveis do settings.ini
	 * - forceurl
	 * - welcome
	 * - poslogin
	 * - prelogin
	 * - login
	 * - password
	 * - key
	 * - button
	 */

	
	$custom = customlogin::getcustom();
	
	
	$fr = new Frame ("customlogin");
	$fr->title=_("Login Page Messages");
	
	$fm = new Form ("customlogin");
	
	$fm->itype="textbox";
	$fm->iname="forceurl";
	$fm->ilabel=_("Force URL");
	$fm->ihelp=_("Force load of this site after login<BR>Leave blank to user`s typed URL");
	$fm->ivalue=$custom[forceurl];
	$fm->nextitem();
		
	$fm->itype="filebox";
	$fm->iname="logo";
	$fm->ilabel=_("Upload Logo");
	$fm->ihelp=_("Upload your logo to <BR>the user`s login page");
	$fm->nextitem();

	$fm->itype="textbox";
	$fm->iname="title";
	$fm->ilabel=_("Window Title");
	$fm->ihelp=_("Text in browser`s window title");
	$fm->ivalue=$custom[title];
	$fm->nextitem();	
	
	$fm->itype="textarea";
	$fm->iname="welcome";
	$fm->ilabel=_("Welcome Message");
	$fm->ihelp=_("Text in top of login page");
	$fm->ivalue=$custom[welcome];
	$fm->nextitem();

	$fm->itype="textbox";
	$fm->iname="poslogin";
	$fm->ilabel=_("Customer Login Title");
	$fm->ihelp=_("Text above user/pass fields");
	$fm->ivalue=$custom[poslogin];
	$fm->nextitem();
	
	$fm->itype="textbox";
	$fm->iname="prelogin";
	$fm->ilabel=_("Credit Customer Login Title");
	$fm->ihelp=_("Text above key field");
	$fm->ivalue=$custom[prelogin];
	$fm->nextitem();
	
	$fm->itype="textbox";
	$fm->iname="login";
	$fm->ilabel=_("Customer Login Label");
	$fm->ihelp=_("The label next to the login field");
	$fm->ivalue=$custom[login];
	$fm->nextitem();

	$fm->itype="textbox";
	$fm->iname="password";
	$fm->ilabel=_("Customer Password Label");
	$fm->ihelp=_("The label next to the password field");
	$fm->ivalue=$custom[password];
	$fm->nextitem();
	
	$fm->itype="textbox";
	$fm->iname="key";
	$fm->ilabel=_("Credit Customer Key Label");
	$fm->ihelp=_("The label next to the key field");
	$fm->ivalue=$custom[key];
	$fm->nextitem();
	
	$fm->itype="textbox";
	$fm->iname="button";
	$fm->ilabel=_("'Login' Button Text");
	//$fm->ihelp="";
	$fm->ivalue=$custom[button];
	$fm->nextitem();
	
	$fr->draw($fm); 
	

	 
	$f1 = new Frame("customrestore");
	$f1->title = _("Restore Messages");
		$frm1 = new Form ("customrestore");
		$frm1->itype="list";
		$frm1->iname="lang";
		$frm1->ilabel=_("Language");
		$frm1->ivalues=array("pt_BR" => "Portugu&ecirc;s do Brasil", "en" => "English");
		$frm1->ihelp="";
		$frm1->nextitem();
	$f1->buttontext = _("Restore");
	$f1->draw($frm1);


	$f1 = new Frame("customtheme");
	$f1->title = _("Theme");
		$frm1 = new Form ("customtheme");
		$frm1->itype="list";
		$frm1->iname="theme";
		$frm1->ilabel=_("Select Theme");
		$frm1->ivalues=customlogin::getthemes();
		$frm1->value=$custom[theme];
		$frm1->ihelp="";
		$frm1->nextitem();
	//$f1->buttontext = _("Save");
	$f1->draw($frm1);
	

	$page->close();
	
	
	
?>
