<?php

// class.qr.php
// 05/31/2018
// JPZ -- Initial release and concepts


require_once 'zlib.php';
// DEFINE our ciphers
define('AES_256_CBC', 'aes-256-cbc');
define('AES_256_GCM', 'aes-256-gcm');

// Class to encode, encrypt, decode, and decrypt onvif camerial credentials 
// And transfer a key to the client as a qr code representing an encrypted 
// key and tag for a GCM encryption scheme. A the username for the webserver 
// (not the camera) is used for the encryption key to encrypt the record key.
// The username can be salted for enhanced security.

class QRonvif {
	protected $description='';
	protected $ip_addr='';
	protected $cam_user='';
	protected $cam_pass='';
	protected $record='';
	protected $encrypted_record='';
	protected $key1='';
	
	public function getDescription() {return $this->description;}
	public function setDescription($Desc){$this->description = $Desc;}
	public function getIpAddr() {return $this->ip_addr;}
	public function setIpAddr($IpAddr){$this->ip_addr = $IpAddr;}
	public function getCamUser() {return $this->cam_user;}
	public function setCamUser($CamUser){$this->cam_user=$CamUser;}
	public function getCamPass(){return $this-cam_pass;}
	public function setCamPass($CamPass){$this->cam_pass=$CamPass;}
	
	/*
		Constructor & Destructor
	*/
	public function __construct()
	{
		// nothing to do
	}
	public function __destruct()
	{
		// nothing to do
	}
// Creates an encoded record with the human description, ip address, and username address pair, ready to be encrypted
// TODO: work off of class memebers instead of passed parameters
	
	public function createRecord(){
		$ret=dec2padhex(strlen($this->description),2).$this->description.dec2padhex(strlen($this->cam_user),2).$this->cam_user.dec2padhex(strlen($this->cam_pass),2).$this->cam_pass.ip2hex($this->ip_addr);
		$this->record = $ret;
		return $ret;
	}


	// decodeRecord
	// decodes record encoded by createRecord
function decodeRecord($p_dat){
		$l1=hexdec(substr($p_dat,0,2));
		$f1=substr($p_dat,2,$l1);
		$l2=hexdec(substr($p_dat,2+$l1,2));
		$f2=substr($p_dat,4+$l1,$l2);
		$l3=hexdec(substr($p_dat,4+$l1+$l2,2));
		$f3=substr($p_dat,6+$l1+$l2,$l3);
		$f4=substr($p_dat,6+$l1+$l2+$l3);
//		echo "Len of first field is: ".$l1." and the data is: ".$f1."\n";
//		echo "Len of 2nd field is: ".$l2." and the data is: ". $f2."\n";
//		echo "Len of 3rd field is: ".$l3." and the data is: ".$f3."\n";
//		echo "The values of the last field is: ".$f4."\n";
		$f5=substr($f4,8);
//		echo "Port is ". $f5."\n";
		$f4=implode('.', array(hexdec(substr($f4,0,2)),  hexdec(substr($f4,2,2)) , hexdec(substr($f4,4,2)) ,hexdec(substr($f4,6,2))));
//		echo "The ip addr is: ". $f4."\n";
		if(!$f5=='') $f4.=":$f5";
		return array("desc" =>  $f1, "uname" =>  $f2, "pwd" =>  $f3, "ip" =>  $f4); // match to form

}

	// encrypts a Record created by creatRecord. Returns an array with the encrypted record and the key
	// encrypted record format
	// 00 - 03 length of iv
	// 04 -len iv
	// len-eor data
	public function encryptRecord($rec){
		if($rec=='') $rec=$this->record;
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
		$encryption_key1 = openssl_random_pseudo_bytes(32);
		$encrypted = openssl_encrypt($rec, AES_256_CBC, $encryption_key1, 0, $iv);
		
		$iv = base64_encode($iv);
//		echo "iv: ". $iv."\n";
//		echo "data: ".$encrypted."\n";
		$encrypted = dec2padhex(strlen($iv),4).$iv.$encrypted;
		$this->encrypted_record=$encrypted;
		$this->key1=$encryption_key1;
		return array('data' => $encrypted, 'key' => $encryption_key1);
	}

	// Decrypts a record encrypted by encryptRecord. Accepts the encrypted record and the key as parameters
	// Returns the decrypted record
	function decryptRecord($rec, $key){
		$l1 = hexdec(substr($rec,0,4));
//		echo "Len iv: ".$l1."\n";
		$iv = base64_decode(substr($rec, 4, $l1));
		$data = substr($rec, 4+$l1);
		
		$decrypted = openssl_decrypt($data, AES_256_CBC, $key, 0, $iv);
		return $decrypted;
		
	}

	// encrypts an encryption key for transport over the Internet as a QR code
	// Uses GCM encryption.
	// Returns the encrypted key and the tag in an array
	// Salt the username when it is hashed for extra security (not done in development).
	function encryptKey($key){
		if($key=='') $key=$this->key1;
		global $username;
		$username="user";
		$hash = hash("sha512", $username);
		$iv = substr($hash, 0, 32);
		$aad = substr($hash, 32);
		$encryption_key2 = openssl_random_pseudo_bytes(32);
		$encrypted = openssl_encrypt($key, AES_256_GCM, $hash, 0, hex2bin($iv), $tag, $aad, 16);
		return  array('data' => $encrypted, 'tag' => $tag);	
		
	}

	// decrypts the encryption key encrypted by encryptKey
	// takes the array returned by encryptKey as the parameter
	// Returns the decrypted key
	// Use same salt here as in encryptKey if salting the username
	function decryptKey($key){
		global $username;
		$username="user";
		$hash = hash("sha512", $username);
		$iv = substr($hash, 0, 32);
		$aad = substr($hash, 32);
//		echo "hash ". $hash."\n";
		// $decrypted = openssl_decrypt($key['data'], AES_256_GCM, $hash, 0, hex2bin($iv), $key['tag'], $aad);
		$decrypted = openssl_decrypt(hex2bin($key['data']), AES_256_GCM, $hash, 0, hex2bin($iv), hex2bin($key['tag']), $aad);
		return $decrypted;
	}
	
	function decodeKey($auth){
		$l1=hexdec(substr($auth,0,2));
		$tag = substr($auth,2,$l1);
		$key= substr($auth,2+$l1);
		return array('tag' => $tag, 'data' => $key);
	}
	
}
?>
