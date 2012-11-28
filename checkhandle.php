<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

if ( isset($_GET['view'])) {
	
	$handle = $_GET['view'];

	$query = "select * from users where handle='$handle'";
	$result = @mysql_query($query);

	if( @mysql_num_rows($result))
		echo "The entered username is not available";

	else
		echo "The entered username is available";

}

?>