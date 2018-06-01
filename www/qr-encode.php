<?php

	require 'lib/class.qr.php';


$qr=new QRonvif();
$qr->setDescription($_POST['desc']);
$qr->setIpAddr($_POST['ip']);
$qr->setCamUser($_POST['uname']);
$qr->setCamPass($_POST['pwd']);

$qr->createRecord()."\n";
$result = $qr->encryptRecord('');
//echo "Encrypt Record:".$result['data']."\nKey: ".bin2hex($result['key'])."\n";
//echo "Length: ".strlen($result['data'])."\n";
$record_out=dec2padhex(strlen($result['data']),4);
$record_out.=$result['data'];
$uuid=writeRecord($record_out);
//echo "Record to write: ".$record_out."\n";
$result = $qr->encryptKey('');
//echo "Encrypt Key: ".bin2hex($result['data'])."\ntag: ".bin2hex($result['tag'])."\n";
echo "https://auth.zwebusa.com/qr.php?id=".dec2padhex(strlen(bin2hex($result['tag'])),2).bin2hex($result['tag']). bin2hex($result['data'])."\n";
$url=urlencode("https://auth.zwebusa.com/qr-decode.php?id=".dec2padhex(strlen(bin2hex($result['tag'])),2).bin2hex($result['tag']).bin2hex($result['data'])."&file=".$uuid);
echo "Link: https://auth.zwebusa.com/qr-decode.php?id=".dec2padhex(strlen(bin2hex($result['tag'])),2).bin2hex($result['tag']).bin2hex($result['data'])."&file=".$uuid."<br/>\n";
echo $url."\n";
echo "<img src=\"http://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$url\" />";
// writes the record file, returns the uuid extension
function writeRecord($rec){
	$ext=uniqid();
	$filename="/home/www/auth.zwebusa.com/qr/.out.";
	$filename.=$ext;
	if(!file_exists($filename)){
		 $file = fopen( $filename, "w" );
	 
		if( $file == false ) {
			echo ( "Error in opening new file" );
			exit();
		}
		fwrite( $file, $rec );
		fclose( $file );
	}else{
		// regen uuid!?!?	    
		die("wtf!!!");
	}
   return $ext;
}

?>
