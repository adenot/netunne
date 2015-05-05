<?php

echo trim(shell_exec("date"))."\n";
if ($_GET["if"]) {
	
	$_GET["if"]=escapeshellcmd($_GET["if"]);
	echo trim(shell_exec("grep ".$_GET["if"]." /proc/net/dev"));
} else if ($_GET["mem"]) {

	$results['ram'] = array('total' => 0, 'free' => 0, 'used' => 0, 'percent' => 0);
	
	$bufr = trim(shell_exec("head -n 4 /proc/meminfo"));
	if ( $bufr != "" ) {
		$bufe = explode("\n", $bufr);
		foreach( $bufe as $buf ) {
			if (preg_match('/^MemTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
			$results['ram']['total'] = $ar_buf[1];
			} else if (preg_match('/^MemFree:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
					$results['ram']['free'] = $ar_buf[1];
			} else if (preg_match('/^Cached:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
					$results['ram']['cached'] = $ar_buf[1];
			} else if (preg_match('/^Buffers:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
					$results['ram']['buffers'] = $ar_buf[1];
			}
		}
		$results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
		$results['ram']['percent'] = round(($results['ram']['used'] * 100) / $results['ram']['total']);
	}
	echo $results['ram']['percent'];

	
	
	
} else {
	echo trim(shell_exec("head -n 1 /proc/stat"));
}

?>
