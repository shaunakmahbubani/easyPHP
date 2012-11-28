<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

$LOGGEDIN = FALSE;
session_start();

if ( isset($_SESSION['userid'])) 
	$LOGGEDIN = TRUE;

$curuserid = $_SESSION['userid'];
$inspcol = $_SESSION['inspcol'];

$imageid = sanitizeString($_GET['view']);

$query = "select * from images where imageid=$imageid";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());

	$row = mysql_fetch_row($result);
	
	$userquery = "select * from users where userid=$row[1]";
	$userresult = mysql_query($userquery);

	if (!$result) die ("Database access failed: " . mysql_error());
	$userrow = mysql_fetch_row($userresult);
	
if($row[5]==1) {

	echo <<<_END
        <div class="postver text" id="image$row[0]" >
        	<img src="images/users/$userrow[0].jpg" class="displayimage" >
            <h1><a href="profile.php?view=$userrow[0]"><b>$userrow[1]</b></a> posted in <b>$row[4]</b></h1><br><br>
            <img class="imgpost" src="$row[7]" alt="$row[2]" onClick="sidebarextend($row[0])">
            <h2>$row[2]</h2>
            <h3>$row[3]</h3>
            <h4 class="like"> $row[9]</h4><h4>Likes &nbsp; </h4> <h4> $row[10] </h4><h4>Feedbacks &nbsp;</h4><h4 class="inspire"> $row[11] </h4><h4>Inspired &nbsp;</h4> <br /><br />
            <button class="interactions" onClick="like($row[0])"> <img src="like.png"> Like </button>
            <button class="interactions" onClick="sidebarextend($row[0])"> <img src="feedback.png"> Feedback </button>
            <button class="interactions" onClick="inspire($row[0],$userrow[0])"> <img src="inspire.png"> Inspire </button>
	    <div class="comments"></div>		

	</div>
_END;

	}
	
else {
	echo <<<_END
        <div class="posthorz text" id="image$row[0]" >
        	<img src="images/users/$userrow[0].jpg" class="displayimage" >
            <h1><a href="profile.php?view=$userrow[0]"><b>$userrow[1]</b></a> posted in <b>$row[4]</b></h1><br><br>
            <img class="imgpost" src="$row[7]" alt="$row[2]" onClick="sidebarextend($row[0])">
            <h2>$row[2]</h2>
            <h3>$row[3]</h3>
            <h4 class="like"> $row[9]</h4><h4>Likes &nbsp; </h4> <h4> $row[10] </h4><h4>Feedbacks &nbsp;</h4><h4 class="inspire"> $row[11] </h4><h4>Inspired &nbsp;</h4> <br /><br />
            <button class="interactions" onClick="like($row[0])"> <img src="like.png"> Like </button>
            <button class="interactions" onClick="sidebarextend($row[0])"> <img src="feedback.png"> Feedback </button>
            <button class="interactions" onClick="inspire($row[0],$userrow[0])"> <img src="inspire.png"> Inspire </button>
	    <div class="comments"></div>
		</div>
_END;


}

?>