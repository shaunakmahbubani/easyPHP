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
$inspcol = $_SESSION['inspcol'];

$imageid = sanitizeString($_GET['view']);
$inspuserid = sanitizeString($_GET['user']);

$query = "select * from inspirations where fk_imageid=$imageid and fk_userid=$curuserid";
$result = mysql_query($query);

if( !@mysql_num_rows($result)) {
	
	$query = "select * from images where imageid=$imageid";
	$result = mysql_query($query);

	$row = mysql_fetch_assoc($result);

$query = "insert into inspirations(fk_imageid,fk_userid,authorid,inspuserid,col) values('$row[imageid]','$curuserid','$row[fk_userid]','$inspuserid','$inspcol')";
mysql_query($query);

$query = "update images set inspirations=inspirations+1 where imageid=$imageid";
mysql_query($query);

$inspcol = ($inspcol + 1) % 3;
$_SESSION['inspcol'] = $inspcol;

$query = "update users set inspcol=$inspcol where userid=$curuserid";
mysql_query($query);
}

$query = "select inspirations from images where imageid=$imageid";
$result = mysql_query($query);
$res = @mysql_fetch_row($result); //supressing warnings

echo $res[0]  ;



}

?>