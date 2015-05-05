function alerta() {
	alert("ok");
}

// Detect if the browser is IE or not.
// If it is not IE, we assume that the browser is NS.
var IE = document.all?true:false

// If NS -- that is, !IE -- then set up for mouse capture
if (!IE) document.captureEvents(Event.MOUSEMOVE)

// Set-up to use getMouseXY function onMouseMove
document.onmousemove = getMouseXY;

// Temporary variables to hold mouse x-y pos.s
var mouseX = 0
var mouseY = 0

// Main function to retrieve mouse x-y pos.s
function getMouseXY(e) {
  if (IE) { // grab the x-y pos.s if browser is IE
    mouseX = event.clientX + document.body.scrollLeft
    mouseY = event.clientY + document.body.scrollTop
  } else {  // grab the x-y pos.s if browser is NS
    mouseX = e.pageX
    mouseY = e.pageY
  }  
  // catch possible negative values in NS4
  if (mouseX < 0){mouseX = 0}
  if (mouseY < 0){mouseY = 0}  
  
  clearTimeout(autologout);
  auto_logout();
  
  return true
}


var is_unblackout=0;

var autologout;

var xmlhttp;
var xmlhttp2; 
var xmlhttp_set;
var xmlhttp_end="yes";

function htmlentities(s) {
var ss = new String();
for( i = 0; i < s.length; i++) {
   var fs = s.charCodeAt(i);
   ss += '&#' + fs + ';';
   }
return ss;
}

function logout() {
	window.location = '/entrance/auth.php?action=logout';
}

function auto_logout() {
	// desloga sozinho em 10 minutos - a pedidos
	autologout = setTimeout("logout();",600000);
}

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

var keybox_name;
function getkey(name,size) { 

   if (!size) { size=0; }

   if (window.XMLHttpRequest) { 
      xmlhttp = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   }  
   keybox_name = name;
    
    var url = "/_engine/jx_getkey.php?size="+size; // No question mark needed
    xmlhttp.open("GET",url,true);
    //xmlhttp.setRequestHeader("Content-Type",
    // "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp.onreadystatechange = getkey_return; 
    xmlhttp.send('');

}
function getkey_return() {
    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
			response = xmlhttp.responseText;
			document.getElementById("keybox_"+keybox_name).value = response;
      } 
    } 
}

function getevents() { 

   if (window.XMLHttpRequest) { 
      xmlhttp = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   }  
    
    var url = "/_engine/jx_events.php"; // No question mark needed
    xmlhttp.open("GET",url,true);
    //xmlhttp.setRequestHeader("Content-Type",
    // "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp.onreadystatechange = getevents_return; 
    xmlhttp.send('');
    
    setTimeout("getevents();",2000);



}

function getevents_return() {
    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
			response = xmlhttp.responseText;
			document.getElementById("eventbox").innerHTML = response;
			if (response!="") {
				document.getElementById("eventbox_table").style.visibility='visible';
			} else {
				document.getElementById("eventbox_table").style.visibility='hidden';
			}
      } 
    } 
}
function savesetting(section,name,value) { 

   if (window.XMLHttpRequest) { 
      xmlhttp_set = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp_set = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   } 
   
   section = urlencode(section);
   name = urlencode(name);
   value= urlencode(value);
   
    var str = "func=save&section="+section+"&name="+name+"&value="+value;
    //alert(str);
    var url = "/_engine/jx_setting.php"; // No question mark needed
    xmlhttp_set.open("POST",url,true);
    xmlhttp_set.setRequestHeader("Content-Type",
    "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp_set.send(str);

} 

var clock_name;
function get_clock(name) { 

	setTimeout ("get_clock('"+name+"');",1000);

   if (window.XMLHttpRequest) { 
      xmlhttp2 = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp2 = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   }  
   clock_name = name;
    
    var url = "/_engine/jx_getclock.php";
    xmlhttp2.open("GET",url,true);
    //xmlhttp.setRequestHeader("Content-Type",
    // "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp2.onreadystatechange = getclock_return; 
    xmlhttp2.send('');

}
function getclock_return() {
    if (xmlhttp2.readyState == 4) { 
       if (xmlhttp2.status == 200) {
			response = xmlhttp2.responseText;
			document.getElementById(clock_name).innerHTML = response;
      } 
    } 
}

// variavel para passar do multilist_add pro response dele (ajax'd)
var multilist_box;

function multilist_add(name,uvalue) { 

   if (window.XMLHttpRequest) { 
      xmlhttp = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   }
   
	if (!uvalue) {
		// quando digitado ou clicado
		
		eval ('var value = multilist_selected_name_'+name+';');
		eval ('var label = multilist_selected_value_'+name+';');
		var mark=1;
		
		// zerando as variaveis e campos
		document.getElementById("multilist_textbox_"+name).value="";
		eval('multilist_selected_name_'+name+'="";');
		eval('multilist_selected_value_'+name+'="";');
		//alert(value+' '+label);
		
	} else {
		// add automatico
		var value = uvalue;
		eval('var all_names = multilist_names_'+name+';');
		eval('var all_values = multilist_values_'+name+';');
		for (i=0;i<all_names.length;i++) {
			if (all_names[i]==value) {
				var label = all_values[i];
				break;
			}
		}
		var mark=0;
	}
	
	if (value=="noadd") { return; }
	
	multilist_remove_array(name,value);
	eval ("var selected = multilist_selected_"+name+";");
	multilist_all_deselect(selected,name);
	eval ("multilist_selected_"+name+"=-1;");
	
	
	//alert("value:"+value+"\nlabel:"+label);
	
	value= urlencode(value);
	label = urlencode(label);
	var str = "func=add&name="+name+"&value="+value+"&label="+label+"&mark="+mark;
	
	eval ('multilist_box = "multilist_box_'+name+'";');


	document.getElementById('multilist_hidden_'+name).value += value+';';
	var newvalue = document.getElementById('multilist_hidden_'+name).value;

	var multilist_value;
	eval ("multilist_value = multilist_value_"+name+";");
	
	// muda a borda se o valor padrao for diferente do atual
	if (multilist_value!=newvalue) {
		document.getElementById('multilistdiv_'+name).className='multilistdiv multilistdiv_edited';
	} else {
		document.getElementById('multilistdiv_'+name).className='multilistdiv multilistdiv_normal';
	}

    var url = "/_engine/jx_multilist.php"; // No question mark needed
    xmlhttp.open("POST",url,true);
    xmlhttp.onreadystatechange = multilist_add_return; 
    xmlhttp.setRequestHeader("Content-Type",
    "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp.send(str);
} 
function multilist_add_return() {
    var response;
    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
           response = xmlhttp.responseText;
           //alert(response);
			document.getElementById(multilist_box).innerHTML = response+document.getElementById(multilist_box).innerHTML;
      } 
    } 
}

function multilist_remove(name,value,label,box,item) {
	//alert("box:"+box+" item:"+item);
	var newvalue = document.getElementById('multilist_hidden_'+name).value;
	newvalue = newvalue.replace(value+';','');
	document.getElementById('multilist_hidden_'+name).value = newvalue;
	document.getElementById(box).removeChild(item);

	var multilist_value;
	eval ("multilist_value = multilist_value_"+name+";");
	
	// muda a borda se o valor padrao for diferente do atual
	if (multilist_value!=newvalue) {
		document.getElementById('multilistdiv_'+name).className='multilistdiv multilistdiv_edited';
	} else {
		document.getElementById('multilistdiv_'+name).className='multilistdiv multilistdiv_normal';
	}

//	alert(newvalue);

	multilist_add_array(name,value,label);

}

function multilist_remove_array(name,value) {
// lembrando:
	// value=name[]
	// label=value[]
	
		//alert("rem_array");
	
		eval('var all_names = multilist_names_'+name+';');
		eval('var all_values = multilist_values_'+name+';');
		var new_names = new Array();
		var new_values = new Array();
		var j=0;
		
		for (i=0;i<all_names.length;i++) {
			if (value!=all_names[i]) {
				new_names[j]=all_names[i];
				new_values[j]=all_values[i];
				j++;
			}
		}
		eval('multilist_names_'+name+' = new_names;');
		eval('multilist_values_'+name+' = new_values;');

}
function multilist_add_array(name,value,label) {
// lembrando:
	// value=name[]
	// label=value[]
	
		//alert("add_array");
	
		eval('var all_names = multilist_names_'+name+';');
		eval('var all_values = multilist_values_'+name+';');
		eval('var all_original = multilist_original_'+name+';');
		var new_names = new Array();
		var new_values= new Array();
		var tlabel="";
		var tvalue="";
		var j;
		var k=0;

		for (var i=0;i<all_original.length;i++) {
			//txt.value+="entra "+all_original[i]+"\n";
			for (var j=0;j<all_values.length;j++) {	
				if (all_names[j]==all_original[i]) {
					//txt.value+="achou na lista atual: "+all_original[i]+"\n";
					tlabel=all_values[j];
					tvalue=all_names[j];
					break;
				}
			}
			if ((tlabel=="")&&(tvalue=="")) {
				//txt.value+="nao achou na lista atual \n";
				if (added==1) {
					//txt.value+="jah foi addado\n";
					continue;
				} else {
					//txt.value+="addando da funcao "+label+"\n";
					new_names[k]=value;
					new_values[k]=label;
					k++;
					var added=1;
				}
			} else {
				//txt.value+="addando da lista: "+tlabel+"\n";
				new_names[k]=tvalue;
				new_values[k]=tlabel;
				k++;
			}
			
			tlabel="";
			tvalue="";
		}	
		
		eval('multilist_names_'+name+' = new_names;');
		eval('multilist_values_'+name+' = new_values;');
}

function multilist_show_list ( arrN,arrV,name) {
	var newdiv;

	document.getElementById("multilist_all_"+name).innerHTML="";

	for (var i=0;i<arrN.length;i++) {
		if (arrN[i]=="") { continue; };
		newdiv=document.createElement("DIV");
		newdiv.setAttribute("className","multilist_all_item");
		newdiv.setAttribute("class","multilist_all_item");
		newdiv.setAttribute("id","multilist_all_item_"+name+"_"+i);
		eval('newdiv.onclick=function onclick(event) { multilist_all_click("'+arrN[i]+'","'+arrV[i]+'","'+i+'","'+name+'","yes","yes"); }');
		newdiv.onmouseover=function onmouseover(event) { this.className='multilist_all_item_selected'; }
		newdiv.onmouseout=function onmouseout(event) { this.className='multilist_all_item'; }
		newdiv.innerHTML = htmlentities(arrV[i]);
		
		document.getElementById("multilist_all_"+name).appendChild(newdiv);
	}
	document.getElementById("multilist_all_"+name).style.visibility="visible";

}
function multilist_hide_list (name) {
	document.getElementById("multilist_all_"+name).style.visibility="hidden";

}
function multilist_showhide_list (name,blur) {
	list = document.getElementById("multilist_all_"+name);
	if (list.style.visibility=="hidden") {
		//eval("multilist_show_list(multilist_names_"+name+",multilist_values_"+name+",'"+name+"');");
		multilist_all_keyup(null,name);
	} else {
		multilist_hide_list(name);
	}
	document.getElementById("multilist_textbox_"+name).focus();
	eval ('clearTimeout(multilist_tid_'+name+');');
}

function addEvent(obj,event_name,func_name,name){
	eval ('var func=function (e) { return '+func_name+'(e,"'+name+'"); };');
	if (obj.attachEvent){
		obj.attachEvent("on"+event_name, func );
	}else if(obj.addEventListener){
		obj.addEventListener(event_name,func,true);
	}else{
		obj["on"+event_name] = func;
	}
}
function multilist_textbox_focus(name) {
	document.getElementById('multilist_textbox_'+name).className="multilist_textbox multilist_textbox_edit";
	document.getElementById('multilist_all_div_'+name).className="multilist_all_div multilist_all_div_edit";

}
function multilist_textbox_blur(name) {
	document.getElementById('multilist_textbox_'+name).className="multilist_textbox";
	document.getElementById('multilist_all_div_'+name).className="multilist_all_div";

	eval('multilist_tid_'+name+' = setTimeout("multilist_hide_list(\\"'+name+'\\");",200);');
}
function multilist_all_keyup (evt,name) {
	if (evt!=null) {
		if(multilist_all_updown(evt)==false) { return; }
	}	
	eval("var arrN = multilist_names_"+name+";");
	eval("var arrV = multilist_values_"+name+";");
	var newarrN = new Array();
	var newarrV = new Array();
	var j=0;
	var str = document.getElementById("multilist_textbox_"+name).value;
	
	for (var i=0;i<arrN.length;i++) {
		if (arrV[i].indexOf(str)!=-1) {
			newarrN[j]=arrN[i];
			newarrV[j]=arrV[i];
			j++;
		}
	}
	multilist_show_list(newarrN,newarrV,name);
	
	eval("multilist_selected_"+name+" = -1;");
	
	// se soh tiver um e for igual ao q foi digitado, eu seleciono
	if ((j==1)&&(str==newarrV[0])) {
		//alert(newarrV[0]);
		eval("multilist_selected_"+name+" = 0;");
		multilist_all_choose (name);
		multilist_all_click (newarrN[0],newarrV[0],0,name);
	}
}
function multilist_all_updown (evt,name) {
	if (!evt) evt = window.event;
	var k = evt.keyCode;
	
	if (k==38) {
		if (name) { multilist_all_moveup(name); }
        evt.returnValue = false;
        evt.cancel = true;
        evt.preventDefault();
        return false;
	} else if (k==40) {
		if (name) { multilist_all_movedown(name); }
        evt.returnValue = false;
        evt.cancel = true;  
        evt.preventDefault();
        return false;
	} else if (k==13) {
		if (name) { multilist_all_choose(name,1);  }
        evt.returnValue = false;
        evt.cancel = true;  
        evt.preventDefault();
        return false;
	}
}
function multilist_all_click (strN,strV,i,name,hide,add) {

	document.getElementById('multilist_textbox_'+name).value=strV;
	eval("multilist_selected_name_"+name+" = strN;");
	eval("multilist_selected_value_"+name+" = strV;");
	multilist_all_select(i,name,1);

	eval ("var selected = multilist_selected_"+name+";");
	if (selected != i) { 
		multilist_all_deselect(selected,name);
	
		selected = i;
		eval ("multilist_selected_"+name+" = selected;");
	}

	document.getElementById('multilist_textbox_'+name).focus();

	if (hide=="yes") {
		multilist_hide_list(name);
	}
	if (add=="yes") {
		multilist_add(name);
		document.getElementById('multilist_textbox_'+name).value="";
	}
}

function multilist_all_choose (name,doclick) {
	eval ("var selected = multilist_selected_"+name+";");
	if (doclick==1) {
		if (document.getElementById('multilist_all_item_'+name+'_'+selected)) {
			document.getElementById('multilist_all_item_'+name+'_'+selected).onclick();
		}
	}
	multilist_all_select(selected,name);
}
function multilist_all_select (i,name,click) {
	//alert("select "+click);
	if (!document.getElementById('multilist_all_item_'+name+'_'+i)) { return; }
	if (click==1) {
		document.getElementById('multilist_all_item_'+name+'_'+i).className="multilist_all_item_clicked";
	} else {
		document.getElementById('multilist_all_item_'+name+'_'+i).className="multilist_all_item_selected";
	}
}
function multilist_all_deselect(i,name) {
	if (document.getElementById('multilist_all_item_'+name+'_'+i)) {
		document.getElementById('multilist_all_item_'+name+'_'+i).className="multilist_all_item";
	}
}

function multilist_all_moveup (name) {
	eval ("var selected = multilist_selected_"+name+";");
	if (selected==0) { return; }
	selected--;
	
	eval ("multilist_selected_"+name+" = selected;");

	multilist_all_select(selected,name);
	selected++;
	multilist_all_deselect(selected,name);


}
function multilist_all_movedown (name) {
	eval ("var selected = multilist_selected_"+name+";");
	
	list = document.getElementById("multilist_all_"+name);
	if (list.style.visibility=="hidden") {
		selected=-1;
		multilist_all_keyup(null,name);
	}

	selected++;

	if (!document.getElementById('multilist_all_item_'+name+'_'+selected)) { return; }

	eval ("multilist_selected_"+name+" = selected;");

	multilist_all_select(selected,name);
	selected--;
	if (selected!=-1) {
		multilist_all_deselect(selected,name);
	}
}


function multilist_reset(name) {
	var value = document.getElementById('multilist_hidden_'+name).value;
	eval ('multilist_value_'+name+' = value;');
	document.getElementById('multilistdiv_'+name).className='multilistdiv multilistdiv_normal';
}



function select_textbox_focus(name) {
	document.getElementById('select_textbox_'+name).className="select_textbox select_textbox_edit";
	document.getElementById('select_all_div_'+name).className="select_all_div select_all_div_edit";

}
function select_textbox_blur(name) {
	document.getElementById('select_textbox_'+name).className="select_textbox";
	document.getElementById('select_all_div_'+name).className="select_all_div";

	eval('select_tid_'+name+' = setTimeout("select_hide_list(\\"'+name+'\\");",200);');
}

function select_showhide_list (name,blur) {
	list = document.getElementById("select_all_"+name);
	if (list.style.visibility=="hidden") {
		select_show_list(name);
	} else {
		select_hide_list(name);
	}
	document.getElementById("select_textbox_"+name).focus();
	eval ('clearTimeout(select_tid_'+name+');');
}

function select_hide_list (name) {
	document.getElementById("select_all_"+name).style.visibility="hidden";
}
function select_show_list (name) {
	document.getElementById("select_all_"+name).style.visibility="visible";
}

function select_create_list ( arrN,arrV,name,formname) {
	var newdiv;

	document.getElementById("select_all_"+name).innerHTML="";

	for (var i=0;i<arrN.length;i++) {
		if (arrN[i]=="") { continue; };
		newdiv=document.createElement("DIV");
		newdiv.setAttribute("className","select_all_item");
		newdiv.setAttribute("class","select_all_item");
		newdiv.setAttribute("id","select_all_item_"+name+"_"+i);
		eval('newdiv.onclick=function onclick(event) { select_all_click("'+arrN[i]+'","'+arrV[i]+'","'+name+'","'+formname+'"); }');
		newdiv.onmouseover=function onmouseover(event) { this.className='select_all_item_selected'; }
		newdiv.onmouseout=function onmouseout(event) { this.className='select_all_item'; }
		newdiv.innerHTML = htmlentities(arrV[i]);
		
		document.getElementById("select_all_"+name).appendChild(newdiv);
	}
	document.getElementById("select_all_"+name).style.visibility="visible";

}

function select_all_click (value,label,name,formname) {
	document.getElementById("select_textbox_"+name).value=label;
	document.getElementById("select_hidden_"+name).value=value;
	select_hide_list(name);
	
	eval ('select_value = select_value_'+name+';');

	if(value != select_value) {
		if (document.getElementById('selectdiv_'+name))
			document.getElementById('selectdiv_'+name).className='selectdiv selectdiv_edited';
		status_reset(formname);
	} else {
		if (document.getElementById('selectdiv_'+name))
			document.getElementById('selectdiv_'+name).className='selectdiv selectdiv_normal';
	}
}	
function select_reset(name) {
	value = document.getElementById("select_hidden_"+name).value;
	eval ('select_value_'+name+' = value;');
	if (document.getElementById('selectdiv_'+name))
		document.getElementById('selectdiv_'+name).className='selectdiv selectdiv_normal';
}


function minimize_frame (framename) {
	document.getElementById('frame_'+framename).style.display='none';
	document.getElementById('frame_minimized_'+framename).style.display='block';
	savesetting('startminimized',framename,'1');
}
function maximize_frame (framename) {
	document.getElementById('frame_'+framename).style.display='block';
	document.getElementById('frame_minimized_'+framename).style.display='none';
	savesetting('startminimized',framename,'0');
}

function textbox_onfocus(name) {
	document.getElementById('textbox_'+name).className='textbox textbox_edit';
}
function textbox_onchange(name,formname) {
	eval ('textbox_value = textbox_value_'+name+';');

	if(document.getElementById('textbox_'+name).value != textbox_value) {
		document.getElementById('textbox_'+name).className='textbox textbox_edited textbox_edit';
		status_reset(formname);
	} else {
		document.getElementById('textbox_'+name).className='textbox textbox_edit';
	}
}
function textbox_onblur (name,formname) {
	eval ('textbox_value = textbox_value_'+name+';');

	if(document.getElementById('textbox_'+name).value != textbox_value) {
		document.getElementById('textbox_'+name).className='textbox textbox_edited';
		status_reset(formname);
	} else {
		document.getElementById('textbox_'+name).className='textbox';
	}
}

function textbox_reset(name,formname) {
	textbox_value = document.getElementById('textbox_'+name).value;
	eval ('textbox_value_'+name+' = textbox_value;');

	textbox_onblur(name,formname);
}

function textarea_onfocus(name) {
	document.getElementById('textarea_'+name).className='textarea textbox_edit';
}
function textarea_onchange(name,formname) {
	eval ('textarea_value = textarea_value_'+name+';');

	if(document.getElementById('textarea_'+name).value != textarea_value) {
		document.getElementById('textarea_'+name).className='textarea textbox_edited textbox_edit';
		status_reset(formname);
	} else {
		document.getElementById('textarea_'+name).className='textarea textbox_edit';
	}
}
function textarea_onblur (name,formname) {
	eval ('textarea_value = textarea_value_'+name+';');

	if(document.getElementById('textarea_'+name).value != textarea_value) {
		document.getElementById('textarea_'+name).className='textarea textbox_edited';
		status_reset(formname);
	} else {
		document.getElementById('textarea_'+name).className='textarea';
	}
}
function textarea_reset(name,formname) {
	textarea_value = document.getElementById('textarea_'+name).value;
	eval ('textarea_value_'+name+' = textarea_value;');

	textarea_onblur(name,formname);
}


function textarea_big_onfocus(name) {
	document.getElementById('textarea_'+name).className='textarea_big textbox_edit';
}
function textarea_big_onchange(name,formname) {
	eval ('textarea_value = textarea_value_'+name+';');

	if(document.getElementById('textarea_'+name).value != textarea_value) {
		document.getElementById('textarea_'+name).className='textarea_big textbox_edited textbox_edit';
		status_reset(formname);
	} else {
		document.getElementById('textarea_'+name).className='textarea_big textbox_edit';
	}
}
function textarea_big_onblur (name,formname) {
	eval ('textarea_value = textarea_value_'+name+';');

	if(document.getElementById('textarea_'+name).value != textarea_value) {
		document.getElementById('textarea_'+name).className='textarea_big textbox_edited';
		status_reset(formname);
	} else {
		document.getElementById('textarea_'+name).className='textarea_big';
	}
}
function textarea_big_reset(name,formname) {
	textarea_value = document.getElementById('textarea_'+name).value;
	eval ('textarea_value_'+name+' = textarea_value;');

	textarea_onblur(name,formname);
}

function textarea_big_div_showhide(name) {
	// 328	
	var div = document.getElementById("textarea_big_div_"+name);
	if (div.style.visibility=="hidden") {
		div.style.display='';
		div.style.visibility="visible";
	} else {
		div.style.display='none';
		div.style.visibility="hidden"; 
	}
	
		
}


function status_reset(formname) {
	if (document.getElementById('frame_'+formname+'_status').style.display=='block') {
		document.getElementById('frame_'+formname+'_status').style.display='none';
		document.getElementById('iframe_'+formname).src='/_engine/act.php';
	}

}

function list_onchange(name,formname) {
	eval ('list_value = list_value_'+name+';');

	if(document.getElementById('list_'+name).value != list_value) {
		document.getElementById('listdiv_'+name).className='listdiv listdiv_edited';
		status_reset(formname);
	} else {
		document.getElementById('listdiv_'+name).className='listdiv listdiv_normal';
	}


}
function list_reset(name,formname) {
	list_value = document.getElementById('list_'+name).value;
	eval ('list_value_'+name+' = list_value;');
	list_onchange(name,formname);
}


function list_showtr(targetname,formname) {
	sep = document.getElementById("tr_sep_"+formname+"_"+targetname);
	obj = document.getElementById("tr_"+formname+"_"+targetname);
	obj.style.display="";

	if (sep) {
		sep.style.display="";
	}
}
function list_hidetr(targetname,formname) {
	sep = document.getElementById("tr_sep_"+formname+"_"+targetname);
	obj = document.getElementById("tr_"+formname+"_"+targetname);
	obj.style.display="none";

	if (sep) {
		sep.style.display="none";
	}
}

function list_showtr0(value,name,targetname,formname,fix) {
	//alert("tr_"+formname+"_"+name);

	sep = document.getElementById("tr_sep_"+formname+"_"+targetname);
	obj = document.getElementById("tr_"+formname+"_"+targetname);


	sel = document.getElementById("list_"+name);
	if (value!=sel.value) { return; }


	eval ('fixed_hide = form_'+formname+'_fixed_hide;');
	eval ('fixed_show = form_'+formname+'_fixed_show;');
	

	if (fix==1) {

		// TIRANDO DO FIXED_HIDE
		var fixed_hide2 = new Array();
		while (tmp=fixed_hide.pop()) {
			if (tmp!=formname+"_"+targetname) {
				fixed_hide2.push(tmp);
			}
		}

		eval ('form_'+formname+'_fixed_hide = fixed_hide2;');
		
		// COLOCANDO NO FIXED_SHOW
		fixed_show.push(formname+"_"+targetname);
		eval ('form_'+formname+'_fixed_show = fixed_show;');

		//if (obj.style.display=='none') {
		//	return;
		//}
	
	
	} else {
		for (i in fixed_hide) {
			if (fixed_hide[i]==formname+"_"+targetname) {
				return;
			}
		}
	}
	
	//alert("fixedhide: "+fixed_hide.join("\n")+"\nfixedshow: "+fixed_show.join("\n"));
	
	
	obj = document.getElementById("tr_"+formname+"_"+targetname);
	obj.style.display="";

	if (sep) {
		sep.style.display="";
	}
	
}

function list_hidetr0(value,name,targetname,formname,fix) {
	//alert("tr_"+formname+"_"+name);
	
	sel = document.getElementById("list_"+name);
	if (value!=sel.value) { return; }


	if (fix==1) {

		// TIRANDO DO FIXED_SHOW
		var fixed_show2 = new Array();
		while (tmp=fixed_show.pop()) {
			if (tmp!=formname+"_"+targetname) {
				fixed_show2.push(tmp);
			}
		}

		eval ('form_'+formname+'_fixed_show = fixed_show2;');
		
		// COLOCANDO NO FIXED_HIDE
		fixed_hide.push(formname+"_"+targetname);
		eval ('form_'+formname+'_fixed_hide = fixed_hide;');

	
	} else {
		for (i in fixed_show) {
			if (fixed_show[i]==formname+"_"+targetname) {
				return;
			}
		}
	}
	
	//alert("fixedhide: "+fixed_hide.join("\n")+"\nfixedshow: "+fixed_show.join("\n"));
	

	obj = document.getElementById("tr_"+formname+"_"+targetname);
	obj.style.display="none";
	
	sep = document.getElementById("tr_sep_"+formname+"_"+targetname);
	if (sep) {
		sep.style.display="none";
	}
}


function form_release(name) {
	eval ('form_fields = form_'+name+'_fields;');
	eval ('form_types = form_'+name+'_types;');

	for (i=0;i<form_fields.length;i++) {
		//alert(form_types[i]);
		if (form_types[i]) {
			eval(form_types[i]+'_reset(\''+form_fields[i]+'\',name);');
		}
	}
}

function tooltip_show (text,obj,offsetx,offsety) {
	if (!obj) { obj=0; }
	if (!offsetx) { offsetx=390; }
	if (!offsety) { offsety=-16; }

	if (text=="") { return; }

	var tooltip = document.getElementById('tooltip');
	var tooltipshadow = document.getElementById('tooltipshadow');
	tooltip.innerHTML=text;

	tooltipshadow.style.width=tooltip.clientWidth+"px";
	tooltipshadow.style.height=tooltip.clientHeight+"px";
	if (obj==0) {
		tooltipshadow.style.left=mouseX+10; // os valores de baixo +4
		tooltipshadow.style.top=mouseY+24;
	
		tooltip.style.left=mouseX+6;
		tooltip.style.top=mouseY+20;
		
	} else {
		obj = document.getElementById(obj);
		
		tooltip.style.left=obj.offsetLeft+offsetx;
		tooltip.style.top=obj.offsetTop+offsety;
	
		tooltipshadow.style.left=obj.offsetLeft+offsetx+4
		tooltipshadow.style.top=obj.offsetTop+offsety+4;
	}
		
	tooltipshadow.style.visibility='visible';
	tooltip.style.visibility='visible';

	


	//setTimeout('tooltip_hide();',5000);
}
function tooltip_hide() {
	var tooltip = document.getElementById('tooltip');
	var tooltipshadow = document.getElementById('tooltipshadow');
	tooltip.style.visibility='hidden';
	tooltipshadow.style.visibility='hidden';
	tooltip.style.left=0;
	tooltip.style.top=0;
	tooltipshadow.style.left=0;
	tooltipshadow.style.top=0;
}

function hide_all_selects () {
	selects = document.getElementsByTagName("select");
	for (i=0;i<selects.length;i++) {
		selects[i].style.visibility="hidden";
	}
	//alert("hideados");
} 
function show_all_selects () {
	selects = document.getElementsByTagName("select");
	for (i=0;i<selects.length;i++) {
		selects[i].style.visibility="visible";
	}
	//alert("showed");
}

function blackout () {
	if (checkOpac()==70) { return; }

	hide_all_selects();	
	changeOpac(0,"blackout");
	document.getElementById("blackout").style.visibility="visible";
	opacity("blackout",0,70,200);
}
function unblackout () {
	if (checkOpac()==0) { return; }

	opacity("blackout",70,0,200);
	//document.getElementById("blackout").style.visibility="hidden";
	document.getElementById("blackout2").innerHTML="";

	show_all_selects();
}
function pageblackout () {
	hide_all_selects();
	parent.document.getElementById("blackout").style.visibility="visible";
	changeOpac(100,"blackout",1);
	opacity("blackout",0,100,10,1); // precisa senao o IE pisca...
}
function pageunblackout () {
	is_unblackout=1;
	opacity("blackout",100,0,200,1);
//	parent.document.getElementById("blackout").style.visibility="hidden";
//	parent.document.getElementById("blackout").style.zIndex="-1";

}

function opacity(id, opacStart, opacEnd, millisec, isparent) {
    //speed for each frame
    var speed = Math.round(millisec / 100);
    var timer = 0;

    //determine the direction for the blending, if start and end are the same nothing happens
    if(opacStart > opacEnd) {
        for(i = opacStart; i >= opacEnd; i--) {
            setTimeout("changeOpac(" + i + ",'" + id + "','"+isparent+"')",(timer * speed));
            timer++;
        }
    } else if(opacStart < opacEnd) {
        for(i = opacStart; i <= opacEnd; i++)
            {
            setTimeout("changeOpac(" + i + ",'" + id + "','"+isparent+"')",(timer * speed));
            timer++;
        }
    }
}

//change the opacity for different browsers
function changeOpac(opacity, id, isparent) {
	if (isparent==1) {
	    var object = parent.document.getElementById(id).style;
	    parent.blackout_opac = opacity;
	} else {
	    var object = document.getElementById(id).style;
	    blackout_opac = opacity;
	}

    if (opacity!=0) {
    	object.visibility="visible";
    	if (is_unblackout!=1) {
    		hide_all_selects();
    	}
    }

    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
    
    if (opacity==0) {
    	object.visibility="hidden";
  		show_all_selects();
    }
} 
function checkOpac (isparent) {
	if (isparent==1) {
	    return parent.blackout_opac;
	} else {
	    return blackout_opac;
	}
}

function table_apply () {
	blackout();

   if (window.XMLHttpRequest) { 
      xmlhttp = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   }

	
    var url = "/_engine/jx_apply.php"; // No question mark needed
    xmlhttp.open("GET",url,true);
    xmlhttp.onreadystatechange = table_framelog_return; 
    //xmlhttp.setRequestHeader("Content-Type",
    // "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp.send("");

}

function table_framelog (title,action,details) {

	if (xmlhttp_end=="no") {
		setTimeout("table_framelog('"+title+"','"+action+"');",100);
		return;
	}

	xmlhttp_end="no";

	blackout();

   if (window.XMLHttpRequest) { 
      xmlhttp = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   }
	
	title = urlencode(title);
	action= urlencode(action);
	
    var str = "logtitle="+title+"&action="+action+"&details="+details;
    var url = "/_engine/jx_framelog.php"; // No question mark needed
    xmlhttp.open("POST",url,true);
    xmlhttp.onreadystatechange = table_framelog_return; 
    xmlhttp.setRequestHeader("Content-Type",
    "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp.send(str);

}

function table_framelog_return () {
    var response;
    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
           response = xmlhttp.responseText;
           //alert(response);
			document.getElementById("blackout2").innerHTML = response;
			xmlhttp_end="yes";
      }
    }
}

function framebutton_animate(name,top) {
	if (!top) { 
		top=0;
		changeOpac(0,"framebutton_"+name+"_hideout");

	}
	obj = document.getElementById("framebutton_"+name);
	
	
	if (top<30) {
		if (is_unblackout==1) {
			top = top + 10;
			obj.style.top = top-38+"px";
			opac = top * 2;
			//changeOpac(opac,"framebutton_"+name+"_hideout");
		}
		setTimeout("framebutton_animate('"+name+"',"+top+");",100);
	} else {
		obj.style.top = "0px";
		
		opacity("framebutton_"+name+"_hideout",0,100,200);
		show_all_selects();
		
	}

}



	function showSubMenu(id){
		obj = "menu"+id;
		obj = document.getElementById(obj);
		obj.className = (obj.className=="menuSubItemDisabled")?"menuSubItemEnabled":"menuSubItemDisabled";
	}

String.prototype.ucFirst = function () {
   var firstLetter = this.substr(0,1).toUpperCase()
   return this.substr(0,1).toUpperCase() + this.substr(1,this.length);
}

	function hidetr(obj, arrObj, arrSep, vl){
		
		totTr = document.getElementById('form').getElementsByTagName('tr').length;
		
		if(obj == vl) {
				
			// Loop pra esconder os campos
			for(i = 0; i < arrObj.length; i++){
				
				if(document.getElementById(arrObj[i])){
					
					document.getElementById(arrObj[i]).style.display = 'none';
					
				}
				
			}
			
			// Loop pra esconder os separadores dos campos
			for(i = 0; i < arrSep.length; i++){
				
				if(document.getElementById(arrSep[i])){
						
					document.getElementById(arrSep[i]).style.display = 'none';
					
				}
				
			}
			/*
			// Loop pra ocultar o ultimo separador
			if(document.getElementById('form').getElementsByTagName('tr')[totTr-2].style.display == 'none'){
				
				for(i = ((totTr-arrObj.length)-2); i > 0; i--){

					if(document.getElementById('form').getElementsByTagName('tr')[i]){
						
						if(document.getElementById('form').getElementsByTagName('tr')[i].style.display == ''){
							
							document.getElementById('form').getElementsByTagName('tr')[i].style.display = 'none';
							break;
						
						} // if
					
					} // if
				
				} // for
			
			} // if
			*/
		} // if
		
	} // function
	
	function showtr(obj, arrObj, arrSep, vl){
		
		totTr = document.getElementById('form').getElementsByTagName('tr').length;
		
		if(obj == vl) {
				
			// Loop pra esconder os campos
			for(i = 0; i < arrObj.length; i++){
				
				if(document.getElementById(arrObj[i])){
					
					document.getElementById(arrObj[i]).style.display = '';
					
				}
				
			}
			
			// Loop pra esconder os separadores dos campos
			for(i = 0; i < arrSep.length; i++){
				
				if(document.getElementById(arrSep[i])){
						
					document.getElementById(arrSep[i]).style.display = '';
					
				}
				
			}
			/*
			// Loop pra ocultar o ultimo separador
			if(document.getElementById('form').getElementsByTagName('tr')[totTr-2].style.display == 'none'){
				
				for(i = ((totTr-arrObj.length)-2); i > 0; i--){

					if(document.getElementById('form').getElementsByTagName('tr')[i]){
						
						if(document.getElementById('form').getElementsByTagName('tr')[i].style.display == ''){
							
							document.getElementById('form').getElementsByTagName('tr')[i].style.display = 'none';
							break;
						
						} // if
					
					} // if
				
				} // for
			
			} // if
			*/
		} // if
		
	} // function
	
