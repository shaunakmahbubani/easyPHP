<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

$name = $username = $email = $handle = $password = $confirmpass = $fail = $failmessage = "";


if( isset($_GET['validate'])) {

if (isset($_POST['name']))
	$name = sanitizeString($_POST['name']);
if (isset($_POST['email']))
	$email = sanitizeString($_POST['email']);
if (isset($_POST['handle']))
	$handle = sanitizeString($_POST['handle']);
if (isset($_POST['password']))
	$password = sanitizeString($_POST['password']);
if (isset($_POST['confirmpass']))
	$confirmpass = sanitizeString($_POST['confirmpass']);

	$fail = validate_name($name);
	$fail .= validate_email($email);
	$fail .= validate_handle($handle);
	$fail .= validate_password($password);
	$fail .= validate_confirmpass($password,$confirmpass);
	
}

	if($fail == "" && isset($_GET['validate'])) {

		$salt1 = "g^8p";
		$salt2 = "jd#4l";

		$saltedpassword = md5("$salt1$password$salt2");
		
		$query = "Insert into users(username, handle, email, bio, password, inspcol) values('$name','$handle','$email','','$saltedpassword',0)";
		mysql_query($query);
		$userid = mysql_insert_id();
		
		session_start();
		$_SESSION['userid'] = $userid;
		$_SESSION['name'] = $name;
		$_SESSION['handle'] = $handle;
		$_SESSION['inspcol'] = 0;
		
		mkdir("./images/$userid");

		$query = "Insert into follow values('$userid','$userid')";
		mysql_query($query);
		
		header("Location: profile.php");
	}
	
	if($fail != "" && isset($_GET['validate'])) {
		
		$failmessage = "The following errors were found: <br/ > <ul>";
	}


echo <<<_END
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
    <LINK REL=StyleSheet HREF="signup.css" TYPE="text/css">
    <script type="text/JavaScript" src="jquery.js"></script>
    <script>
	var namevalid=true;
	var emailvalid=true;
	var handlevalid=true;
	var passwordvalid=true;
	var conpasswordvalid=true;

	function validate(form) {
		return (namevalid && emailvalid && handlevalid && passwordvalid && conpasswordvalid);
	}

	function validatename(field) {
		namevalid=true;
		if (field == "") { $("#validatename").html("Please Enter your Name"); namevalid = false;}
		else $("#validatename").html("");
	}

	function validateemail(field) {
		emailvalid=true;
		if (field == "") { $("#validateemail").html("Please Enter your Email Address"); emailvalid = false;}
		else if (!((field.indexOf(".") > 0) && (field.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test(field))
			{ $("#validateemail").html("Please Enter a valid Email Address"); emailvalid = false;}
		else $("#validateemail").html("");
	}

	function validatehandle(field) {
		handlevalid=true;
		if (field == "") { $("#validatehandle").html("Please Enter a Username"); handlevalid = false; return}
		else if (field.length < 5) { $("#validatehandle").html("Username must be atleast 5 characters"); handlevalid = false; return}
		else if (/[^a-zA-Z0-9_-]/.test(field)) { $("#validatehandle").html("Only letters, numbers, - and _ in usernames"); handlevalid = false; return}
		$("#validatehandle").load("checkhandle.php?view=" + field);
	}

	function validatepassword(field) {
		passwordvalid=true;
		if (field == "") { $("#validatepassword").html("No Password was entered."); passwordvalid = false;}
		else if (field.length < 6)
			{ $("#validatepassword").html("Passwords must be at least 6 characters."); passwordvalid = false;}
		else $("#validatepassword").html("");
	}

	function validateconfirmpassword(field, field2) {
		conpassword = true;
		form = field.form;
		field1 = form.password.value;
		if( field2 != field1) { $("#validateconfirmpassword").html("Passwords do not match"); conpasswordvalid = false;}
		else $("#validateconfirmpassword").html("");
	}


    </script>
		
</head>

<body>

<div id="navbar">

</div>

<div class="container">

    <div id="header">

        <div class="details">
            <h1>Sign Up</h1>
            <h2>Discover a world of inspiration. </h2>
            
        </div>
    </div>
    
    <div class="upload">

	<form method="post" action="signup.php?validate=1" onSubmit="return validate(this)" id="signup">
	<table>
		<tr>
			<td colspan="2"> $failmessage $fail </ul> <br /></td>
		</tr>
		<tr>
			<td> Name: </td>
			<td> <input type="text" name="name" size="30" value="$name" onBlur="validatename(this.value)"> <span id="validatename"></span></td>
		</tr>
		<tr>
			<td> Email ID: </td>
			<td> <input type="text" name="email" size="30" value="$email" onBlur="validateemail(this.value)"> <span id="validateemail"></span></td>
		</tr>
		<tr>
			<td> Select username: </td>
			<td> <input type="text" name="handle" size="30" value="$handle" onBlur="validatehandle(this.value)"> <span id="validatehandle"></span></td>
		</tr>
		<tr>
			<td> Password: </td>
			<td> <input type="password" name="password" size="30" onBlur="validatepassword(this.value)"> <span id="validatepassword" ></span></td>
		</tr>
		<tr>
			<td> Confirm Password: </td>
			<td> <input type="password" name="confirmpass" size="30" onBlur="validateconfirmpassword(this,this.value)"> <span id="validateconfirmpassword"></span></td>
		</tr>
		<tr>
			<td></td>
			<td> <input type="submit" value="Next"> </td>
		</tr>
	</table>
	</form>
	
	</div>
	
	</div>
	
</body>
</html>

_END;

function validate_name($field) {
	if ($field == "") return "<li>No Name was entered </li>";
	return "";
}

function validate_email($field) {
	if ($field == "") return "<li>No Email was entered</li>";
	else if (!((strpos($field, ".") > 0) &&
	(strpos($field, "@") > 0)) ||
	preg_match("/[^a-zA-Z0-9.@_-]/", $field))
	return "<li>The Email address is invalid</li>";
	return "";
}

function validate_handle($field) {
	if ($field == "") return "<li>No Username was entered</li>";
	else if (strlen($field) < 5)
	return "<li>Usernames must be at least 5 characters</li>";
	else if (preg_match("/[^a-zA-Z0-9_-]/", $field))
	return "<li>Only letters, numbers, - and _ in usernames</li>";
	return "";
}

function validate_password($field) {
	if ($field == "") return "<li>No Password was entered</li>";
	else if (strlen($field) < 6)
	return "<li>Passwords must be at least 6 characters</li>";
	return "";
}

function validate_confirmpass($field1,$field2) {
	if ($field2 == "") return "<li>Please confirm your Password</li>";
	if ($field1 != $field2) return "<li>Passwords donot match</li>";
	return "";
}

?> 
