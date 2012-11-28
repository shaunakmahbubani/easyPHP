<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

$userid = $_GET['user'];
$followid = $_GET['follow'];
$following = false;

$query = "Select * from follow where fk_userid=$userid and fk_followid=$followid";
$result = mysql_query($query);
if (mysql_num_rows(mysql_query($query)))
	$following = true;

if($following) {
$query = "delete from follow where fk_userid=$userid and fk_followid=$followid";
mysql_query($query);
echo "FOLLOW";
}

else {	
$query = "insert into follow(fk_userid,fk_followid) values('$userid','$followid')";
mysql_query($query);
echo "UNFOLLOW";
};

?>

