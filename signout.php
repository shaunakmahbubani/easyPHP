<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

session_start();

if( isset($_SESSION['userid'])) {
	
	$_SESSION=array();
	if (session_id() != "" || isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-2592000, '/');
	session_destroy();

}

header("location: signin.php");

?>