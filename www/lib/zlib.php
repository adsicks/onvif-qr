<?php
// Some worker functions outside of class

// ip2hex($ip_addr)
// $ip_addr = ip address to encode
// encodes an ip address as 4 hex numbers

function ip2hex($ip_addr){
	$ret='';
	    if(strstr($ip_addr, ':')){
			$i = explode(':', $ip_addr);
			$ip_addr=$i[0];
			$port_addr=$i[1];
			$i='';
		}
		$i=explode('.', $ip_addr);
		foreach($i as $value){
			// $value=dechex($value);
			// $ret.=str_pad($value,2,'0',STR_PAD_LEFT);
			$ret.=dec2padhex($value,2);
		}
		unset($value);
		$ret.=$port_addr;
		return $ret;
}

// dec2padhex($d, $p)
// $d= a decimal number
// $p = length of pad
// returns a hexadecimal number padded with $p zeros
	
function dec2padhex($d, $p){
	if($p =='') $p=2; 
	return str_pad(dechex($d), $p, '0', STR_PAD_LEFT);
}
?>
