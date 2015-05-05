<?php


class customlogin {
	
	function getcustom () {
		$custom = @parse_ini_file(DIRSET."settings.ini",1);
		$custom = $custom[custom];
		if (is_array($custom)) {
			foreach ($custom as $k=>$v) {
				$custom[$k]=stripslashes($v);
			}
		}
		return $custom;
	}
	/** getmode
	 *  TRUE = ADVANCED
	 *  FALSE = BASIC
	 */
	function getmode() {
		$custom = @parse_ini_file(DIRSET."settings.ini",1);
		$custom = $custom[custom];
		if ($custom[advanced]==1) {
			return true;
		} else {
			return false;
		}
	}
	
	function getuploaded($theme="custom") {
		$i=0;
		if ($handle = opendir(DIRUSERTHEMES.$theme."/")) {
			while (false !== ($file = readdir($handle))) {
				if ($file=="."||$file=="..") { continue; }
				$filestat = stat(DIRUSERTHEMES.$theme."/".$file);
				$ret[$i][0]=$file;
				$ret[$i][1]=$filestat[7];
				$i++;
			}
		}		
		closedir($handle);
		
		return $ret;

	}
	function getthemes() {
		if ($handle = opendir(DIRTHEMES)) {
			while (false !== ($file = readdir($handle))) {
				if (substr($file,0,1)==".") { continue; }
				$list[$file]=$file;
			}
		}
		return $list;
	}
	
}


?>
