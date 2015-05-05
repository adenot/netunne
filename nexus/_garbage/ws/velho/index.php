<?
	function create_xml($vet, $sub=0){
		
		if(is_array($vet)||is_object($vet)){
			
			$aux = "";
			foreach($vet as $key=>$val){
				$aux.= sprintf("<%s>%s</%s>", $key, create_xml($val, $sub+1), $key);
			}
				
			
		}else{
			$aux = $vet;
		}
		
		if($sub){
			$ret = $aux;
		}else{
			$ret = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
			$ret.= "<return>";
			$ret.= $aux;
			$ret.= "</return>";
		}
		
		return $ret;
	}
	
	
	header("content-type: text/xml");
	echo create_xml($_SERVER);
?>