<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');
require 'lib/class.ponvif.php';
require 'lib/class.qr.php';


$qr=new QRonvif();

$username="user";
//echo "Auth Code: ".htmlspecialchars($_GET["id"])."\n";
$auth=htmlspecialchars($_GET["id"]);
$ext=htmlspecialchars($_GET["file"]);
//echo  "Auth Code: ".$auth."\n";
$dec=$qr->decodeKey($auth);
echo "Tag: ".$dec['tag']."\n";
echo "Key: ".$dec['data']."\n";
echo "tag ".hex2bin($dec['tag'])." data ".hex2bin($dec['data'])."\n";
$decrypt=$qr->decryptKey($dec);
//echo "Decrypted Key: ".bin2hex($decrypt)."\n";

// from here decrypt record
	$filename="/home/www/auth.zwebusa.com/qr/.out.";
	$filename.=$ext;
	
	
		 $file = fopen( $filename, "r" );
	 
		if( $file == false ) {
			//echo ( "Error in opening new file" );
			exit();
		}
		$rec=fread( $file,filesize($filename));
		fclose( $file );

	
//echo "Record: ".$rec."\n";

$dec_rec=$qr->decryptRecord(substr($rec,4), $decrypt);
$data=$qr->decodeRecord($dec_rec);
var_dump($data);

if($data['ip']=='') die("Error!!!");
echo "<h1>Select a stream from ".$data['desc']." to view link</h1>\n";
$test=new ponvif();

$test->setUsername($data['uname']);
$test->setPassword($data['pwd']);
$test->setIPAddress($data['ip']); 

	$test->initialize();

		if ($test->isFault($sources=$test->getSources())) die("Error getting sources");
	//	echo "	last soap response\n";
		//var_dump($test->getLastResponse());
		
		echo "\n<BR><BR>";
	        $profileToken=$sources[0][0]['profiletoken'];
	        
	        echo "<form action=\"streams.php?id=$auth&file=$ext\" method=\"post\"><select name=\"token\">";
	        $x=0;
		foreach($sources[0] as $tokens){
			
			if(isset($tokens['profiletoken'])){
			echo "<option value=\"".$tokens['profiletoken']."\">".$tokens['profilename']."</option>";
			$x=$x+1;
			}
			//var_dump($tokens);
			
		}
		echo "<input type=\"hidden\" name=\"camera\" value=\"".$_POST["camera"]."\"><input type=\"submit\"></select></form>";        
       

	

?>
