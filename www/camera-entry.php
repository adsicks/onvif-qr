<?php require_once('auth-lib/src/auth-class.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Camera Key Creator</title>

</head>
<body id="main_body" >
	
	<img id="top" src="images/top.png" alt="">
	<div id="form_container">
	
		<h1><a>Create a QR Code</a></h1>
		<form method="post" action="qr-encode.php">
					<div class="form_description">
			<h2>Camera Key Creator</h2>
			<p>Enter camera info to create a link for a user.</p>
		</div>						
			
		<label class="description" for="desc">Description </label>
		<div>
			<input id="element_1" name="desc" class="element text medium" type="text" maxlength="255" value=""/> 
		</div>
		<label class="description" for="ip">ip </label>
		<div>
			<input id="element_2" name="ip" class="element text medium" type="text" maxlength="255" value=""/> 
		</div> 
		
		<label class="description" for="uname">camera user name </label>
		<div>
			<input id="element_3" name="uname" class="element text medium" type="text" maxlength="255" value=""/> 
		</div> 
		
		<label class="description" for="pwd">camera password </label>
		<div>
			<input id="element_4" name="pwd" class="element text medium" type="text" maxlength="255" value=""/> 
		</div> 
		
		<label class="description" for="user">client username </label>
		<div>
			<input id="element_5" name="user" class="element text medium" <?if($auth->getUsername()=="admin") echo "type=\"text\" value=\"\""; else echo "type=\"hidden\" value=\"".$auth->getUsername()."\""; ?> maxlength="255" /> 
		</div> 
			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		
		</form>	
		
	</body>
</html>
