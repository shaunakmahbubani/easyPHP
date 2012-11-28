<?php //login.php
$db_hostname = 'localhost';
$db_database = 'publications';
$db_username = 'admin';
$db_password = '';


function sanitizeString($var)
{
	$var = strip_tags($var);
	$var = htmlentities($var);
	$var = stripslashes($var);
	return mysql_real_escape_string($var);
}


?>