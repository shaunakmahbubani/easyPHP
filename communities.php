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
$curusername = $_SESSION['name'];
$curuserhandle = $_SESSION['handle'];
$curuserimagepath = "images/users/$curuserid.jpg"; 

$userid = $curuserid;
$path = "images/$userid/";

if( isset($_GET['cat'])) {
		
	$cat = sanitizeString($_GET['cat']);
	$subquery = "select * from images where category='$cat'";
}

echo <<<_END

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
    <LINK REL=StyleSheet HREF="communities.css" TYPE="text/css">
     <script type="text/JavaScript" src="jquery.js"></script>
     <script type="text/JavaScript" src="interactions.js"></script>
     
     <script>
     	function sidebardisplay(imageid) {
     		$(".sidebar").fadeOut(function() {
		$(".sidebar").load("sidebarload.php?view=" + imageid,function() {
 					$(".sidebar").fadeIn();
			});
		});
     	}
     	
     	function sidebarextend(imageid){
		$(".sidebar").fadeOut();
     		$(".sidebox").animate({width:'81%'},600, function() {	
		$(".sidebox").removeClass("sidebox").addClass("lightbox");
		$(".sidebar .comments").load("commentsload.php?view=" + imageid).css('display','block');
		$(".sidebar").fadeIn(); });
     	}
     
	function sidebarcollapse(){
		$(".sidebar").fadeOut();
		$(".lightbox").animate({width:'38%'},600, function() {	
		$(".lightbox").removeClass("lightbox").addClass("sidebox");
		$(".sidebar .comments").css('display','none');
		$(".sidebar").fadeIn(); });
	}
		     
     	$(document).ready(function(){
     		
		});
	</script>
	
</head>
<body>
<div id="navbar">
	<div class="wrapper">
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


<div class="wrapper">
	
<div id="navcol" onClick="sidebarcollapse()">
	<div class="details">
                <img src="images/users/$userid.jpg" class="headerdisplayimage">
                <a href="Profile.php"><h1>$curusername</h1></a>
                <h2>@$curuserhandle</h2>
                
            </div>
            
	
	<div class="filterbar">
		<h1 style="color:gray">Communities:</h1>
		<h1><a href="communities.php?cat=Paint">Paint</a></h1>
		<h1><a href="communities.php?cat=Illustration">Illustration</a></h1>
		<h1><a href="communities.php?cat=Graphic Design">Graphic Design</a></h1>
		<h1><a href="communities.php?cat=Photography">Photography</a></h1>
		<h1><a href="communities.php?cat=Web Design">Web Design</a></h1>
	</div>
	

</div>

<div id="container">

    <div id="leftcol">

_END;

$query = "$subquery and orientation=1 order by datetime desc limit 10";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
	
	$userquery = "select * from users where userid=$row[1]";
	$userresult = mysql_query($userquery);

	if (!$result) die ("Database access failed: " . mysql_error());
	$userrow = mysql_fetch_row($userresult);

	echo <<<_END
        <div class="leftpost text" id="image$row[0]">
            <img class="imgpost" src="$row[7]" alt="$row[2]" onClick="sidebardisplay($row[0])">
            <img src="images/users/$userrow[0].jpg" class="displayimage">
            <h2>$row[2]</h2>
            <h3>Posted by <a href="profile.php?view=$userrow[0]"><b>$userrow[1]</b></a></h3>
            <div class="interactionbuttons">
            <button class="postinteractions like" onClick="like($row[0])" style="background-image:url('like.png'); background-size: 30px 30px;">  $row[9] </button>
            <button class="postinteractions feedback" onClick="feedback($row[0])" style="background-image:url('feedback.png'); background-size: 30px 30px;"> $row[10] </button>
            <button class="postinteractions inspire" onClick="inspire($row[0],$userrow[0])" style="background-image:url('inspire.png'); background-size: 30px 29px;"> $row[11] </button>
            </div>
        </div>

_END;

};

echo <<<_END

    </div>

    <div id="rightcol">
    
_END;

$query = "$subquery and orientation=2 order by datetime desc limit 10";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
	
	$userquery = "select * from users where userid=$row[1]";
	$userresult = mysql_query($userquery);

	if (!$result) die ("Database access failed: " . mysql_error());
	$userrow = @mysql_fetch_row($userresult);

	echo <<<_END
         <div class="leftpost text" id="image$row[0]">
            <img class="imgpost" src="$row[7]" alt="$row[2]" onClick="sidebardisplay($row[0])">
            <img src="images/users/$userrow[0].jpg" class="displayimage">
            <h2>$row[2]</h2>
            <h3>Posted by <a href="profile.php?view=$userrow[0]"><b>$userrow[1]</b></a></h3>
            <div class="interactionbuttons">
            <button class="postinteractions like" onClick="like($row[0])" style="background-image:url('like.png'); background-size: 30px 30px;">  $row[9] </button>
            <button class="postinteractions feedback" onClick="feedback($row[0])" style="background-image:url('feedback.png'); background-size: 30px 30px;"> $row[10] </button>
            <button class="postinteractions inspire" onClick="inspire($row[0],$userrow[0])" style="background-image:url('inspire.png'); background-size: 30px 29px;"> $row[11] </button>
            </div>
        </div>
_END;

};

echo <<<_END
		
    </div>

    
	</div>
</div>

<div class="sidebox">
	<div class="sidebar">

	</div>
</div>


</body>
</html>

_END;

}

else {
	header("location: signin.php");
}

?>