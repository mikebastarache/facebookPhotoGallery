<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

//QUERY DB TO GET ACCESS TOKEN INFORMATION
//$sql = "SELECT * FROM users WHERE fbid = " . $user_id;
$sql = "SELECT * FROM app";
$rs = $db->query($sql);
$row = $db->fetchArray($rs);
$total = $db->getRowsNum($rs);
$fanpage_access_token = $row['fanpage_access_token'];
$page_args = array(
	'access_token' => $fanpage_access_token 
);

if($access_token == "") { $access_token = $fanpage_access_token;}
$user_args = array(
	'access_token' => $access_token 
);


$total = 0;
$uploaderId = AppInfo::fanpage();
$uploader_full_name = AppInfo::fanpageName();

//GET REQUEST VARIABLES
$pid = mysql_real_escape_string($_REQUEST['pid']);


//QUERY DB FOR UPLOADERS ID
$sql = "SELECT fbId FROM photos WHERE photoId=" . $pid;
$rs = $db->query($sql);
$row = $db->fetchArray($rs);
$total = $db->getRowsNum($rs);
if($total > 0){
    $uploaderId = $row['fbId'];
}

//Photo variables
$pSource = "";
$pHeight = 0;
$pWidth = 0;
$pDate = date("Y-m-d H:i:s");
$pDescription = "";
$pObjectId = 0;
$pLike = 0; // user likes photo
$pLikeCount = 0; 

//Page variables
$pgName = "";
$pgProfile = "";
$pgId = 0;
$pgLikes = 0;
$pgLink = "";

if(isset($parsed_signed_request['page']['liked'])){
    $pgLike = $parsed_signed_request['page']['liked']; // user likes page
}

//GET uploaders full name
try{
    $uploaderResult = $facebook->api('/' . $uploaderId, 'get', $user_args);
    $uploader_full_name = $uploaderResult['name'];
    
} catch(FacebookApiException $e) {
    //$msg .= "<li>User - " . $e->getMessage() . "</li>";
}

//---------------------------------------------//
//Get Photo
try {
	$photo = $facebook->api('/'. $pid , 'get', $user_args);

    $pSource = $photo['source'];
    $pLink = $photo['link'];
    $pHeight = $photo['height'];
    $pWidth = $photo['width'];
    $pDate = $photo['created_time'];

    //dynamic photo description
    if(isset($photo['name'])){
        $pDescription = $photo['name'];
    } else {
        $pDescription = "";
    }

    //dynamic photo comments
    if(isset($photo['comments'])){
        $pComments = $photo['comments'];
    } else {
        $pComments = array();
    }

    //dynamic photo likes
    if(isset($photo['likes'])){
        $photoLikeData = $photo['likes']['data'];
        $pLikeCount = 0;

        //Search array to see if user Id is in list
        foreach ($photoLikeData as $value) { 
            $pLikeCount = $pLikeCount + 1;
            if (isset($value['id']) && $value['id'] == $user_id ) {
                $pLike = 1;
            }
        }
    }
    
    //Parse 'From' to get Fanpage details
    $pFanpage = $photo['from'];
    $pgName = $pFanpage['name'];
    $pgId = $pFanpage['id'];

} catch (FacebookApiException $e) {
    $msg .= "<li>Photo - " . $e->getMessage() . "</li>";
}
//---------------------------------------------//

//---------------------------------------------//
//Get Fanpage name
try {
	$fanpage = $facebook->api('/'. $pgId , 'get', $page_args);
    $pgName = $fanpage['name'];
    $pgLikes = $fanpage['likes'];
    $pgLink = $fanpage['link'];
    
} catch (FacebookApiException $e) {
    $msg .= "<li>Fanpage - " . $e->getMessage() . "</li>";
}
//---------------------------------------------//

?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title><?php echo $app_name; ?></title>
    <link rel="stylesheet" href="stylesheets/reset.css" media="all" type="text/css" />
    <link rel="stylesheet" href="stylesheets/facebook.css" media="all" type="text/css" />
    <link rel="stylesheet" href="stylesheets/photo.css" media="all" type="text/css" />

    <script type="text/javascript" src="javascript/jquery-1.9.1.min.js"></script>
    <!--[if IE]>
      <script type="text/javascript">
        var tags = ['header', 'section'];
        while(tags.length)
          document.createElement(tags.pop());
      </script>
    <![endif]-->
   </head>
<body class="fbbody">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=469855103086569";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

window.fbAsyncInit = function() {
    FB.init({
        appId      : '<?php echo AppInfo::appID(); ?>', // App ID
        channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        xfbml      : true // parse XFBML
    });
};
</script>

<div id="photoWrapper">

<?php
    

if($msg != ""){ 
    echo '<h1>'. $lbl_error_title . '</h1>';
    echo '<ul class="fberrorbox">' . $msg . '</ul>';
} else {

    //---------------------------------------------
    //Display Photo
    echo '<div id="photoMatt">';
    echo '<span class="photoLarge" style="background-image:url(' . $pSource . '); height:'.$pHeight.'px;"></span>';
    echo '</div>';
    //---------------------------------------------


    //---------------------------------------------
    //Display User who uploaded photo
    echo '<div id="photoBlock">';
    echo '<table id="fbtable">';
    echo '<tr>';
    echo '<td class="fbProfileBlock"><a href="https://www.facebook.com/'. $uploaderId .'" target="_blank"><img src="http://graph.facebook.com/'. $uploaderId .'/picture" style="width:50px; height:50px; border: 0px;" alt="'.  $uploader_full_name .'" /></a></td>';
    echo '<td>';
    echo '<table><tr><td style="padding:5px;" colspan="2"><a href="https://www.facebook.com/'. $uploaderId .'" target="_blank">'.  $uploader_full_name .'</a></td></tr>';
    echo '<tr><td style="padding:5px;">'.$pDescription.'</td></tr></table>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    //---------------------------------------------
    echo  '<br /><br />';
    echo '<span id="socialBlock">';

    //Like photo
    if ($user_id == 0) {
        //If user is NOT logged in
        echo '<a href="#" onclick="facebookLogin();">'. $btnLike .'</a>';

    } else {
        //If user is logged in
        if($pLike == 1){
            echo '<a title="'. $btnUnlikeComment .'" user_id="'. $user_id .'" object_id="'. $pid .'" like="DELETE" class="like-photo-action">'. $btnUnlike .'</a>';
        } else {
            echo '<a title="'. $btnLikeComment .'" user_id="'. $user_id .'" object_id="'. $pid .'" like="CREATE" class="like-photo-action">'. $btnLike .'</a>';
        }
    }

    //Comment link
    echo '<span class="fbSpacer">&#8226;</span>';
    if ($user_id == 0) {
        //If user is NOT logged in
        echo '<a href="#" onclick="facebookLogin();">'. $btnComment .'</a>';

    } else {
        //If user is logged in
        echo '<a href="#" onclick="commentFocus()">' . $btnComment . '</a>';
    }

    //Share link
    echo '<span class="fbSpacer">&#8226;</span>';
    echo '<a href="#" id="postToWall">' . $btnShare . '</a>';

    echo '</span>';



    //Like block
    if($pLikeCount > 0){
        echo '<div class="fbbluebox" id="likeCounter"><span class="fbUserLikeCount">';
        if($user_id > 0) {
            if($pLikeCount == 1 && $pLike == 1){
                echo '<div class="gridText"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /></div>';
                echo '<div class="gridTextCount"><span class="counter" data-count="' . $pLikeCount . '">' . $lblYouLike . '</span></div>';
                
            } else if ($pLikeCount == 1 && $pLike == 1){
                echo '<div class="gridText"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /></div>';
                echo '<div class="gridTextCount">'  . $lblYouAnd .' <span class="counter" data-count="' . $pLikeCount . '">' . $pLikeCount . '</span> ' .  $lblPersonLikes . '</div>';
            
            } else if ($pLikeCount > 1 && $pLike == 1){
                echo '<div class="gridText"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /></div>';
                echo '<div class="gridTextCount">'  . $lblYouAnd .' <span class="counter" data-count="' . $pLikeCount . '">' . $pLikeCount . '</span> ' .  $lblPeopleLike . '</div>';
            
            } else if ($pLikeCount == 1 && $pLike == 0){
                echo '<div class="gridText"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /></div>';
                echo '<div class="gridTextCount"><span class="counter" data-count="' . $pLikeCount . '">' . $pLikeCount . '</span> ' .  $lblPersonLikes . '</div>';
           
            } else {
                echo '<div class="gridText"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /></div>';
                echo '<div class="gridTextCount"><span class="counter" data-count="' . $pLikeCount . '">' . $pLikeCount . '</span> ' .  $lblPeopleLike . '</div>';
            }
            


        } else {
            if($pLikeCount == 1){
                 echo '<div class="gridText"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /></div>';
                 echo '<div class="gridTextCount"><span class="counter" data-count="' . $pLikeCount . '">' . $pLikeCount . '</span> ' .  $lblPersonLikes . '</div>';

            } else if ($pLikeCount > 1){
                 echo '<div class="gridText"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /></div>';
                 echo '<div class="gridTextCount"><span class="counter" data-count="' . $pLikeCount . '">' . $pLikeCount . '</span> ' .  $lblPeopleLike . '</div>';
            }
        }
        echo '</span></div>';
    } else {
        echo '<div class="fbblueboxNone" id="likeCounter"><span class="fbUserLikeCount"></span></div>';
    }


    //Comment block
    echo '<div id="view_comments">';

    //---------------------------------------------
    //Display Comments

    if(isset($pComments['data'])){
    $CommentsData = $pComments['data'];
        foreach ($CommentsData as $uComment) { 
    
    
            $commentId = $uComment['id'];
            $commentorData = $uComment['from'];
            $thumbnail = "http://graph.facebook.com/". $commentorData['id'] ."/picture";
            $userLink = "http://facebook.com/". $commentorData['id'];
            $userCommentDate = dateDiff(date('r', strtotime($uComment["created_time"])), date("Y-m-d H:i:s"), $locale);
            $userLikeComment = $uComment['user_likes'];
    
            echo '<div class="fbbluebox">';
            echo '<table id="fbCommenttable">';
            echo '<tr>';
            echo '<td class="fbCommentProfileBlock"><a href="'. $userLink .'" target="_blank"><img src="'.$thumbnail.'" width="40" height="40" border="0"></a></td>';
	        echo '<td>';
            echo '<span class="commentBlock">';
            if($commentorData['id'] == $user_id){
                echo '<a class="remove" comment_id="' . $commentId . '" title="delete"></a>';
            }
            echo '<a href="'. $userLink .'" target="_blank">'. $commentorData['name'] .'</a> ' . $uComment['message'] . '</span><br />';
	        echo '<span class="commentDate">';
            echo  $userCommentDate;
    
            if($user_id > 0) {
                echo  '<span class="fbSpacer">&#8226;</span>';
                echo  '<span class="fbuserLikeBlock">';
                if($uComment['user_likes'] == "true"){
                    if($uComment['can_remove'] == "true"){
                        echo '<a title="'. $btnUnlikeComment .'" user_id="'. $user_id .'" object_id="'. $commentId .'" like="DELETE" class="like-action">'. $btnUnlike .'</a>';
                    } else {
                        echo '<a title="'. $btnUnlikeComment .'">'. $btnUnlike .'</a>';
                    }
                } else {
                    echo '<a title="'. $btnLikeComment .'" user_id="'. $user_id .'" object_id="'. $commentId .'" like="CREATE" class="like-action">'. $btnLike .'</a>';
                }
                echo  '</span>';
            }
            echo '<span class="fbLikeBlock">';
            if($uComment['like_count'] > 0){
                if($uComment['like_count'] == 1){ $lblTmpPeople = $lblPersonLikes; } else { $lblTmpPeople = $lblPeopleLike; }
                echo '<span class="counterBlock"><span class="fbSpacer">&#8226;</span>';
                echo '<a title="'. $uComment['like_count'] .' '. $lblTmpPeople .'"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /><span class="counter" data-count="' . $uComment['like_count'] . '">'. $uComment['like_count'] .'</span></a></span>';
            } else { echo '<span class="counterBlock"><span class="counter" data-count="0"></span></span>'; }
            echo '</span>';
            echo '</span></td>';
            echo '</tr>';
            echo '</table>';
            echo '</div>';
        }
    }
    echo '</div>';

    //Comment box
    if($user_id > 0) {
        //Set thumbnail to logged in user
        $thumbnail = "http://graph.facebook.com/". $user_id ."/picture";
        $userLink = "http://facebook.com/". $user_id;

        echo '<div class="fbbluebox">';
        echo '<table id="fbCommenttable">';
        echo '<tr>';
        echo '<td class="fbCommentProfileBlock"><a href="'. $userLink .'" target="_blank"><img src="'.$thumbnail.'" width="40" height="40" border="0"></a></td>';
	    echo '<td><span class="comment_ui" id="comment_ui">';
        echo '<div class="loading"></div>';
        echo '<input type="text" maxlength="255" id="'. $pid .'" class="comment_box" value="Write a comment..." />';
        echo '</span></td>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';
    }

    //---------------------------------------------


    echo '<br /><br />';

    //---------------------------------------------
    //Display Fanpage
    echo '<table id="fbtable">';
    echo '<tr>';
    echo '<td class="fbProfileBlock"><a href="'. $pgLink .'" target="_blank"><img src="http://graph.facebook.com/'. $pgId .'/picture" style="width:50px; height:50px; border: 0px;" alt="'. $pgName .'" /></a></td>';
    echo '<td>';
    echo '<table><tr><td style="padding:5px;" colspan="2"><a href="'. $pgLink .'" target="_blank">'. $pgName .'</a></td></tr>';
    echo '<tr><td style="padding:5px;">';
    echo '<div class="fb-like" data-href="http://www.facebook.com/RoyaleKittensChatons" data-send="false" data-layout="button_count" data-show-faces="false"></div>';
    echo '</td><td style="padding:5px; vertical-align:middle;">';
    echo '<a title="' . $pDate . '">'. dateDiff(strftime('%Y-%m-%d %H:%M:%S', strtotime('now')), $pDate, $locale) . '</a>';
    echo '</td></tr></table>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    //---------------------------------------------

}

echo '<br /><p class="caption">'. $lbl_privacy .'</p>';
?>
</div>
</div>

<script type="text/javascript">
var protocol = "http://";
if (location.protocol === 'https:') {
    protocol = "https://";
}

$(document).ready(function(){
	$(".loading").hide();
    var x = $('#photoWrapper').width();
    var y = $('#photoWrapper').height() + 60;
    //var y = 700;
    parent.$.colorbox.resize({height:y});
    //parent.updateHeight(y + 400);
    //window.frameElement.height = y + 400 +"px";
    //parent.document.body.clientHeight = y + 400 +"px";

    $(".fbuserLikeBlock").on('click', ".like-action", function(){
	  var object_id = $(this).attr("object_id");
	  var user_id = $(this).attr("user_id");
	  var like = $(this).attr("like");
	  var DATA = 'object_id=' + object_id + '&user_id=' + user_id + '&action=' + like + '&signed_request=<?php echo($_SESSION['signed_request']);?>';
      var txtLike = "<?php echo $btnLike;?>";
      var txtUnlike = "<?php echo $btnUnlike;?>";
      var personLikes = '<?php echo $lblPersonLikes;?>';
      var peopleLikes = '<?php echo $lblPeopleLike;?>';
      var lblPeople = personLikes;
	  var element = $(this);
      var currBlock =$(this).parent().nextUntil().find('.counterBlock');
      var curr =$(this).parent().nextUntil().find('.counter');
      var currCounter = parseInt(curr.attr('data-count'));
      document.body.style.cursor = 'wait';
    
		$.ajax({
				type: "POST",
				url: "like.php",
				data: DATA,
				cache: false,
				success: function (response) {
                    if(response == "DELETED"){
                        var newCurrCounter = currCounter - 1;
                        if(newCurrCounter > 1){ lblPeople = peopleLikes;}
	                    $(element).replaceWith('<a user_id="' + user_id + '" object_id="' + object_id + '" like="CREATE" class="like-action">' + txtLike + '</a>');
                        
                        if(newCurrCounter < 1){ 
                            $(currBlock).html('<span class="counterBlock"><span class="counter" data-count="0"></span></span>'); 
                        } else {
                            $(currBlock).html('<span class="counterBlock"><span class="fbSpacer">&#8226;</span><a title="' + newCurrCounter + ' ' + lblPeople + '"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /><span class="counter" data-count="' + newCurrCounter + '">' + newCurrCounter + '</span></a></span>');
                        }

                    } else if (response == "CREATED"){
                        var newCurrCounter = currCounter + 1;
                        if(newCurrCounter > 1){ lblPeople = peopleLikes;}
	                    $(element).replaceWith('<a user_id="' + user_id + '" object_id="' + object_id + '" like="DELETE" class="like-action">' + txtUnlike + '</a>');
                        $(currBlock).html('<span class="counterBlock"><span class="fbSpacer">&#8226;</span><a title="' + newCurrCounter + ' ' + lblPeople + '"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /><span class="counter" data-count="' + newCurrCounter + '">' + newCurrCounter + '</span></a></span>');

                    } else {
                        alert(response);
                    }
                    document.body.style.cursor = 'default';
                }
		});
		return false;
	});

    $("#socialBlock").on('click', ".like-photo-action", function(){
	  var object_id = $(this).attr("object_id");
	  var user_id = $(this).attr("user_id");
	  var like = $(this).attr("like");
	  var DATA = 'object_id=' + object_id + '&user_id=' + user_id + '&action=' + like + '&signed_request=<?php echo($_SESSION['signed_request']);?>';
      var txtLike = "<?php echo $btnLike;?>";
      var txtUnlike = "<?php echo $btnUnlike;?>";
	  var element = $(this);
      var curr =$("#likeCounter").find('.counter');
      var currCounter = parseInt(curr.attr('data-count'));
      if(currCounter == ''){ currCounter = 0; }
      var personLikes = '<?php echo $lblPersonLikes;?>';
      var peopleLikes = '<?php echo $lblPeopleLike;?>';
      var youLike = '<?php echo $lblYouAnd;?>';
      var youLikeThis = '<?php echo $lblYouLike;?>';
      document.body.style.cursor = 'wait';

		$.ajax({
				type: "POST",
				url: "like.php",
				data: DATA,
				cache: false,
				success: function (response) {
                    if(response == "DELETED"){
	                    $(element).replaceWith('<a user_id="' + user_id + '" object_id="' + object_id + '" like="CREATE" class="like-action">' + txtLike + '</a>');
                        var newCurrCounter = currCounter - 1;
                        if(newCurrCounter > 1){ lblPeople = peopleLikes;}
                        if(newCurrCounter < 1){ 
                            $("#likeCounter").addClass('fbblueboxNone');
                            $(".gridTextCount").html('<span class="counter" data-count="0"></span>'); 
                        } else if(newCurrCounter > 1){
                            $(".gridTextCount").html('<span class="counter" data-count="' + newCurrCounter + '">' + newCurrCounter + '</span> ' + lblPeople);
                        } else {
                            $(".gridTextCount").html('<span class="counter" data-count="' + newCurrCounter + '">' + newCurrCounter + '</span> ' + personLikes);
                        }

                    } else if (response == "CREATED"){
	                    $(element).replaceWith('<a user_id="' + user_id + '" object_id="' + object_id + '" like="DELETE" class="like-action">' + txtUnlike + '</a>');
                        var newCurrCounter = currCounter + 1;
                        if(newCurrCounter > 2){ lblPeople = peopleLikes;}
                        if(newCurrCounter > 1){
                            var newnCounter = newCurrCounter - 1;
                            $(".gridTextCount").html( youLike + ' <span class="counter" data-count="' + newCurrCounter + '">' + currCounter + '</span> ' + lblPeople);
                        } else {
                            $("#likeCounter").addClass('fbbluebox');
                            $(".gridTextCount").html( youLikeThis + '<span class="counter" data-count="' + newCurrCounter + '"></span>');
                        }

                    } else {
                        alert(response);
                    }
                    document.body.style.cursor = 'default';
                }
		});
		return false;
	});

    $(".fbbluebox").on('click', ".remove", function(){
	  var comment_id = $(this).attr("comment_id");
	  var DATA = 'comment_id=' + comment_id + '&signed_request=<?php echo($_SESSION['signed_request']);?>';
      var parentTag = $(this).parents(".commentBlock");
      var blockParentTag = parentTag.parents(".fbbluebox");
      document.body.style.cursor = 'wait';
		$.ajax({
				type: "POST",
				url: "remove_comment.php",
				data: DATA,
				cache: false,
				success: function (response) {
                    $(blockParentTag).remove();
                    document.body.style.cursor = 'default';
                }
		});
		return false;
	});


    $(".comment_box").focus(function(){
	    $(this).filter(function(){
	        return $(this).val() == "" || $(this).val() == "Write a comment..."
	    }).val("").css("color","#000000");
	});
	
    $(".comment_box").blur(function(){
	    $(this).filter(function(){
	        return $(this).val() == ""
	    }).val("Write a comment...").css("color","#808080");
	});

    $(".comment_box").keypress(function(e) {
		var ID = $(this).attr("id");
		code= (e.keyCode ? e.keyCode : e.which);
		if (code == 13) {
			$(".loading").show();
            document.body.style.cursor = 'wait';
			var status=$(this).val();

			if(status == "Write a comment..."){
				$(".loading").hide();
                document.body.style.cursor = 'default';

			}else{
				var DATA = 'status=' + status + '&pid=' + ID + '&signed_request=<?php echo($_SESSION['signed_request']);?>';
				$.ajax({
					type: "POST",
					url: "post_comment.php",
					data: DATA,
					cache: false,
					success: function(data){
						$(".loading").hide();
						$(".comment_box").val("Write a comment...").css("color","#808080").css("height","15px").blur();
						$("#view_comments").append(data);

                        //Resize canvas
                        document.body.style.cursor = 'default';
                        var x = $('#photoWrapper').width();
                        var y = $('#photoWrapper').height() + 100;
                        parent.$.colorbox.resize({height:y});
					}
				});
			}
			return false;
		}
	});


    // Set up so we handle click on the buttons
    $('#postToWall').click(function() {
        FB.ui(
        {
            method: 'stream.publish',
		    message: 'test',
		    display: 'dialog',
		    attachment: {
			    name: ('<?php echo $ShareTitle;?>'),
			    description: ('<?php echo $ShareDescription;?>'),			
			    href: '<?php echo AppInfo::fanpageUrl();?>',
				    media: [{
						    "type": "image",
						    "src":  '<?php echo $SharePhoto;?>',
						    "href": '<?php echo AppInfo::fanpageUrl();?>'
						    }]
		    },
				
		    action_links: [
			    { text: '<?php echo $ShareLinkTitle;?>', href: '<?php echo AppInfo::fanpageUrl();?>' }
		    ]
        },
        function (response) {
            // If response is null the user canceled the dialog
            if (response != null) {
            //logResponse(response);
            }
        }
        );
    });
});

function commentFocus() {
    $(".comment_box").focus();
    $(".comment_box").val() == "";
    $(".comment_box").css("color","#000000");
    return false;
}

function facebookLogin()
{
    var popupWidth=500;
    var popupHeight=300;
    var xPosition=($(window).width()-popupWidth)/2;
    var yPosition=($(window).height()-popupHeight)/2;
    var loginUrl="http://www.facebook.com/dialog/oauth/?"+
            "scope=user_photos,user_likes,publish_stream&"+
        "client_id=<?php echo AppInfo::appID(); ?>&"+
        "redirect_uri="+protocol+"<?php echo $_SERVER["HTTP_HOST"]; ?>/loginHandler.php&"+
        "response_type=code&"+
        "state=<?php if(isset($_SESSION['state'])){ echo $_SESSION['state'];} ?>&"+
        "display=popup";

        var loginUrl="install-app.php";
        
    facebookLoginWindow=window.open(loginUrl, "LoginWindow", 
        "location=1,scrollbars=1,"+
        "width="+popupWidth+",height="+popupHeight+","+
        "left="+xPosition+",top="+yPosition);
         
    loginWindowTimer=setInterval(onTimerCallbackToCheckLoginWindowClosure, 1000);
}

function onTimerCallbackToCheckLoginWindowClosure() 
{
    // If the window is closed, then reinit Facebook 
    if (facebookLoginWindow.closed) 
    {
        clearInterval(loginWindowTimer);
        //FB.getLoginStatus(onFacebookLoginStatus);
        window.top.location.href = "<?php echo AppInfo::fanpageUrl();?>";
    }
}
</script>
</body>
</html>
<?php require_once('close.php');?>