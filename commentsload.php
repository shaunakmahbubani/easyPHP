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

$userid = $curuserid = $_SESSION['userid'];
$curusername = $_SESSION['name'];
$curuserimagepath = "images/users/$curuserid.jpg"; 

$imageid = sanitizeString($_GET['view']);

		echo "<div class=\"commentboxes\">";
		
		$feedbackquery = "select * from feedback where fk_imageid=$imageid";
		$feedresult = mysql_query($feedbackquery);

		if (!$feedresult) die ("Database access failed: " . mysql_error());
		$fbrows = mysql_num_rows($feedresult);
		for ($i = 0 ; $i < $fbrows ; ++$i)
		{
				$fbrow = mysql_fetch_row($feedresult);
				
				$userquery1 = "select username from users where userid=$fbrow[2]";
				$userresult1 = mysql_query($userquery1);
				$fbuserrow = @mysql_fetch_row($userresult1);

				echo <<<_END
				
            	<div class="commentbox" >
            		<div class="commentimagebox"><img src="images/users/$fbrow[2].jpg" class="commentimage"></div>
            		<h1>$fbuserrow[0]</h1>
            		<h2>$fbrow[3]</h2>
               	</div>
               	
_END;

		}
		
		echo<<<_END
                
                </div>
               	<div class="commentbox commentinputbox">
               		<form method="post" action="feedback.php">
            		<div class="commentimagebox"><img src="$curuserimagepath" class="commentimage"></div>
            		<h1>$curusername</h1>
            		<textarea name="feedback" cols="40" rows="2"></textarea> <br />
            		<input type="hidden" name="imageid" value="$imageid">
            		<input type="submit" value="post"> <br>
            		</form>
               	</div>
           
_END;



?>