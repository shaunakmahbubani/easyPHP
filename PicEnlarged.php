<?php

require_once 'login.php';

$LOGGEDIN = FALSE;
session_start();

if(isset($_GET['view']))
{
	$imageid = $_GET['view'];
	
$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

$query = "Select * from images where imageid=$imageid";
$result = mysql_query($query);

$imageresult = mysql_fetch_row($result);

	$userid = $imageresult[1];
	$title = $imageresult[2];
	$description = $imageresult[3];
	$category = $imageresult[4];
	$imagepath = $imageresult[7];

$query = "Select * from users where userid=$userid";
$result = mysql_query($query);

$userresult = mysql_fetch_row($result);
	
	$username = $userresult[1];
	$handle = $userresult[2];
	$userpicpath = "images/users/$userid.jpg";

$curuserid = $_SESSION['userid'];
$curusername = $_SESSION['name'];
$curuserhandle = $_SESSION['handle'];
$curuserimagepath = "images/users/$curuserid.jpg"; 

echo <<<_END
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>

    <LINK REL=StyleSheet HREF="PicEnlarged.css" TYPE="text/css">
    <script type="text/JavaScript" src="jquery.js"></script>
    <script type="text/JavaScript" src="interactions.js"></script>
     <script>
     function divheight(id) {
     	$("#image" + id ).css('height',$("#image" + id + " .leftimgpost").css('height'));
     	
     }
     </script>	
</head>
<body>

<div id="navbar">
	<div class="container">
	<a href="feed.php"><img src="homeicon.png" class="navicon" /></a>
<a href="communitiesmain.php"><img src="communitiesicon.png" class="navicon"/></a>
	
	<ul class="menutop">
	<li><a href="userprofile.php"><img src="profileicon.png" class="navicon"  style="float:right"/></a></li>
		<ul class="menusub">
			<li><a href="editprofile.php">Edit Profile</a></li>
			<li><a href="signout.php">Sign Out</a></li>
		</ul>
	</ul>
	</div>
</div>

<div class="container">

    <div id="leftcol">

        <div id="header">

            <div class="details">
                <img src="$userpicpath" class="displayimage">
                <h1>$username</h1>
                <h2>@$handle</h2>
            </div>


            <div class="tab" ><a href="profile.php?view=$userid">WORKSPACE</a></div>
            <div class="tab" ><a href="portfolio.php?view=$userid">PORTFOLIO</a></div>
            <div class="tab" ><a href="moodboard.php?view=$userid">INSPIRATIONS</a></div>

        </div>
        
_END;

$query = "Select * from images where fk_userid=$userid order by datetime desc limit 5";
$result = mysql_query($query);
if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
	
echo <<<_END

        <div class="leftpost" id="image$row[0]" >
        	<a href="PicEnlarged.php?view=$row[0]"><img class="leftimgpost" src="$row[7]" onLoad="divheight($row[0])"></a>
        	<a href="PicEnlarged.php?view=$row[0]"><h1>$row[2]</h1></a>
        	<h2>$row[4]</h2>
        </div>
        
_END;

};

echo <<<_END
    </div>

    <div id="rightcol">

        <div class="post" id="image$imageid">
            <h1><b>$username</b> posted in <b>$category</b></h1>
            <img class="imgpost" src="$imagepath" alt="$title">
            <h2>$title</h2>
            <h3>$description</h3>
            <h4 class="like"> $imageresult[9] Likes</h4><h4> $imageresult[10] Feedbacks</h4><h4 class="inspire"> $imageresult[11] Inspired</h4> <br /><br />
            <button class="interactions" onClick="like($imageid)"> <img src="like.png"> Like </button>
            <button class="interactions" onClick="feedback($imageid)"> <img src="feedback.png"> Feedback </button>
            <button class="interactions" onClick="inspire($imageid,$curuserid)"> <img src="inspire.png"> Inspire </button>
            <div class="comments">
_END;


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

				if (!$result) die ("Database access failed: " . mysql_error());
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
            		<textarea name="feedback" cols="90" rows="2"></textarea> <br />
            		<input type="hidden" name="imageid" value="$imageid">
            		<input type="submit" value="post"> <br>
            		</form>
               	</div>
            </div>
        </div>
        </div>
    </div>

</div>

</body>
</html>

_END;

}

?>