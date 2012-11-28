<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

$fail = $email = "";

if (isset($_POST['email']) && isset($_POST['password'])) {
	
	$email = sanitizeString($_POST['email']);
	$password = sanitizeString($_POST['password']);
		
	$query = "select * from users where email='$email'";
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	
	if (!$row) {
		
		$fail = "Invalid Email ID or Password";
	}
	
	else {

		$salt1 = "g^8p";
		$salt2 = "jd#4l";

		$saltedpassword = md5("$salt1$password$salt2");
		
		if ($saltedpassword == $row['password']) {
			session_start();
			$_SESSION['userid'] = $row['userid'];
			$_SESSION['name'] = $row['username'];
			$_SESSION['handle'] = $row['handle'];
			$_SESSION['inspcol'] = $row['inspcol'];
			
						
			header("location: feed.php");
		}
		
		else {
			$fail = "Invalid Email ID or Password";
		}
	}
}

echo <<<_END

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
    <LINK REL=StyleSheet HREF="signin.css" TYPE="text/css">
</head>

<body>

	<div class="top">
		<div class="container">
		
			<h1>Artizen<h1>
			<h2>The Inspiration Network<h2>
		
		</div>
	</div>
	
	<div class="container upload">

	<form method="post" action="signin.php">
	<table>
		<tr>
			<td> Email: </td>
			<td> <input type="text" name="email" size="30" value="$email"> </td>
		</tr>
		<tr>
			<td> Password </td>
			<td> <input type="password" name="password" size="30"> </td>
		</tr>
		<tr>
			<td colspan="2"> $fail </td>
		</tr>
		<tr>
			<td></td>
			<td> <input type="submit" value="Sign In">
			     <a href="signup.php"><button type="button" value="Sign up" href="signup.php">Sign Up</button></a></td>
		</tr>
	</table>
	</form>
	
	</div>
	
</body>
</html>


_END;

?>