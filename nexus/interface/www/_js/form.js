    var xmlReq = null;;

    function sub(f,act)
    {
       var file = '/_engine/act.php?act='+act;
       var str = getFormValues(f,"validate");
       xmlReq = getXML(file,str);
    }

    function getXML(file,str)
    {

       var doc = null
       if (typeof window.ActiveXObject != 'undefined' )
       {
           doc = new ActiveXObject("Microsoft.XMLHTTP");
           doc.onreadystatechange = displayState;
       }
       else
       {
           doc = new XMLHttpRequest();
           doc.onload = displayState;
       }

       doc.open( "POST", file, true );
       doc.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");

       doc.send(str);
       return doc;

    }
     function getFormValues(fobj,valFunc)
    {

       var str = "";
       var valueArr = null;
       var val = "";
       var cmd = "";

       for(var i = 0;i < fobj.elements.length;i++)
       {
           switch(fobj.elements[i].type)
           {
               case "text":
                    if(valFunc)
                    {
                        //use single quotes for argument so that the value of
                        //fobj.elements[i].value is treated as a string not a literal
                        cmd = valFunc + "(" + 'fobj.elements[i].value' + ")";
                        val = eval(cmd)
                    }
                    str += fobj.elements[i].name +
                     "=" + escape(fobj.elements[i].value) + "&";
                     break;
               case "select-one":
                    str += fobj.elements[i].name +
                    "=" + fobj.elements[i].options[fobj.elements[i].selectedIndex].value + "&";
                    break;
           }
       }
       str = str.substr(0,(str.length - 1));
       return str;
    }   
    