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

$_SESSION['prevpage'] = "feed.php";

$userid = $curuserid;
$path = "images/$userid/";

$subquery = "select * from images join follow on images.fk_userid=follow.fk_followid where follow.fk_userid=$userid";

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
    <LINK REL=StyleSheet HREF="feed.css" TYPE="text/css">
     <script type="text/JavaScript" src="jquery.js"></script>
     <script type="text/JavaScript" src="interactions.js"></script>
     
     <script>
     	$(document).ready(function(){ 
			$(".comments .commentbox:last-child").css('display','block');
		});
     	function showfeedback(imageid) {	
			$("#image" + imageid + " .comments .commentbox").css('display','block');
			$("#image" + imageid + " .comments .commentbutton").css('display','none');
		}
		function feedback(imageid) {
			$("#image" + imageid + " .comments .commentinputbox").css('display','block');	
			$("#image" + imageid + " .comments .commentinputbox textarea").focus();
		}
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
	
<div id="navcol">
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
        	<img src="images/users/$userrow[0].jpg" class="displayimage">
            <h1><a href="profile.php?view=$userrow[0]"><b>$userrow[1]</b></a> posted in <b>$row[4]</b></h1>
            <a href="PicEnlarged.php?view=$row[0]"><img class="imgpost" src="$row[7]" alt="$row[2]"></a>
            <h2>$row[2]</h2>
            <h3>$row[3]</h3>
            <h4 class="like"> $row[9]</h4><h4>Likes &nbsp; </h4> <h4> $row[10] </h4><h4>Feedbacks &nbsp;</h4><h4 class="inspire"> $row[11] </h4><h4>Inspired &nbsp;</h4> <br /><br />
            <button class="interactions" onClick="like($row[0])"> <img src="like.png"> Like </button>
            <button class="interactions" onClick="feedback($row[0])"> <img src="feedback.png"> Feedback </button>
            <button class="interactions" onClick="inspire($row[0],$userrow[0])"> <img src="inspire.png"> Inspire </button>
             <div class="comments">
_END;

		if ( $row[10] > 1)
           	echo "<button class=\"commentbutton\" onClick=\"showfeedback($row[0])\"> Show All Feedback </button>";

		echo "<div class=\"commentboxes\">";
		
		$feedbackquery = "select * from feedback where fk_imageid=$row[0]";
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
            		<textarea name="feedback" cols="30" rows="2"></textarea> <br />
            		<input type="hidden" name="imageid" value="$row[0]">
            		<input type="submit" value="post"> <br>
            		</form>
               	</div>
            </div>
        </div>

_END;

};

echo <<<_END

    </div>
    
    <div id="rightcol" style="margin-right: 15px">
    
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
        	<img src="images/users/$userrow[0].jpg" class="displayimage">
            <h1><a href="profile.php?view=$userrow[0]"><b>$userrow[1]</b></a> posted in <b>$row[4]</b></h1>
            <a href="PicEnlarged.php?view=$row[0]"><img class="imgpost" src="$row[7]" alt="$row[2]"></a>
            <h2>$row[2]</h2>
            <h3>$row[3]</h3>
            <h4 class="like"> $row[9]</h4><h4>Likes &nbsp; </h4> <h4> $row[10] </h4><h4>Feedbacks &nbsp;</h4><h4 class="inspire"> $row[11] </h4><h4>Inspired &nbsp;</h4> <br /><br />
            <button class="interactions" onClick="like($row[0])"> <img src="like.png"> Like </button>
            <button class="interactions" onClick="feedback($row[0])"> <img src="feedback.png"> Feedback </button>
            <button class="interactions" onClick="inspire($row[0],$userrow[0])"> <img src="inspire.png"> Inspire </button>
            <div class="comments">
            
_END;

		if ( $row[10] > 1)
           echo "<button class=\"commentbutton\" onClick=\"showfeedback($row[0])\"> Show All Feedback </button>";

		echo "<div class=\"commentboxes\">";
		$feedbackquery = "select * from feedback where fk_imageid=$row[0]";
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
				
            	<div class="commentbox">
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
            		<textarea name="feedback" cols="50" rows="2"></textarea> <br />
            		<input type="hidden" name="imageid" value="$row[0]">
            		<input type="submit" value="post"> <br>
            		</form>
               	</div>
            </div>
        </div>
        
_END;
        
};

Echo <<<_END
        
        </div>
        
        <div class="column" >
    
_END;

        $query = "Select * from inspirations as insp join images on insp.fk_imageid=images.imageid where insp.fk_userid=$userid order by insp.datetime desc limit 15";
		$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
	
	$userquery = "select * from users where userid=$row[2]";
	$userresult = mysql_query($userquery);
	
	$userrow = mysql_fetch_row($userresult);
	
	$inspuserquery = "select * from users where userid=$row[4]";
	$inspuserresult = mysql_query($inspuserquery);
	
	$inspuserrow = mysql_fetch_row($inspuserresult);
		
	$authorquery = "select * from users where userid=$row[3]";
	$authresult = mysql_query($authorquery);
	
	$authrow = mysql_fetch_row($authresult);
	
	echo <<<_END
	
        <div class="post">
        	<img src="images/users/$userrow[0].jpg" class="displayimage">
        	<h3><a href="profile.php?view=$userrow[0]">$userrow[1]</a> was inspired by <a href="profile.php?view=$inspuserrow[0]">$inspuserrow[1]</a></h3>
            <a href="PicEnlarged.php?view=$row[1]"><img class="imgpost" src="$row[14]" alt="$row[9]"></a>
            <h1>$row[9]</h1>
	    <h2>Originally posted by <a href="profile.php?view=$authrow[0]">$authrow[1]</a> in $row[11]</h2>
	    <div class="interactionbuttons">
            <button class="postinteractions like" onClick="like($row[1])" style="background-image:url('like.png'); background-size: 30px 30px;">  $row[16] </button>
            <button class="postinteractions feedback" onClick="feedback($row[1])" style="background-image:url('feedback.png'); background-size: 30px 30px;"> $row[17] </button>
            <button class="postinteractions inspire" onClick="inspire($row[1],$userrow[0])" style="background-image:url('inspire.png'); background-size: 30px 29px;"> $row[18] </button>
            </div>
        </div>

_END;

};

echo <<<_END

    </div>

_END;


echo <<<_END
    

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