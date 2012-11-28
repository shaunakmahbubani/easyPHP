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


if($curuserid == $userid)
	header("location: userProfile.php");

$_SESSION['prevpage'] = "profile.php?view=$userid";

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

$ordersubquery = "order by datetime desc";
$catsubquery = "";

if ( isset($_GET['catsort']) && $_GET['catsort']!="all") {
	$catsubquery = "category='" . $_GET['catsort'] . "' and";
}

if (isset($_GET['order'])) {
	$ordersubquery = "order by " . $_GET['order'] . " desc";
}


echo <<<_END

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
    <LINK REL=StyleSheet HREF="profile.css" TYPE="text/css">
    <script type="text/JavaScript" src="jquery.js"></script>
     <script type="text/JavaScript" src="interactions.js"></script>
     <script>
          $(document).ready(function(){ 
	
	$(".connectionstab").click(function() {
		$(".connections").css('display','block');
		$(".sort").css('display','none');
		$(".connectionstab").css({'border-bottom': '#ededed solid 2px'});
		$(".sorttab").css({'border-bottom':'solid 1px white'});
	});
	
	$(".sorttab").click(function() {
		$(".sort").css('display','block');
		$(".connections").css('display','none');
		$(".sorttab").css({'border-bottom': '#ededed solid 2px'});
		$(".connectionstab").css({'border-bottom':'none'});
	});
	
	$(" .comments .commentinputbox").css('display','block');
	
	$("ul.menutop").hover(function() { $("ul.menusub").slideDown('fast').show();}, 
		function() { $("ul.menusub").slideUp('slow');});
});

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

    <div id="header">

        <div class="details">
            <img src="$profile_imagepath" class="displayimage">
            <h1>$profile_username</h1>
             <h2>@$profile_handle</h2>
_END;

		if($following)
			echo "<button class=\"followbutton\" onClick=\"follow($curuserid,$userid)\">UNFOLLOW</button>";
		else
			echo "<button class=\"followbutton\" onClick=\"follow($curuserid,$userid)\">FOLLOW</button>";
		
echo <<<_END
           
            <h3>$profile_bio</h3>

        </div>

        <div class="selectedtab" >WORKSPACE</div>
        <div class="tab" ><a href="portfolio.php?view=$userid">PORTFOLIO</a></div>
        <div class="tab" ><a href="moodboard.php?view=$userid">INSPIRATIONS</a></div>
    </div>
    
	<div class="connectionstab" style="width:45%">See Connections </div>
	<div class="sorttab" style="width:45%; float:none; border-bottom: #ededed solid 2px">Sort Images</div>
	
	<div class="connections">
		see followers
		see following
		<br><br><br><br>
	</div>
	
	<div class="sort" style="display:block; border-top: none;">
	<form method="get" action="profile.php"> 
		<input type="hidden" name="view" value="$userid">
		Sort by: &nbsp;&nbsp;Category &nbsp;<select name="catsort" size="1" selected="-1">
				<option value="all"> All </option>
				<option value="Paint"> Paint </option>
				<option value="Illustration"> Illustration </option>
				<option value="Graphic Design"> Graphic Design </option>
				<option value="Web Design"> Web Design </option>
				<option value="Photography"> Photography </option>
			     </select>
			    &nbsp;&nbsp;Order by &nbsp;<select name="order" size="1">
				<option value="datetime"> Most Recent </option>
				<option value="likes"> Most Likes </option>
				<option value="inspirations"> Most Inspired </option>
			    </select>
		&nbsp;&nbsp;<input type="submit" value="sort">
	</form>
	</div>

    
    <div id="leftcol">

_END;

$query = "Select * from images where fk_userid=$userid and $catsubquery orientation=1 $ordersubquery limit 10";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
	
	echo <<<_END

        <div class="post" id="image$row[0]">
        	<h1><b>$profile_username</b> posted in <b>$row[4]</b></h1>
            <a href="PicEnlarged.php?view=$row[0]"><img class="imgpost" src="$row[7]" alt="$row[2]"></a>
            <h2>$row[2]</h2>
            <h3>$row[3]</h3>
            <h4 class="like"> $row[9]</h4><h4>Likes &nbsp; </h4> <h4> $row[10] </h4><h4>Feedbacks &nbsp;</h4><h4 class="inspire"> $row[11] </h4><h4>Inspired &nbsp;</h4> <br /><br />
            <button class="interactions" onClick="like($row[0])"> <img src="like.png"> Like </button>
            <button class="interactions" onClick="feedback($row[0])"> <img src="feedback.png"> Feedback </button>
            <button class="interactions" onClick="inspire($row[0],$userid)"> <img src="inspire.png"> Inspire </button>
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
            		<textarea name="feedback" cols="40" rows="2"></textarea> <br />
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

    <div id="rightcol">

_END;


$query = "Select * from images where fk_userid=$userid and $catsubquery orientation=2 $ordersubquery limit 10";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
	
	echo <<<_END

        <div class="post" id="image$row[0]">
        	<h1><b>$profile_username</b> posted in <b>$row[4]</b></h1>
            <a href="PicEnlarged.php?view=$row[0]"><img class="imgpost" src="$row[7]" alt="$row[2]"></a>
            <h2>$row[2]</h2>
            <h3>$row[3]</h3>
            <h4 class="like"> $row[9]</h4><h4>Likes &nbsp; </h4> <h4> $row[10] </h4><h4>Feedbacks &nbsp;</h4><h4 class="inspire"> $row[11] </h4><h4>Inspired &nbsp;</h4> <br /><br />
            <button class="interactions" onClick="like($row[0])"> <img src="like.png"> Like </button>
            <button class="interactions" onClick="feedback($row[0])"> <img src="feedback.png"> Feedback </button>
            <button class="interactions" onClick="inspire($row[0],$curuserid)"> <img src="inspire.png"> Inspire </button>
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
            		<textarea name="feedback" cols="66" rows="2"></textarea> <br />
            		<input type="hidden" name="imageid" value="$row[0]">
            		<input type="submit" value="post"> <br>
            		</form>
               	</div>
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

else {
	header("Location: signin.php");
}

?>