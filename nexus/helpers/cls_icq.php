k<?php

class Icq {
	
	public $user;
	public $pass;
	
	public $host = "login.oscar.aol.com";
	public $port = 5190;
	
	private $fd;
	
	function __construct ($user,$pass) {
		
		$this->user = $user;
		$this->pass = $pass;
		
		define (DEBUG,true);
		
	}
	
	// UTILITY
	private function log ($txt) {
		if (DEBUG) { echo $txt."\n";}
	}
	
	
	private function hexn ($var,$size) {
		$size = $size * 2;
		$var = sprintf("%0".$size."X",$var);
		//echo $var."\n";
		return $var;		
	}
	private function FLAP_random_datagram() {
		return rand(0,32768);
	}
	
	// LOW LEVEL FUNCTIONS
	
	private function FLAP ($channel, $datagram, $datasize, $data) {
		/* CHANNEL (byte)
		 * 		0x01 - New Connection Negotiation
		 * 		0x02 - SNAC data
		 * 		0x03 - FLAP-level Error
		 * 		0x04 - Close Connection Negotiation
		 * 		0x05 - Keep alive
		 * DATAGRAM (word-2bytes)
		 * 		aleatorio
		 * DATASIZE (word)
		 * 		tamanho em bytes do data
		 * DATA
		 * 		dados ou um SNAC
		 * 
		 */
		 $channel = $this->hexn($channel,1);
		 $datagram = $this->hexn($datagram,2);
		 $datasize = $this->hexn($datasize,2);
		 
		 return "2A".$channel.$datagram.$datasize.$data;

	}
	// SNAC vai dentro de um FLAP, e o channel do FLAP eh "02"
	private function SNAC ($family,$subtype, $flags,$requestid, $data) {
		// word / word / word / dword-4bytes
		$family = $this->hexn($family,2);
		$subtype = $this->hexn($subtype,2);
		$flags = $this->hexn($flags,2);
		$requestid = $this->hexn($requestid,4);
		
		return $family.$subtype.$flags.$requestid.$data;
		
	}
	private function TLV ($type, $data="") {
		// word / word

		$lenght = $this->hexn(strlen($data)/2,2);

		$type = $this->hexn($type,2);

		
		return $type.$lenght.$data;
		
	}
	
	// LOGIN SUBFUNCTIONS
	private function login_01_new_connection () {
		
		$pkt = $this->FLAP(0x01,$this->FLAP_random_datagram(),4,"00000000");
		return $pkt;
		
	}
	
	private function login_01_request_key () {
		$tlvs = $this->TLV(0x01,bin2hex($this->user));
		$tlvs.= $this->TLV(0x02,bin2hex($this->pass));
		$tlvs.= $this->TLV(0x03,bin2hex("ICQ Inc. - Product of ICQ (TM).2000b.4.65.1.3281.85"));
		$tlvs.= $this->TLV(0x16,"010A");
		$tlvs.= $this->TLV(0x17,"0004");
		$tlvs.= $this->TLV(0x18,"0041");
		$tlvs.= $this->TLV(0x19,"0001");
		$tlvs.= $this->TLV(0x1A,"0CD1");
		$tlvs.= $this->TLV(0x14,"00000055");
		$tlvs.= $this->TLV(0x0F,"656E");
		$tlvs.= $this->TLV(0x0E,"7573");
		
		
		// SNAC(17,06)
		//$snac = $this->SNAC(0x17,0x06,0x00,0x00,$tlvs);
		//$snac_size = strlen($snac) / 2;
		
		
		$pkt = $this->FLAP(0x01,$this->FLAP_random_datagram(),strlen($tlvs)/2,$tlvs);
		
		return $pkt;
	}
	
	private function send_pkt($data) {
		if (DEBUG) { $this->log("Sending: |$data|"); }
		$c="";
		for ($i=0;$i<strlen($data);$i=$i+2) {
			eval("\$c .= sprintf(\"%c\",0x".$data[$i].$data[$i+1].");");
			
		}
		//if (DEBUG) { $this->log("Sending (chared): |$c|"); }
		
		fwrite($this->fd,$c);
		
	}
	private function open_pkt($data) {
		return strtoupper(bin2hex($data));
		
	}
	
	// ACT 1: CONNECT AND LOGIN
	
	private function connect () {
		$this->fd = fsockopen($this->host,$this->port,$errno, $errstr, 30);
		if (!$this->fd) {
    		echo "$errstr ($errno)\n";
    		return false;
		}
		
		$this->log("Connected!");
		
		return true;
	}
	private function is_connected()  {
		if ($this->fd) return true; else return false; 
	}
	
	public function login() {
		if (!$this->is_connected()) {
			if (!$this->connect()) {return false;}
		}
		

		$pkt = $this->login_01_request_key();
		
		$this->send_pkt($pkt);
		//return;
		
		do {
			echo $this->open_pkt(fgets ( $this->fd, 4096 ));
		} while (feof($this->fd));
		
		
	}
	
	

	
}

$icq = new Icq ("352550525","robot01");
$icq->login();


?>
