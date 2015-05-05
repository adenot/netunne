<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 02/07/2006					*
	*																*
	****************************************************************/

class update {
	function getlist () {
		$npak = new Npak();
		return $npak->parselist();
		
		
	}
	/*
		$nlist=@file_get_contents(DIRSET."npak.list");
	
	//	print_r($nlist);
		if ($nlist==FALSE) { return 0; }
	
		$nlist = explode("\n\n",$nlist);
		for ($i=0;$i<count($nlist);$i++) {
			// cada pacote tem um espaco de 2 enters entre eles
			$plist=explode("\n",$nlist[$i]);
			for ($j=0;$j<count($plist);$j++) {
				if (trim($plist[$j])=="") { continue; }
				$temp = explode(":",$plist[$j]);
				$temp[1] = str_replace($temp[0].":","",$plist[$j]);
				if ($ret[$i][$temp[0]]) {
					$ret[$i][$temp[0]].="\n".trim($temp[1]);
				} else {
					$ret[$i][$temp[0]]=trim($temp[1]);
				}
			}
		}
		return $ret;
	}
	*/
	function gettable() {
		$list = update::getlist();
		$list = $list[npak];
		$n=0;
		$newlist = array();
		//echo "PRODID".PRODID;
		for ($i=0;$i<count($list);$i++) {
			//if (intval(trim($list[$i]["Product"]))!=PRODID) { continue; }
			$newlist[$n][0]=$list[$i]["Package"];
			$newlist[$n][1]=$list[$i]["Description"];
			$newlist[$n][2]=$list[$i]["Version"];
			$n++;
			
		}
		return $newlist;
	}
		
}













?>
