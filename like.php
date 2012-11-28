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

$query = "select * from likes where fk_imageid=$imageid and fk_userid=$curuserid";
$result = mysql_query($query);
//$row = @mysql_fetch_row($result); //supressing warnings

if( !@mysql_num_rows($result)) {

	$query = "insert into likes(fk_imageid,fk_userid) values('$imageid','$curuserid')";
	mysql_query($query);
	

	$query = "update images set likes=likes+1 where imageid=$imageid";
	mysql_query($query);
	
}

$query = "select likes from images where imageid=$imageid";
$result = mysql_query($query);
$res = @mysql_fetch_row($result); //supressing warnings

echo $res[0] ;

}
?>