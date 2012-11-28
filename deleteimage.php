<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

$LOGGEDIN = FALSE;
session_start();

if ( isset($_SESSION['userid'])) {
	$LOGGEDIN = TRUE;

$curuserid = $_SESSION['userid'];
$imageid = sanitizeString($_GET['view']);

$query = "delete from images where imageid=$imageid";
mysql_query($query);

header("Location: userprofile.php");

}
?>