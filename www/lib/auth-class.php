<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
// Class to authenticate auth.zwebusa.com and entire zwebusa.com websites

class Zauth{
	function load_data(){
		$this->data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
	}
	
	public $realm;
	public $users; // read these pairs from database
	protected $data;
	
	function noLogin(){ 
		    header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$this->realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($this->realm).'"');
			die('Text to send if user hits Cancel button');
	}
	
	function IsServerEmpty(){ return (empty($_SERVER['PHP_AUTH_DIGEST']));}
	function IsWrongCredentials(){
		return (!($this->data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
		!isset($this->users[$this->data['username']]));}
	function IsValidResponse(){
	//	var_dump($this->data['response']);
	//	var_dump( $this->valid_response());
		return ($this->data['response'] == $this->valid_response());}
	function http_digest_parse($txt){
		// protect against missing data
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$this->data = array();
		$keys = implode('|', array_keys($needed_parts));

		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

		foreach ($matches as $m) {
			$this->data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}

		return $needed_parts ? false : $this->data;
	}

	function logout_bad_credentials($msg){
		header('HTTP/1.1 401 Unauthorized');
		die($msg.' -- Logged out');
	}
	
	// Generate a Valid Response
	
	function valid_response(){
		$A1 = md5($this->data['username'] . ':' . $this->realm . ':' . $this->users[$this->data['username']]);
		$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$this->data['uri']);
		return md5($A1.':'.$this->data['nonce'].':'.$this->data['nc'].':'.$this->data['cnonce'].':'.$this->data['qop'].':'.$A2);
	}
	
	function getUsername(){return $this->data['username'];}
}	

// set up class 
$auth = new Zauth;
$auth->realm='Z Web Auth Server';
$auth->users = array('admin' => 'mypass', 'guest' => 'guest', 'user' => 'password'); //TODO: read from database or external source
$auth->load_data();

// Check if any auth info yet
if($auth->IsServerEmpty()) $auth->noLogin();
if($auth->IsWrongCredentials()) die('Wrong Credentials');
if($auth->IsValidResponse())  echo "You are logged in as ".$auth->getUsername()."\n"; else $auth->logout_bad_credentials('Bad Credentials');
// var_dump($auth->IsValidResponse());
?>

