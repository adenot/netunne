<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 03/07/2006					*
	*																*
	****************************************************************/


?>
<HTML>
	<HEAD>

	<STYLE>
		.logtext {
			font-family: Verdana;
			font-weight: bold;
			font-size:13px;
			color: #FFFFFF;
			width:100%;
			/*height:100%;*/
		}
		.logadvice {
			font-family: Verdana;
			font-size:11px;
			color: #000000;
			text-align:center;
			width:100%;
			cursor:pointer;
		}
	    hr {
	    	background-color: #000000;
	    	border-width: 1px;
	    	border-style: solid;
	    	border-color: #000000;
	    	height:1px;
	    }
	</STYLE>
	<SCRIPT language="JavaScript">
	
	function urlencode(plaintext)
	{
		// The Javascript escape and unescape functions do not correspond
		// with what browsers actually do...
		var SAFECHARS = "0123456789" +					// Numeric
						"ABCDEFGHIJKLMNOPQRSTUVWXYZ" +	// Alphabetic
						"abcdefghijklmnopqrstuvwxyz" +
						"-_.!~*'()";					// RFC2396 Mark characters
		var HEX = "0123456789ABCDEF";
	
		var encoded = "";
		for (var i = 0; i < plaintext.length; i++ ) {
			var ch = plaintext.charAt(i);
		    if (ch == " ") {
			    encoded += "+";				// x-www-urlencoded, rather than %20
			} else if (SAFECHARS.indexOf(ch) != -1) {
			    encoded += ch;
			} else {
			    var charCode = ch.charCodeAt(0);
				if (charCode > 255) {
				    alert( "Unicode Character '" 
	                        + ch 
	                        + "' cannot be encoded using standard URL encoding.\n" +
					          "(URL encoding only supports 8-bit characters.)\n" +
							  "A space (+) will be substituted." );
					encoded += "+";
				} else {
					encoded += "%";
					encoded += HEX.charAt((charCode >> 4) & 0xF);
					encoded += HEX.charAt(charCode & 0xF);
				}
			}
		} // for
	
		return encoded;
	}

	var last_response;

	function printuserlog (wall,action) {

	   if (window.XMLHttpRequest) { 
	      xmlhttp = new XMLHttpRequest(); 
	   } else if (window.ActiveXObject) { 
	      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
	   } else { 
	       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
	      return; 
	   }
		
		//log = urlencode(log);
		
	    var str = "wall="+wall+"&action="+action;
	    //alert(log);
	    var url = "/_engine/jx_printuserlog.php"; // No question mark needed
	    xmlhttp.open("POST",url,true);
	    xmlhttp.onreadystatechange = printuserlog_return; 
	    xmlhttp.setRequestHeader("Content-Type",
	    "application/x-www-form-urlencoded; charset=UTF-8");
	    xmlhttp.send(str);
	
		window.setTimeout("printuserlog('"+wall+"','"+action+"')", 2000);
	
	}
	function printuserlog_return () {
		var response;
		var tmp = new Array(); 
		if (xmlhttp.readyState == 4) { 
			if (xmlhttp.status == 200) {
	           response = xmlhttp.responseText;
	           //alert(response);
				if ((last_response!=response)&&(response!="")) {
					
					tmp = response.split("#####");
					document.getElementById("content").innerHTML = tmp[1];
					document.getElementById("contenticon").src = <?=DIRIMG?>+tmp[0];
					if (tmp[2]==1) {
						parent.document.getElementById('userlog_buttons').style.display='block';
					}
					
					//document.getElementById("content").innerHTML = response;
					
					last_response = response;
					//window.scrollBy(0,5000);
				}
			}
		}
	}
	
	</SCRIPT>
	</HEAD>
<BODY bgcolor="#A4A7AE" onload="printuserlog('<?=$wall?>','<?=$action?>');">
<table width="250" height="100%" align=center cellspacing=0 cellpadding=6 border=0>
	<tr>
		<td width=50><img src="<?=DIRIMG?>userlog_info.gif" id="contenticon"></td>
		<td width=210 valign=middle><div id="content" class="logtext"></div></td>
	</tr>
</table>
</BODY>

</HTML>