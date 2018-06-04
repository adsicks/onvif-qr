<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require 'lib/class.ponvif.php';
define('AES_256_CBC', 'aes-256-cbc');
define('AES_256_GCM', 'aes-256-gcm');
// Change to user from authentication
$username="user"; 
//echo "Auth Code: ".htmlspecialchars($_GET["id"])."\n";
$auth=htmlspecialchars($_GET["id"]);
$ext=htmlspecialchars($_GET["file"]);
//echo  "Auth Code: ".$auth."\n";
$dec=decodeKey($auth);
//echo "Tag: ".$dec['tag']."\n";
//echo "Key: ".$dec['data']."\n";
//echo "tag ".hex2bin($dec['tag'])." data ".hex2bin($dec['data'])."\n";
$decrypt=decryptKey($dec);
//echo "Decrypted Key: ".bin2hex($decrypt)."\n";

// from here decrypt record
	$filename="/home/www/auth.zwebusa.com/qr/.out.";
	$filename.=$ext;
	
	
		 $file = fopen( $filename, "r" );
	 
		if( $file == false ) {
			echo ( "Error in opening new file" );
			exit();
		}
		$rec=fread( $file,filesize($filename));
		fclose( $file );

	
//echo "Record: ".$rec."\n";

$dec_rec=decryptRecord(substr($rec,4), $decrypt);
$data=decodeRecord($dec_rec);

if($data['ip']=='') die("Error!!!");
echo "Opening Camera ".$data['desc']." for reading</br>\n";
$test=new ponvif();

$test->setUsername($data['uname']);
$test->setPassword($data['pwd']);
$test->setIPAddress($data['ip']); 

try {
		$test->initialize();
		
		$profileToken=$_POST["token"];
		$mediaUri=$test->media_GetStreamUri($profileToken);
		echo $mediaUri;

	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
function decodeKey($auth){
	$l1=hexdec(substr($auth,0,2));
	$tag = substr($auth,2,$l1);
	$key= substr($auth,2+$l1);
	return array('tag' => $tag, 'data' => $key);
}

function decryptKey($key){
	global $username;

	$hash = hash("sha512", $username);
	$iv = substr($hash, 0, 32);
	$aad = substr($hash, 32);
//	echo "hash ".$hash."\n";
	$decrypted = openssl_decrypt(hex2bin($key['data']), AES_256_GCM, $hash, 0, hex2bin($iv), hex2bin($key['tag']), $aad);
	return $decrypted;

}

function decryptRecord($rec, $key){
		$l1 = hexdec(substr($rec,0,4));
	//	echo "Len iv: ".$l1."\n";
		$iv = base64_decode(substr($rec, 4, $l1));
		$data = substr($rec, 4+$l1);
		
		$decrypted = openssl_decrypt($data, AES_256_CBC, $key, 0, $iv);
		return $decrypted;
		
	}
	
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

?>
