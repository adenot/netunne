<?php
	if (file_exists("/tmp/inst.log")) {
		$tmp = explode("\n",trim(file_get_contents("/tmp/inst.log")));
		$tmp = array_pop($tmp);
		echo $tmp;
	}
?>
