
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
	
	$(".comments .commentbox:last-child").css('display','block');
	$(" .comments .commentinputbox").css('display','block');
	
	$("ul.menutop").hover(function() { $("ul.menusub").slideDown('fast').show();}, 
		function() { $("ul.menusub").slideUp('slow');});
	
});

function follow(userid,followid) {
	$(".followbutton").load("follow.php?user="+userid+"&follow="+followid);
}

function like(imageid) {
	 	$("#image" + imageid + " .like").load("like.php?view=" + imageid);
	 }
	 
function inspire(imageid,userid) {
		 $("#image" + imageid + " .inspire").load("inspire.php?view=" + imageid + "&user=" + userid);	
}

function showfeedback(imageid) {	
		$("#image" + imageid + " .comments .commentbox").css('display','block');
		$("#image" + imageid + " .comments .commentbutton").css('display','none');
	}
	
function feedback(imageid) {
			$("#image" + imageid + " .comments .commentinputbox").css('display','block');	
			$("#image" + imageid + " .comments .commentinputbox textarea").focus();
}
	

