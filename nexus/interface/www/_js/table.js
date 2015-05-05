// page:
// 	X - carrega a pagina correspndente
// order:
// 	asc X - ordena ascendentemente pelo campo X
// 	desc X

var table_name;
var table_xml_timeout;

function table_load (name,page,order,list) { 

	//alert("table_load("+name+","+page+","+order+")");

	oTable = document.getElementById("table_"+name);
	oStatus = document.getElementById("table_status_"+name);

	oStatus.innerHTML="Opening socket";


	if (xmlhttp_end=="no") {
		window.setTimeout("table_load('"+name+"','"+page+"','"+order+"','"+list+"');",100);
		return;
	}
	
	// salvando o estado da tabela
	if (order) {
		savesetting("table","order_"+name,order);
	}
	savesetting("table","page_"+name," "+page+" ");

	xmlhttp_end="no";

	if (window.XMLHttpRequest) { 
		xmlhttp = new XMLHttpRequest(); 
	} else if (window.ActiveXObject) { 
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
	} else { 
		alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
		return; 
	}

	//alert(name+order);
	
	var blackout = document.getElementById("table_blackout_"+name);
	var tablediv = document.getElementById("table_"+name);
   
 	//alert(tablediv.clientTop);
   
	name = urlencode(name);
	table_name = name;



	oStatus.innerHTML="Preparing Request";

	// page nao passa pelo urlencode pq eh soh numero
	order = urlencode(order);
	search_value = urlencode(eval('table_search_value_'+name));
	search_field = urlencode(eval('table_search_field_'+name));
	
	var str = "name="+name+"&page="+page+"&order="+order+"&search_value="+search_value+"&search_field="+search_field;
	var url = "/_engine/jx_table.php";
    xmlhttp.onreadystatechange = table_load_return; 
    
    xmlhttp.open("POST",url,true);
 
    xmlhttp.setRequestHeader("Content-Type",
    "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp.send(str);
    
    
	oStatus.innerHTML="Waiting for results...";
	
	// se nao carregar em 10 segundos eu libero o soquete
	table_xml_timeout = window.setTimeout("xmlhttp_end='yes';",10000);
	
	table_updatepage(name);
	
} 

function table_load_return() {
    var response;
    var tmp=new Array();
    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
			response = xmlhttp.responseText;
			tmp = response.split("#####");
			document.getElementById("table_"+table_name).innerHTML = tmp[3];
			eval('var table_total_'+name+' = '+tmp[1]+';');
			table_updatetot(tmp[2]);
			xmlhttp_end="yes";
			window.clearTimeout(table_xml_timeout);
			document.getElementById("table_status_"+table_name).innerHTML=".";
			
      } 
    } 
}


function tablelist_load (name,repeat) { 

	if (xmlhttp_end=="no") {
		window.setTimeout("tablelist_load('"+name+"','"+repeat+"');",100);
		return;
	}

	xmlhttp_end="no";

	if (window.XMLHttpRequest) { 
		xmlhttp = new XMLHttpRequest(); 
	} else if (window.ActiveXObject) { 
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
	} else { 
		alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
		return; 
	}

	name2 = urlencode(name);
	table_name = name2;

	var str = "name="+name;
	var url = "/_engine/jx_tablelist.php";
	xmlhttp.onreadystatechange = tablelist_load_return;
    xmlhttp.open("POST",url,true);
    xmlhttp.setRequestHeader("Content-Type",
    "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp.send(str);
    
    if (repeat=="yes") {
    	window.setTimeout("tablelist_load('"+name+"','yes');",2000);
    }
    
} 

function tablelist_load_return() {
    var response;
    var lastresponse;
    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
			response = xmlhttp.responseText;
			eval("lastresponse = tablelist_"+table_name+"_lastresponse;");
			if (lastresponse != response) {
				document.getElementById("table_"+table_name).innerHTML = response;
				eval("tablelist_"+table_name+"_lastresponse = response;");
			}
			xmlhttp_end="yes";
      } 
    } 
}

function tablelist_showhide (name,id) {
	obj = document.getElementById ('tablelist_'+name+'_'+id+'_content');
	short = document.getElementById ('tablelist_'+name+'_'+id+'_short');
	
	//obj = document.getElementById ('tablelist_'+name+'_'+id+'_short');
	//div = document.getElementById ('tablelist_'+name+'_'+id+'_content');
	//txt = document.getElementById ('tablelist_'+name+'_'+id+'_text').innerHTML;
	//eval('short = tablelist_'+name+'_short['+id+'];');
	//alert(short);
	
	//if (div.style.display=='none') {
	//	div.innerHTML=
	//	obj.innerHTML='<br>\n'+txt;
	//} else {
	//	obj.innerHTML=short;
	//}
	
	if (obj.style.display=='none') {
		obj.style.display='';
		short.style.visibility='hidden';
	} else {
		obj.style.display='none';
		short.style.visibility='visible';
	}
}


function table_prev (name) {
	if (table_isfirst(name)) { return; }
	eval('var table_page = table_page_'+name+';');
	eval('var table_order = table_order_'+name+';');
	table_load(name,table_page-1,table_order);
	eval('table_page_'+name+'--;');	
	table_updatepage(name);
}

function table_next (name) {
	if (table_islast(name)) { return; }
	eval('var table_page = table_page_'+name+';');
	eval('var table_order = table_order_'+name+';');
	table_load(name,table_page+1,table_order);
	eval('table_page_'+name+'++;');	
	table_updatepage(name);
}

function table_last (name) {
	eval('var table_page = table_page_'+name+';');
	eval('var table_order = table_order_'+name+';');
	eval('var table_total = table_total_'+name+';');
	table_load(name,table_total,table_order);
	eval('table_page_'+name+'=table_total;');	
	table_updatepage(name);
}

function table_first (name) {
	eval('var table_page = table_page_'+name+';');
	eval('var table_order = table_order_'+name+';');
	table_load(name,1,table_order);
	eval('table_page_'+name+'=1;');	
	table_updatepage(name);
}

function table_islast(name) {
	eval('var table_page = table_page_'+name+';');
	eval('var table_total = table_total_'+name+';');
	return (table_page==table_total);
}

function table_isfirst (name) {
	eval('var table_page = table_page_'+name+';');
	return (table_page==1);
}
function table_updatepage (name) {
	eval('var table_page = table_page_'+name+';');
	document.getElementById('table_pagenum_'+name).innerHTML=table_page;
}
function table_updatetot (name) {
	document.getElementById('table_pagetot_'+name).innerHTML=eval('table_total_'+name);
}
function table_check (name,i,nocheck,action,id) {
	//alert(name+i);
	if (nocheck==1) {
		table_do_act(id,action);
		return;
	}
	
	if (!document.getElementById('table_check_'+name+'_'+i)) { return; }
	
	if (document.getElementById('table_check_'+name+'_'+i).checked==true) {
		document.getElementById('table_check_'+name+'_'+i).checked=false;
	} else {
		document.getElementById('table_check_'+name+'_'+i).checked=true;
	}
}

function table_checkall (name,obj) {
	eval('var perpage = table_perpage_'+name+';');
	eval('var page = table_page_'+name+';');
	eval('var showall = table_showall_'+name+';');
	eval('var total = table_itemtotal_'+name+';');
	
	var startat = (page-1) * perpage;
	
	if (showall==1) {
		perpage = total;
		page=1;
		//alert(perpage);
		startat=0;
	}


	if (obj.checked==true) {
		for (var i=(1+startat);i<=(startat+perpage);i++) {
			if (document.getElementById('table_check_'+name+'_'+i)) 
				document.getElementById('table_check_'+name+'_'+i).checked=true;
		}
		obj.checked=true;
	} else {
		for (var i=(1+startat);i<=(startat+perpage);i++) {
			if (document.getElementById('table_check_'+name+'_'+i))
			document.getElementById('table_check_'+name+'_'+i).checked=false;
		}
		obj.checked=false;
	}
}

function table_order(name,i) {
	eval('var order = table_order_'+name+';');
	eval('var showall = table_showall_'+name+';');
	if (order=="ASC "+i) {
		order = "DESC "+i;
	} else {
		order = "ASC "+i;
	} 
	if (showall==1) {
		var page=0;
	} else {
		var page=1;
	}
	table_load(name,page,order);
	eval('table_order_'+name+' = order;');

}

function table_showall (name) {
	eval('var table_showall = table_showall_'+name+';');
	eval('var table_order = table_order_'+name+';');

	if (table_showall==1) {
		table_load(name,1,table_order);
		eval('table_showall_'+name+'=0;');
		eval('table_page_'+name+'=1;');
		document.getElementById("table_paging_"+name).style.visibility='visible';
		document.getElementById("table_button_more_"+name).style.display='block';
		document.getElementById("table_button_less_"+name).style.display='none';

	} else {
		table_load(name,0,table_order);
		eval('table_showall_'+name+'=1;');	
		eval('table_page_'+name+'=0;');	
		document.getElementById("table_paging_"+name).style.visibility='hidden';
		document.getElementById("table_button_more_"+name).style.display='none';
		document.getElementById("table_button_less_"+name).style.display='block';
		
	}
	

}

function table_search(name){
	
	var search_value = document.getElementById('search_value_'+name).value;
	var search_field = document.getElementById('search_field_'+name).value;
	//eval('var search_field = document.getElementById(\'search_field_'+name+'");');
	eval('order = table_order_'+name+';');
	eval('table_search_value_'+name+' = search_value;');
	eval('table_search_field_'+name+' = search_field;');
	page = 1;
	
	table_load (name,page,order);
	
}

function table_show_all_contents(name){
	
	document.getElementById('search_value_'+name).value = '';
	eval('order = table_order_'+name+';');
	eval('table_search_value_'+name+' = search_value;');
	page = 1;
	
	table_search(name);
}

function table_do_act(id,action) {
	var refer = urlencode(this_page);
//	alert(this_page);
	location.href = "/_engine/act_link.php?refer="+refer+"&id="+id+"&action="+action;
}

function table_multiaction (name) {
	var values = document.getElementsByName('table_check_'+name);
	var action = document.getElementById("select_multiactions_"+name).value;
	
	var final=new String();
	for (i=0;i<values.length;i++) {
		if (values[i].checked) {
			final += values[i].value+"]n[";
		}
	}
	location.href = "/_engine/act_link.php?action="+action+"&id="+final;	
}

var explorer_id;

function explorer_load (url,id) {

	if (xmlhttp_end=="no") {
		window.setTimeout("explorer_load('"+url+"','"+id+"');",100);
		return;
	}

	xmlhttp_end="no";

	if (window.XMLHttpRequest) { 
		xmlhttp = new XMLHttpRequest(); 
	} else if (window.ActiveXObject) { 
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
	} else { 
		alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
		return; 
	}

	explorer_id = id;
	
	var str = "id="+id;

	xmlhttp.onreadystatechange = explorer_load_return;
    xmlhttp.open("POST",url,true);
    xmlhttp.setRequestHeader("Content-Type",
     "application/x-www-form-urlencoded; charset=UTF-8");
    xmlhttp.send(str);
    

    
} 

function explorer_load_return() {
    var response;
    var lastresponse;
    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
			response = xmlhttp.responseText;
			
			document.getElementById(explorer_id).innerHTML = response;
			
			xmlhttp_end="yes";
      } 
    } 
}