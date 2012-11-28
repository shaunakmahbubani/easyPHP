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

$_SESSION['prevpage'] = "userprofile.php";

$userid = $curuserid;
$path = "images/$userid/";


$query = "Select * from users where userid=$userid";
$result = mysql_query($query);
if (!$result) die ("Database access failed: " . mysql_error());

$profile_result = mysql_fetch_row($result);
$profile_username = $profile_result[1];
$profile_handle = $profile_result[2];
$profile_imagepath = "images/users/$userid.jpg";
$profile_bio = $profile_result[4];

$fail = "";

if($_FILES) {
	

	if($_FILES['image']['error'] == 0)
	{

		switch($_FILES['image']['type'])
		{
		case 'image/jpeg': $ext = 'jpg'; break;
		case 'image/gif': $ext = 'gif'; break;
		case 'image/png': $ext = 'png'; break;
		case 'image/tiff': $ext = 'tif'; break;
		default: $ext = ''; break;
		}

		if ($ext) 
		{
		$name = sanitizeString($_FILES['image']['name']);
		$filepath = "{$path}{$name}";
		$title = sanitizeString($_POST['title']);
		$description = sanitizeString($_POST['description']);
		$category = sanitizeString($_POST['category']);
		$orientation = sanitizeString($_POST['orientation']);
		if(isset($_POST['portfolio']))
			$portfolio = sanitizeString($_POST['portfolio']);
		else 
			$portfolio = 0;
		
		

		$update = "INSERT into images(fk_userid,title,description,category,orientation,filepath,portfolio,likes,feedback,inspirations) values('$userid', '$title', '$description', '$category', $orientation,'$filepath','$portfolio',0,0,0)";
		mysql_query($update);
		echo mysql_error();
		$imageid = mysql_insert_id();
		

		move_uploaded_file($_FILES['image']['tmp_name'], "$filepath");
		$_FILES = NULL;
		}
	}
	
	else 
		echo $_FILES['image']['error'];
	
};

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
	
	$(".uploadtab").click(function() {
		$(".upload").css('display','block');
		$(".connections").css('display','none');
		$(".sort").css('display','none');
		$(".uploadtab").css({'border-bottom': '#ededed solid 2px'});
		$(".connectionstab").css({'border-bottom':'none'});
		$(".sorttab").css({'border-bottom':'none'});
	});
	
	$(".connectionstab").click(function() {
		$(".upload").css('display','none');
		$(".connections").css('display','block');
		$(".sort").css('display','none');
		$(".connectionstab").css({'border-bottom': '#ededed solid 2px'});
		$(".uploadtab").css({'border-bottom':'none'});
		$(".sorttab").css({'border-bottom':'none'});
	});
	
	$(".sorttab").click(function() {
		$(".sort").css('display','block');
		$(".upload").css('display','none');
		$(".connections").css('display','none');
		$(".sorttab").css({'border-bottom': '#ededed solid 2px'});
		$(".uploadtab").css({'border-bottom':'none'});
		$(".connectionstab").css({'border-bottom':'none'});
	});
	
	$(" .comments .commentinputbox").css('display','block');

	$("ul.menutop").hover(function() { $("ul.menusub").slideDown('fast').show();}, 
		function() { $("ul.menusub").slideUp('slow');});
     });

	function validate(form) {
		if (form.title.value == "") {
			$("#validatetitle").html("<br>Please enter a title");
			return false; }
		return true;	
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

    <div id="header">

        <div class="details">
            <img src="$profile_imagepath" class="displayimage">
            <h1>$profile_username</h1>
            <h2>@$profile_handle</h2> <br>
            <h3>$profile_bio</h3>

        </div>

        <div class="selectedtab" >WORKSPACE</div>
        <div class="tab" ><a href="portfolio.php?view=$userid">PORTFOLIO</a></div>
        <div class="tab" ><a href="moodboard.php?view=$userid">INSPIRATIONS</a></div>
    </div>

	<div class="uploadtab">Upload an Image</div>
	<div class="connectionstab">See Connections </div>
	<div class="sorttab">Sort Images</div>
	
    <br><br><br>
    <div class="upload">
	
	<form method='post' action='userprofile.php' enctype='multipart/form-data' onSubmit="return validate(this)">
	<table>
	<tr>
		<td> Select an Image: </td>
		<td> <input type="file" name="image" size='10' /> </td>
		<td> Category: </td>
		<td> <select name="category" size="1">
				<option value="Paint"> Paint </option>
				<option value="Illustration"> Illustration </option>
				<option value="Graphic Design"> Graphic Design </option>
				<option value="Web Design"> Web Design </option>
				<option value="Photography"> Photography </option>
			     </select> </td>
	</tr>
	
	<tr>
		<td> Title: </td>
	    <td> <input type="text" name="title" size="36"><span id="validatetitle"></span> </td>
	    <td> Add to portfolio: </td>
	    <td> <input type="checkbox" name="portfolio" value="1" /> </td>
	    
	</tr>
	
	<tr> 
		<td style="text-align: top"> Description: </td>
		<td> <textarea name="description" cols="27" rows="4"></textarea> </td>
		<td> Select Orientation: </td>
		<td> <input type="radio" name="orientation" value="1" checked> Portrait <br /> 
	     	     <input type="radio" name="orientation" value="2"> Landscape 
		</td>
    </tr>
    
	<tr>
		<td> <br /> <input type='submit' value='Upload' /> </td>
	</tr>
	
	</table>
	</form>

    </div>
    
	<div class="connections">
		see followers
		see following
		<br><br><br><br>
	</div>
	
	<div class="sort">
	<form method="get" action="userprofile.php"> 
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

		
	<ul class="edittop">
		<li><img src="edit.png" class="editimg"/></li>
		<ul class="editsub">
			<li><a href="changeorientation.php?view=$row[0]">Change Orientation</a></li>
			<li><a href="changeportfolio.php?view=$row[0]">Toggle Portfolio</a></li>
			<li><a href="deleteimage.php?view=$row[0]">Delete Image</a></li>
		</ul>
	</ul>
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
		<ul class="edittop">
		<li><img src="edit.png" class="editimg"/></li>
		<ul class="editsub">
			<li><a href="changeorientation.php?view=$row[0]">Change Orientation</a></li>
			<li><a href="changeportfolio.php?view=$row[0]">Toggle Portfolio</a></li>
			<li><a href="deleteimage.php?view=$row[0]">Delete Image</a></li>
		</ul>
	</ul>

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