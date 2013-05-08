<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

if ($user_id) {
  try {
    // Fetch the viewer's basic information
    $basic = $facebook->api('/me');

  } catch (FacebookApiException $e) {
    // If the call fails we check if we still have a user. The user will be
    // cleared if the error is because of an invalid accesstoken
    if (!$facebook->getUser()) {
      header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
      exit();
    }
  }

} 

//This is used to verify the correct user is 'authenticating' the app
if ($user_id == 0) {
    $_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection
} else {
    $_SESSION['state'] = "";
}
?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title><?php echo $app_name; ?></title>
    <link rel="stylesheet" href="stylesheets/reset.css" media="all" type="text/css" />
    <link rel="stylesheet" href="stylesheets/facebook.css" media="all" type="text/css" />
    <link rel="stylesheet" href="stylesheets/base.css" media="all" type="text/css" />
    <link rel="stylesheet" href="stylesheets/forms.css" media="all" type="text/css" />

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
    <script type="text/javascript">
      var interval;
      var facebookLoginWindow;
      var facebookIsLoggedIn = false;
      var loginWindowTimer;
      var uid;
      var accessToken;
      var protocol = "http://";
      var y_mPos = 200;
      var currentHeight = 800;

      if (location.protocol === 'https:') {
        protocol = "https://";
      }

      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          //window.location = window.location;
        });

        interval = setInterval(scrollDown, 1000);
        FB.Canvas.setAutoGrow();
      };

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));

    function scrollDown(){
	    FB.Canvas.getPageInfo(function(pageInfo){
 
		    var documentHeight = getDocHeight();
 
		    var scrollTop2 = ( pageInfo.scrollTop - pageInfo.offsetTop );
		    var clientHeight2 = ( pageInfo.clientHeight - pageInfo.offsetTop );
		
		    if ( ( documentHeight - clientHeight2 - pageInfo.scrollTop ) < 0 )
		    {
			    //Load new tiles
                loadData();
                currentHeight = getDocHeight();
		    }

             // grab the initial top offset of the navigation 
            var sticky_navigation_offset_top = $('#sticky_navigation').offset().top;
            var scroll_top = pageInfo.scrollTop; // our current vertical position from the top
            var yPos = pageInfo.scrollTop-30;
            y_mPos = pageInfo.scrollTop;
        
            // if we've scrolled more than the navigation, change its position to fixed to stick to top,
            // otherwise change it back to relative
            if (scroll_top > 200) {
                $('#sticky_navigation').css({ 'position': 'fixed', 'top': yPos, 'left': 0, 'background-image': 'url(images/nav-background.png)' });
            } else {
                $('#sticky_navigation').css({ 'position': 'relative', 'top': 0, 'background-image': 'url(images/nav-background-solid.png)' });
            }

	    });

        
        FB.getLoginStatus(function(response) {
          if (response.status === 'connected') {
            // the user is logged in and has authenticated your
            // app, and response.authResponse supplies
            // the user's ID, a valid access token, a signed
            // request, and the time the access token 
            // and signed request each expire
            var uid = response.authResponse.userID;
            var accessToken = response.authResponse.accessToken;
            var facebookIsLoggedIn = true;
          } else if (response.status === 'not_authorized') {
            // the user is logged in to Facebook, 
            // but has not authenticated your app
          } else {
            // the user isn't logged in to Facebook.
          }
         });
    };

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
            "state=<?php echo $_SESSION['state']; ?>&"+
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

  <img src="images/<?php echo $img_header;?>" alt="<?php echo $alt_header;?>" width="810" height="262">

  <table border="0" id="sticky_navigation">
    <tr>
    	<td style="width: 17px;"><img src="images/nav-left.png" width="17" height="69" alt="&nbsp;" /></td>
        <td class="nav-container">
        	<span style="float: left;"><?php echo $lbl_sort;?>&nbsp;</span>
            <div class="styled-select">
                <select id="sort" name="sort">
                    <?php if ($user_id == 0) 
                    {
                        echo '<option value="0">' . $opt_all . '</option>';
                        echo '<option value="1">' . $opt_top . '</option>';
                        echo '<option value="2">' . $opt_royale . '</option>';

                        //links for upload buttons
                        $link_upload = 'javascript: facebookLogin();';
                        $link_album = 'javascript: facebookLogin();';
                        $link_upload_id = 'uploadPhotoLogin';
                        $link_album_id = 'addPhotoLogin';

                     } else {
                        echo '<option value="0">' . $opt_all . '</option>';
                        echo '<option value="1">' . $opt_top . '</option>';
                        echo '<option value="2">' . $opt_royale . '</option>';
                        echo '<option value="3">' . $opt_my . '</option>';
                        echo '<option value="4">' . $opt_friends . '</option>';

                        //links for upload buttons
                        $link_upload = 'upload.php?signed_request=' . $signed_request;
                        $link_album = 'album.php?signed_request=' . $signed_request;
                        $link_upload_id = 'uploadPhoto';
                        $link_album_id = 'addPhoto';
                     } 
                       
                    ?>
                </select>
            </div>
        </td>
        <td class="nav-container" style="width: 420px; text-align: right;">
        	<a href="<?php echo $link_upload;?>" id="<?php echo $link_upload_id;?>"><img src="images/<?php echo $img_uploadPhoto;?>" alt="<?php echo $alt_uploadPhoto;?>" height="32" class="nav-btn"></a>
            <a href="<?php echo $link_album;?>" id="<?php echo $link_album_id;?>"><img src="images/<?php echo $img_adFbPhoto;?>" alt="<?php echo $alt_adFbPhoto;?>" height="32" class="nav-btn"></a>
        </td>
    	<td style="width: 17px;"><img src="images/nav-right.png" width="17" height="69" alt="&nbsp;" /></td>
    </tr>
    </table>

    <div id="main" role="main">
        <ul id="tiles"></ul>
    </div>
      
    <div id="loading"><img src="images/loadingAnimation.gif" width="208" height="13" alt="&nbsp;"></div>

<link rel="stylesheet" href="stylesheets/colorbox.css" />

<!-- Include the plug-in -->
<script src="javascript/jquery.wookmark.js"></script>
<script src="javascript/jquery.imagesloaded.js"></script>
<script src="javascript/jquery.colorbox-min.js"></script>
<script type="text/javascript">
    var handler = null;
    var isLoading = false;
    var sort = 0;

    // Prepare layout options.
    var options = {
        autoResize: true, // This will auto-update the layout when the browser window is resized.
        container: $('#main'), // Optional, used for some extra CSS styling
        offset: 2, // Optional, the distance between grid items
        itemWidth: 250 // Optional, the width of a grid item
    };

    function getDocHeight() {
        var D = document;
        return Math.max(
            Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
            Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
            Math.max(D.body.clientHeight, D.documentElement.clientHeight)
        );
    }

    /**
    * When scrolled all the way to the bottom, add more tiles.
    */
    function onScroll(event) {
        // Only check when we're not still waiting for data.
        if (!isLoading) {
            // Check if we're within 100 pixels of the bottom edge of the broser window.
            //alert($(window).scrollTop() + " - " + $(window).height() + " - " + $(document).height());
            var closeToBottom = ($(window).scrollTop() + $(window).height() > $(document).height() - 100);
            if (closeToBottom) {
                loadData();
            }
        }
    };

    /**
    * Refreshes the layout.
    */
    function applyLayout() {
        // Create a new layout handler.
        handler = $('#tiles li');
        handler.wookmark(options);
    };

    /**
    * Loads data from the API.
    */
    function loadData() {
        $('#loading').show();

        var LastRecord = $(".gallery-item:last").attr("id");
        if (!$(".gallery-item:last").attr("id")) {
            LastRecord = 0;
        }
        var DATA = 'LastRecord=' + LastRecord + '&sort=' + sort;
        if (isLoading == false) {
            isLoading = true;
            $.ajax({
                type: "POST",
                url: "getMoreTiles.php",
                data: DATA,
                cache: false,
                success: onLoadData
            });
        }
    };

    /**
    * Receives data from the API, creates HTML for images and updates the layout
    */
    function onLoadData(data) {
        isLoading = false;
        $('#loading').hide();

        if (data != "") {
            // Add image HTML to the page.
            $('#tiles').append(data);
            applyLayout();
        } else {
            applyLayout();
            if ($('#tiles').html() == "" && $('#sort').val() == "3"){
                $('#tiles').html('<?php echo "<br><br>" . $msg_no_photos;?>');
            } else if ($('#tiles').html() == "" && $('#sort').val() == "4"){
                $('#tiles').html('<?php echo "<br><br>" . $msg_no_friend_photos . " <a href=\"#\" class=\"btn-blue\" id=\"sendToFriends\" style=\"color:white;\">" . $msg_no_friend_photos_btn . "</a>";?>');
            }
            //clearInterval(interval);
        }
    };

    var closeEvent = function () {
        console.log('refresh tiles to my photos');
        $('#tiles').empty();
        FB.Canvas.setSize({ width: 810, height: 800 });
        FB.Canvas.scrollTo(0,0);
        sort = 3;
        $('[name=sort]').val(3);
        loadData();
    };

    var closeModalEvent = function () {
        FB.Canvas.setSize({ width: 810, height: currentHeight + "px" });
        loadData();
    };

    $(document).ready(new function () {
        //$(".iframe").colorbox({ iframe: true, width: "90%", height: "600", fixed: true });

		$("#uploadPhoto").colorbox({ iframe: true, width: "90%", height: "300", top: y_mPos + "px", onClosed: function () { closeEvent() } });
        $("#addPhoto").colorbox({ iframe: true, width: "665", height: "400", top: y_mPos + "px", onClosed: function () { closeEvent() } });

        
        $("#tiles").on('click', ".iframe", function(){
           $.colorbox({ href: "getPhoto.php?pid=" + $(this).attr("pid"), iframe:true, width:"90%", height:"800", top: y_mPos + "px", onClosed: function () { closeModalEvent() } });
        });

        // Capture scroll event.
        $(document).bind('scroll', onScroll);

        //load data
        loadData();

        // grab the initial top offset of the navigation 
        var sticky_navigation_offset_top = $('#sticky_navigation').offset().top;

        // our function that decides weather the navigation bar should have "fixed" css position or not.
        var sticky_navigation = function () {
            var scroll_top = $(window).scrollTop(); // our current vertical position from the top

            // if we've scrolled more than the navigation, change its position to fixed to stick to top,
            // otherwise change it back to relative
            if (scroll_top > sticky_navigation_offset_top) {
                $('#sticky_navigation').css({ 'position': 'fixed', 'top': 0, 'left': 0, 'background-image': 'url(images/nav-background.png)'});
            } else {
                $('#sticky_navigation').css({ 'position': 'relative', 'background-image': 'url(images/nav-background-solid.png)' });
            }
        };

        // run our function on load
        sticky_navigation();

        // and run it again every time you scroll
        $(window).scroll(function () {
            sticky_navigation();
        });

    
        //Navigation sort
        $('#sort').change(function(event) {        
            $('#tiles').empty();
            FB.Canvas.setSize({ width: 810, height: 800 });
            FB.Canvas.scrollTo(0,0);
            sort =  this.selectedIndex;
            loadData();
        });

        $("#tiles").on('click', ".like-action", function(){
            var object_id = $(this).attr("object_id");
	        var user_id = $(this).attr("user_id");
	        var like = $(this).attr("like");
	        var DATA = 'object_id=' + object_id + '&user_id=' + user_id + '&action=' + like + '&signed_request=<?php echo($_SESSION['signed_request']);?>';
            var txtLike = "<?php echo $btnLike;?>";
            var txtUnlike = "<?php echo $btnUnlike;?>";
	        var element = $(this);
            var currBlock =$(this).parent().prev().find('.gridTextCount');
            var curr =$(this).parent().prev().find('.counter');
            var currCounter = parseInt(curr.attr('data-count'));
            var personLikes = '<?php echo $lblPersonLikes;?>';
            var peopleLikes = '<?php echo $lblPeopleLike;?>';
            var lblPeople = personLikes;
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
                                $(currBlock).html('<span class="counter" data-count="0"></span>'); 
                            } else {
                                $(currBlock).html('<span class="counter" data-count="' + newCurrCounter + '">' + newCurrCounter + '</span> ' + lblPeople);
                            }
                            
                        } else if (response == "CREATED"){
	                        $(element).replaceWith('<a user_id="' + user_id + '" object_id="' + object_id + '" like="DELETE" class="like-action">' + txtUnlike + '</a>');                            
                            var newCurrCounter = currCounter + 1;
                            if(newCurrCounter > 1){ lblPeople = peopleLikes;}
                            $(currBlock).html('<span class="counter" data-count="' + newCurrCounter + '">' + newCurrCounter + '</span> ' + lblPeople );

                        } else {
                            alert(response);
                        }
                        document.body.style.cursor = 'default';
                    }
		    });
		    return false;
	    });

        $("#tiles").on('click', "#sendToFriends", function(){
            FB.ui(
            {
                method : 'send',
                name: '<?php echo $share_name; ?>',
		        description: ('<?php echo $share_description; ?>'),	
                link   : '<?php echo AppInfo::fanpageUrl(); ?>',
                picture : '<?php echo $SharePhoto; ?>'
            },
            function (response) {
                // If response is null the user canceled the dialog
                if (response != null) {
                //logResponse(response);
                }
            });
        });

    });        
</script>
</body>
</html>
<?php require_once('close.php');?>