<?php

require_once('lib/auth-class.php');
// set up class 
$auth = new Zauth;
$auth->realm='Z Web Auth Server';
$auth->users = array('admin' => 'mypass', 'guest' => 'guest');
$auth->load_data();

// Check if any auth info yet
if($auth->IsServerEmpty()) $auth->noLogin();
if($auth->IsWrongCredentials()) die('Wrong Credentials');
if($auth->IsValidResponse())  echo "You are logged in."; else$auth->logout_bad_credentials('Bad Credentials');
// var_dump($auth->IsValidResponse());

?>
