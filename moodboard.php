<?php

require_once 'login.php';

$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($db_database, $db_server)
or die("Unable to select database: " . mysql_error());

$LOGGEDIN = FALSE;
session_start();

if ( isset($_SESSION)) {
	$LOGGEDIN = TRUE;


$curuserid = $_SESSION['userid'];
$curusername = $_SESSION['name'];
$curuserhandle = $_SESSION['handle'];
$curuserimagepath = "images/users/$curuserid.jpg";

if( isset($_GET['view'])) 
	$userid = $_GET['view'];

else {
	$userid = $curuserid;
}

$_SESSION['prevpage'] = "moodboard.php?view=$userid";

$path = "images/$userid/";

$query = "Select * from users where userid=$userid";
$result = mysql_query($query);
if (!$result) die ("Database access failed: " . mysql_error());

$profile_result = mysql_fetch_row($result);
$profile_username = $profile_result[1];
$profile_handle = $profile_result[2];
$profile_imagepath = "images/users/$userid.jpg";
$profile_bio = $profile_result[4];

$following = false;
$query = "Select * from follow where fk_userid=$curuserid and fk_followid=$userid";
$result = mysql_query($query);
if (mysql_num_rows(mysql_query($query)))
	$following = true;

echo <<<_END

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>

    <LINK REL=StyleSheet HREF="moodboard.css" TYPE="text/css">
    <script type="text/JavaScript" src="jquery.js"></script>
    <script type="text/JavaScript" src="interactions.js"></script>
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

    <div id="header">

        <div class="details">
            <img src="$profile_imagepath" class="displayimage">
            <h1>$profile_username</h1>
            <h2>@$profile_handle</h2>
            
_END;
	if($userid!=$curuserid) {
		if($following)
			echo "<button class=\"followbutton\" onClick=\"follow($curuserid,$userid)\">UNFOLLOW</button>";
		else
			echo "<button class=\"followbutton\" onClick=\"follow($curuserid,$userid)\">FOLLOW</button>";
	}
		
echo <<<_END

            <br>
            <h3>$profile_bio</h3>

        </div>

        <div class="tab" ><a href="Profile.php?view=$userid">WORKSPACE</a></div>
        <div class="tab" ><a href="portfolio.php?view=$userid">PORTFOLIO</a></div>
        <div class="selectedtab" >INSPIRATIONS</div>

    </div>

    <div class="column" style="margin-right: 15px">
    
_END;

$query = "Select * from inspirations as insp join images on insp.fk_imageid=images.imageid where insp.fk_userid=$userid and col=0 order by insp.datetime desc limit 5";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);

	$authorquery = "select * from users where userid=$row[3]";
	$authresult = mysql_query($authorquery);
	
	$authrow = mysql_fetch_row($authresult);
	
	echo <<<_END
	
        <div class="post">
            <a href="PicEnlarged.php?view=$row[1]"><img class="imgpost" src="$row[14]" alt="$row[9]"></a>
            <h1>$row[9]</h1>
	    <h2>Originally posted by <a href="profile.php?view=$authrow[0]">$authrow[1]</a> in $row[11]</h2>
            <div class="interactionbuttons">
            <button class="postinteractions like" onClick="like($row[1])" style="background-image:url('like.png'); background-size: 30px 30px;">  $row[16] </button>
            <button class="postinteractions feedback" onClick="feedback($row[1])" style="background-image:url('feedback.png'); background-size: 30px 30px;"> $row[17] </button>
            <button class="postinteractions inspire" onClick="inspire($row[1],$userid)" style="background-image:url('inspire.png'); background-size: 30px 29px;"> $row[18] </button>
            </div>
        </div>

_END;

}

echo <<<_END

    </div>

    <div class="column" style="margin-right: 15px">
    
_END;

        $query = "Select * from inspirations as insp join images on insp.fk_imageid=images.imageid where insp.fk_userid=$userid and col=1 order by insp.datetime desc limit 5";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
		
	$authorquery = "select * from users where userid=$row[3]";
	$authresult = mysql_query($authorquery);
	
	$authrow = mysql_fetch_row($authresult);
	
	echo <<<_END
	
        <div class="post">
            <a href="PicEnlarged.php?view=$row[1]"><img class="imgpost" src="$row[14]" alt="$row[9]"></a>
            <h1>$row[9]</h1>
	    <h2>Originally posted by <a href="profile.php?view=$authrow[0]">$authrow[1]</a> in $row[11]</h2>
            <div class="interactionbuttons">
            <button class="postinteractions like" onClick="like($row[1])" style="background-image:url('like.png'); background-size: 30px 30px;">  $row[16] </button>
            <button class="postinteractions feedback" onClick="feedback($row[1])" style="background-image:url('feedback.png'); background-size: 30px 30px;"> $row[17] </button>
            <button class="postinteractions inspire" onClick="inspire($row[1],$userid)" style="background-image:url('inspire.png'); background-size: 30px 29px;"> $row[18] </button>
            </div>
        </div>

_END;

}

echo <<<_END

    </div>

    <div class="column">
    
_END;

  $query = "Select * from inspirations as insp join images on insp.fk_imageid=images.imageid where insp.fk_userid=$userid and col=2 order by insp.datetime desc limit 5";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
	
	$authorquery = "select * from users where userid=$row[3]";
	$authresult = mysql_query($authorquery);
	
	$authrow = mysql_fetch_row($authresult);
	
	echo <<<_END
	
        <div class="post">
            <a href="PicEnlarged.php?view=$row[1]"><img class="imgpost" src="$row[14]" alt="$row[9]"></a>
            <h1>$row[9]</h1>
	    <h2>Originally posted by <a href="profile.php?view=$authrow[0]">$authrow[1]</a> in $row[11]</h2>
            <div class="interactionbuttons">
            <button class="postinteractions like" onClick="like($row[1])" style="background-image:url('like.png'); background-size: 30px 30px;">  $row[16] </button>
            <button class="postinteractions feedback" onClick="feedback($row[1])" style="background-image:url('feedback.png'); background-size: 30px 30px;"> $row[17] </button>
            <button class="postinteractions inspire" onClick="inspire($row[1],$userid)" style="background-image:url('inspire.png'); background-size: 30px 29px;"> $row[18] </button>
            </div>
        </div>

_END;

}

echo <<<_END

    </div>

</div>

</body>
</html>

_END;

}

?>