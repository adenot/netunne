var xmlhttp; 

function loadbody(page_request) { 

   if (window.XMLHttpRequest) { 
      xmlhttp = new XMLHttpRequest(); 
   } else if (window.ActiveXObject) { 
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); 
   } else { 
       alert("Seu navegador n&atilde;o suporta XMLHttpRequest."); 
      return; 
   } 

    xmlhttp.open("GET", page_request, true); 
    xmlhttp.onreadystatechange = processReqChange; 
    xmlhttp.send(null); 
} 

function processReqChange() {
    var entrada;

    if (xmlhttp.readyState == 4) { 
       if (xmlhttp.status == 200) {
           entrada = xmlhttp.responseText; 

	   document.getElementById("body").innerHTML = entrada;

      } 
    } 
}