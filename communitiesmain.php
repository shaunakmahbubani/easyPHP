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

$categories[0] = "Paint";
$categories[1] = "Illustration";
$categories[2] = "Graphic Design";
$categories[3] = "Photography";
$categories[4] = "Web Design";


echo <<<_END

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
    <LINK REL=StyleSheet HREF="communitiesmain.css" TYPE="text/css">
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

_END;

for ($i = 0 ; $i < 5 ; $i++)
{
	
	echo <<<_END
	

    <div class="community">
    
    	<div class="title">
    		<a href="communities.php?cat=$categories[$i]"><h1> $categories[$i] </h1></a>
    	</div>
    	
    	<div class="images">
_END;

$query = "Select * from images where category='$categories[$i]' order by datetime desc limit 4";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);
for ($j = 0 ; $j < $rows ; ++$j)
{
	$row = mysql_fetch_row($result);
	
	echo <<<_END
    	
    	<a href="communities.php?cat=$categories[$i]"><img class="imgpost" src="$row[7]" alt="$row[2]"></a>
_END;

}

	echo <<<_END
	
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
	header("location: signin.php");
}

?>