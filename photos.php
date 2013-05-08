<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

$pData = array();
$albumTitle = "";
$user_args = array(
	'access_token' => $access_token 
);

//GET REQUEST VARIABLES
$aid = mysql_real_escape_string($_GET['aid']);

if ($user_id) {
    //GET Album info from Facebook
    try{
        $albumInfoResult = $facebook->api('/' . $aid, 'get', $user_args);
        $albumTitle = $albumInfoResult['name'];

    } catch(FacebookApiException $e) {
        $msg .= "<li>" . $e->getMessage() . "</li>";
    }

    //GET Photos from Facebook
    try{
        $albumResult = $facebook->api('/' . $aid .'/photos', 'get', $user_args);
        $pData = $albumResult['data'];

    } catch(FacebookApiException $e) {
        $msg .= "<li>" . $e->getMessage() . "</li>";
    }         
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
    <link rel="stylesheet" href="stylesheets/file.css" media="all" type="text/css" />
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
<div id="photoBlock">
<?php 
 if($msg != ""){ 
    echo '<h1>'. $lbl_error_title . '</h1>';
    echo '<ul class="fberrorbox">' . $msg . '</ul>';

} else if ($user_id == 0){ 
    echo '<h1>'. $lbl_error_title . '</h1>';
    echo '<ul class="fberrorbox">' . $lbl_session_description . '</ul>';

} else { 
    echo '<h1>' . $albumTitle . '</h1>';
    echo '<span style="float:right;"><a href="album.php?signed_request=' . $signed_request . '" class="btn-grey">' . $btn_back . '</a></span>';
    echo '<span id="fbTitleBar">'. $lbl_photos_instructions . '</span>';
    echo '<ul id="fbPhotos">';
    foreach($pData as $photo)
    {
        $p_id = $photo['id'];
        $p_link = $photo['link'];
        $p_photo = $photo['source'];
        $p_photo = $http . 'graph.facebook.com/' . $p_id . '/picture?width=160&height=124&access_token=' . $access_token;
        
        echo '<li>';
        echo '<a href="previewPhoto.php?aid=' . $aid . '&pid=' . $p_id . '&signed_request=' . $signed_request . '">';
        echo '<span class="fbAlbumFrame" style="background-image:url(' . $p_photo . ')" />';
        echo '</a>';
        echo '<span style="float:right;"><a href="javascript: addPhoto(' . $p_id . ');" class="btn-blue" style="color:white;">' . $btn_add . '</a></span>';
        echo '</li>';
    } 
    echo '</ul>';
} 
?>
<br />

<p class="caption"><?php echo $lbl_privacy;?></p>
</div>

<script>
    $(document).ready(function () {
        var x = $('#photoBlock').width();
        var y = $('#photoBlock').height() + 120;

        parent.$.colorbox.resize({ height: y });

    });

    function addPhoto(id) {
        document.body.style.cursor='wait'; 
        window.location = "addFbPhoto.php?pid=" + id + "&signed_request=<?php echo $signed_request;?>";
        return true;
    }
</script>
</body>
</html>
<?php require_once('close.php');?>
