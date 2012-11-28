<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());


$LOGGEDIN = FALSE;
session_start();


if ( isset($_SESSION['userid']) && isset($_POST['feedback'])) {
	$LOGGEDIN = TRUE;
	$curuserid = $_SESSION['userid'];
	
	$imageid = $_POST['imageid'];
	$feedback  = $_POST['feedback'];
	
	$query = "Insert into feedback(fk_imageid,fk_userid,feedback) values('$imageid','$curuserid','$feedback')";
	mysql_query($query);
	
	mysql_query("update images set feedback=feedback+1 where imageid=$imageid");
	
	$prevpage = $_SESSION['prevpage'];
	header("location: $prevpage");
	
}

else
	header("location: feed.php");

?>