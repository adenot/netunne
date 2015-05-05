<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 19, 2006					*
	*																*
	****************************************************************/

class server {
	
	function getevents($textonly=0) {
	
		
		if (file_exists(DIRDATA."events.ser")) {
			$err = unserialize(file_get_contents(DIRDATA."events.ser"));
		} else { $err = array(); }
		
		if (is_array($err)&&count($err)!=0) {
			
			/*
			$err2 = array();
			$d=0;
			for ($i=count($err)-1;$i>=0;$i--) {
				unset($done);
				for ($j=0;$j<count($err2);$j++) {
					if ($err2[$j][desc]==$err[$i][desc]) {
						$err2[$j][count]++;
						$done=1;
					} 
				} 
				if (!$done) {
					$d++;
					$err2[$d]=$err[$i];
					$err2[$d][count]=1;
				}
			}
			*/
			
			$err2=array();
			$d=0;
			for ($i=count($err)-1;$i>=0;$i--) {
				$done="no";
				for ($j=0;$j<count($err2);$j++) {
					if ($err2[$j][desc]==$err[$i][desc]) {
						$done="yes";
						$err2[$j][count]++;
						break;
					}
				}
				if ($done=="no") {
					$err2[$d]=$err[$i];
					$err2[$d][count]=1;
					$d++;
				}
			}
			$err3 = array();
			$d=0;
			for ($i=0;$i<count($err2);$i++) {
				$err3[$d][title]=$err2[$i][title]." (".$err2[$i][count].")";
				$err3[$d][desc]="[".conv::formatdate($err2[$i][time])."]\n ".$err2[$i][desc];
				
				$errtxt[$d]=conv::formatdate($err2[$i][time])." ".$err2[$i][title];
				
				$d++;
			}	
			
			/*
			// agora preciso inverter e anexar ao texto a contagem e data
			$err3 = array();
			$d=0;
			for ($i=1;$i<=count($err2);$i++) {
				$err3[$d][title]=$err2[$i][title]." (".$err2[$i][count].")";
				$err3[$d][desc]="[".conv::formatdate($err2[$i][time])."]\n ".$err2[$i][desc];
				$d++;
			}
			*/
			
			if ($textonly==1) 
				return implode("\n",$errtxt);
			else
				return $err3;
			
		}
		return false;

	}


	function cpu_info () {
		$bufr = trim(shell_exec( 'cat /proc/cpuinfo' ));
		$results = array("cpus" => 0);
		
		if ( $bufr != "" ) {
			$bufe = explode("\n", $bufr);
			
			$results = array('cpus' => 0, 'bogomips' => 0);
			$ar_buf = array();
			
			foreach( $bufe as $buf ) {
				$arrBuff = preg_split('/\s+:\s+/', trim($buf));
				if( count( $arrBuff ) == 2 ) {
					$key = $arrBuff[0];
					$value = $arrBuff[1];
					// All of the tags here are highly architecture dependant.
					// the only way I could reconstruct them for machines I don't
					// have is to browse the kernel source.  So if your arch isn't
					// supported, tell me you want it written in.
					switch ($key) {
						case 'model name':
							$results['model'] = $value;
							break;
						case 'cpu MHz':
							$results['cpuspeed'] = sprintf('%.2f', $value);
							break;
						case 'cycle frequency [Hz]': // For Alpha arch - 2.2.x
							$results['cpuspeed'] = sprintf('%.2f', $value / 1000000);
							break;
						case 'clock': // For PPC arch (damn borked POS)
							$results['cpuspeed'] = sprintf('%.2f', $value);
							break;
						case 'cpu': // For PPC arch (damn borked POS)
							$results['model'] = $value;
							break;
						case 'L2 cache': // More for PPC
							$results['cache'] = $value;
							break;
						case 'revision': // For PPC arch (damn borked POS)
							$results['model'] .= ' ( rev: ' . $value . ')';
							break;
						case 'cpu model': // For Alpha arch - 2.2.x
							$results['model'] .= ' (' . $value . ')';
							break;
						case 'cache size':
							$results['cache'] = $value;
							break;
						case 'bogomips':
							$results['bogomips'] += $value;
							break;
						case 'BogoMIPS': // For alpha arch - 2.2.x
							$results['bogomips'] += $value;
							break;
						case 'BogoMips': // For sparc arch
							$results['bogomips'] += $value;
							break;
						case 'cpus detected': // For Alpha arch - 2.2.x
							$results['cpus'] += $value;
							break;
						case 'system type': // Alpha arch - 2.2.x
							$results['model'] .= ', ' . $value . ' ';
							break;
						case 'platform string': // Alpha arch - 2.2.x
							$results['model'] .= ' (' . $value . ')';
							break;
						case 'processor':
							$results['cpus'] += 1;
							break;
						case 'Cpu0ClkTck': // Linux sparc64
							$results['cpuspeed'] = sprintf('%.2f', hexdec($value) / 1000000);
							break;
						case 'Cpu0Bogo': // Linux sparc64 & sparc32
							$results['bogomips'] = $value;
							break;
						case 'ncpus probed': // Linux sparc64 & sparc32
							$results['cpus'] = $value;
							break;
		 			}
				}
			}
		}		
		$keys = array_keys($results);
		$keys2be = array('model', 'cpuspeed', 'cache', 'bogomips', 'cpus');
		
		while ($ar_buf = each($keys2be)) {
			if (! in_array($ar_buf[1], $keys)) {
				$results[$ar_buf[1]] = 'N.A.';
			} 
		}
		
		$buf = trim(shell_exec( 'head -n 1 /proc/acpi/thermal_zone/THRM/temperature'));
		if ( $buf != "" ) {
			$results['temp'] = substr( $buf, 25, 2 );
		}
		
		return $results;
	}
	
	function mem_total () {
		
		$buf = trim(shell_exec("head -n 1 /proc/meminfo"));
		if (preg_match('/^MemTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
			return $ar_buf[1];
		}
		
	}
	
	function gethwinfo () {
		/*
			Array
			(
			    [cpus] => 1
			    [bogomips] => 4057,02
			    [model] => AMD Athlon(tm) 64 Processor 3000+
			    [cpuspeed] => 2010,95
			    [cache] => 512 KB
			)
		*/
		$ret[cpu] = server::cpu_info();
		$ret[mem] = round(server::mem_total() / 1024);
		return $ret;
		
	}
}

?>
