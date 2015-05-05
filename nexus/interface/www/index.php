<?php

	require_once "common.php";
	
	//$page = new Page("Welcome to Nexus");
	//$page->close();
	
?>
<HTML><HEAD>
	<STYLE>
	html,body {
		height: 100%;
		vertical-align:middle;
		padding:0;
		margin:0;
		border:0;
		background-color: #ffffff;
		overflow:hidden;
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
		visibility:visible;
		
		text-align:center;
	}
	</STYLE>
	<SCRIPT>var blackout_opac;</SCRIPT>
    <TITLE><?=constant("PRODNAME")?></TITLE>
    </HEAD>
<BODY onload="document.title='<?=constant("PRODNAME")?>';"><div 
	onclick="this.style.visibility='hidden';"
	id="blackout"><table height="100%" align="center" border=0 cellspaccing=0 cellpadding=0>
			<tr><td valign="middle"><img src="<?=DIRIMG?>loading.gif"></td></tr></table></div>
<IFRAME src="/entrance/index.php" width="100%" height="100%"
	marginheight=0 marginwidth=0
	frameborder=0></IFRAME>
</BODY>
</HTML>