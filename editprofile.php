<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

$LOGGEDIN = FALSE;
session_start();

if ( isset($_SESSION)) {
	$LOGGEDIN = TRUE;

$name = $username = $email = $handle = $password = $confirmpass = $fail = $failmessage = "";

$userid = $_SESSION['userid'];

$query = "Select * from users where userid=$userid";
$result = mysql_query($query);
if (!$result) die ("Database access failed: " . mysql_error());

$row = mysql_fetch_row($result);
$name = $row[1];
$handle = $row[2];
$email = $row[3];
$bio = $row[4];

if( isset($_GET['validate'])) {

if (isset($_POST['name']))
	$name = sanitizeString($_POST['name']);
if (isset($_POST['email']))
	$email = sanitizeString($_POST['email']);
if (isset($_POST['handle']))
	$handle = sanitizeString($_POST['handle']);
if (isset($_POST['bio']))
	$bio = sanitizeString($_POST['bio']);
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

if($_FILES) {
	
	
	if($_FILES['image']['error'] == 0)
	{
		
		switch($_FILES['image']['type'])
		{
		case 'image/jpeg': $ext = 'jpg'; break;
		case 'image/gif': $ext = 'gif'; break;
		case 'image/png': $ext = 'png'; break;
		case 'image/tiff': $ext = 'tif'; break;
		default: $ext = ''; break;
		}

		if ($ext) 
		{

		$var = move_uploaded_file($_FILES['image']['tmp_name'], "images/users/$userid.jpg");
		$_FILES = NULL;
		}
	}

	}

	if($fail == "" && isset($_GET['validate'])) {
	
		$salt1 = "g^8p";
		$salt2 = "jd#4l";

		$saltedpassword = md5("$salt1$password$salt2");
		
		$query = "Update users set username='$name', handle='$handle', email='$email', bio='$bio', password='$saltedpassword' where userid='$userid'";
		mysql_query($query);
		
		$_SESSION['name'] = $name;
		$_SESSION['handle'] = $handle;
				
		header("Location: userprofile.php");
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
    <LINK REL=StyleSheet HREF="editprofile.css" TYPE="text/css">
    <script type="text/JavaScript" src="jquery.js"></script>
    <script>
	var valid=true;

	function validate(form) {
		return valid;
	}

	function validatename(field) {
		if (field == "") { $("#validatename").html("Please Enter your Name"); valid = false;}
		else $("#validatename").html("");
	}

	function validateemail(field) {
		if (field == "") { $("#validateemail").html("Please Enter your Email Address"); valid = false;}
		else if (!((field.indexOf(".") > 0) && (field.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test(field))
			{ $("#validateemail").html("Please Enter a valid Email Address"); valid = false;}
		else $("#validateemail").html("");
	}

	function validatehandle(field) {
		if (field == "") { $("#validatehandle").html("Please Enter a Username"); valid = false; return}
		else if (field.length < 5) { $("#validatehandle").html("Username must be atleast 5 characters"); valid = false; return}
		else if (/[^a-zA-Z0-9_-]/.test(field)) { $("#validatehandle").html("Only letters, numbers, - and _ in usernames"); valid = false; return}
		$("#validatehandle").load("checkhandle.php?view=" + field);
	}

	function validatepassword(field) {
		if (field == "") { $("#validatepassword").html("No Password was entered."); valid = false;}
		else if (field.length < 6)
			{ $("#validatepassword").html("Passwords must be at least 6 characters."); valid = false;}
		else $("#validatepassword").html("");
	}

	function validateconfirmpassword(field, field2) {
		form = field.form;
		field1 = form.password.value;
		if( field2 != field1) { $("#validateconfirmpassword").html("Passwords do not match"); valid = false;}
		else $("#validateconfirmpassword").html("");
	}


    </script>
</head>
<body>
<div id="navbar">
	<div class="container">
	<a href="feed.php"><img src="homeicon.png" class="navicon" /></a>
	<img src="communitiesicon.png" class="navicon"/>
	
	<ul class="menutop">
	<li><a href="userprofile.php"><img src="profileicon.png" class="navicon"  style="float:right"/></a></li>
		<ul class="menusub">
			<li><a href="editprofile.php">Edit Profile</a></li>
			<li><a href="signout.php">Sign Out</a></li>
		</ul>
	</ul>
	</div>
</div>

<div class="container">

    <div id="header">

        <div class="details">
            <h1>Edit Your Profile</h1>
            <h2>Customize your profile to help other artists find you. </h2>
            
        </div>
    </div>
    
    <div class="upload">
    
    <form method="post" action="editprofile.php?validate=1" enctype='multipart/form-data' onSubmit="return validate(this)">
	<table>
		<tr>
			<td colspan="2"> $failmessage $fail </ul> </td>
		</tr>
		<tr>
			<td> Name: </td>
			<td> <input type="text" name="name" size="40" value="$name" onBlur="validatename(this.value)"> <span id="validatename"></span> </td>
		</tr>
		<tr>
			<td> Email ID: </td>
			<td> <input type="text" name="email" size="40" value="$email" onBlur="validateemail(this.value)"> <span id="validateemail"></span> </td>
		</tr>
		<tr>
			<td> Username: </td>
			<td> <input type="text" name="handle" size="40" value="$handle" onBlur="validatehandle(this.value)"> <span id="validatehandle"></span> </td>
		</tr>
		<tr>
			<td> About Me: </td>
			<td> <textarea name="bio" rows="4" cols="30" >$bio</textarea> </td>
		</tr>
		<tr>
			<td> Password: </td>
			<td> <input type="password" name="password" size="40" onBlur="validatepassword(this.value)"> <span id="validatepassword" ></span> </td>
		</tr>
		<tr>
			<td> Confirm Password: </td>
			<td> <input type="password" name="confirmpass" size="40" onBlur="validateconfirmpassword(this,this.value)"> <span id="validateconfirmpassword"></span> </td>
		</tr>
		<tr>
			<td> <br> <img src="images/users/$userid.jpg" class="displayimage"></td>
			<td> Display Image: <br /><input type="file" name="image" size='10' /> </td>
		</tr>
		<tr>
			<td></td>
			<td> <br> <input type="submit" value="Update"> </td>
		</tr>
	</table>
	</form>
    
    
    </div>



</div>

</body>
</html>

_END;

}

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