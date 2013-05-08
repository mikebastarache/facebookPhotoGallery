<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

$aData = array();
$user_args = array(
	'access_token' => $access_token 
);

if ($user_id) {
    //GET Albums from Facebook
    try{
        $albumResult = $facebook->api('/me/albums', 'get', $user_args);
        $aData = $albumResult['data'];

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
 echo "<h1>" . $lbl_albums_album . "</h1>";
 if($msg != ""){ 
    echo '<h1>'. $lbl_error_title . '</h1>';
    echo '<p class="fberrorbox">' . $msg . '</p>';

} else if ($user_id == 0){ 
    echo '<h1>'. $lbl_error_title . '</h1>';
    echo '<p class="fberrorbox">' . $lbl_session_description . '</p>';

} else { 
    echo '<span id="fbTitleBar">'. $lbl_title_album . '</span>';
    echo '<ul id="fbAlbums">';
    foreach($aData as $album)
    {
        if(isset($album["id"])){ $a_id = $album['id']; } else { $a_id = 0; }
        if(isset($album["name"])){ $a_name = $album['name']; } else { $a_name = ''; }
        if(isset($album["link"])){ $a_link = $album['link']; } else { $a_link = ''; }
        
        $a_cover_photo = 0;
        $coverImage = "images/fb-album-nocover.png";
        if(isset($album['cover_photo'])){
            $a_cover_photo = $album['cover_photo'];
        }

        $a_count = 0;
        if(isset($album['count'])){
            $a_count = $album['count'];
        }

        // RETRIEVE THE ALBUM COVER IMAGE
        if($a_cover_photo > 0){
            $coverImage = $http . 'graph.facebook.com/' . $a_cover_photo . '/picture?width=160&height=124&access_token=' . $access_token;
        }
        echo '<li>';
        echo '<a href="photos.php?aid=' . $a_id . '&signed_request=' . $signed_request . '"><span class="fbAlbumFrame" style="background-image:url(' . $coverImage . ')" /></a>';
        echo '<a href="photos.php?aid=' . $a_id . '&signed_request=' . $signed_request . '">' . $a_name .'</a>';
        echo '<br /><span>' . $a_count . ' photos</span>';
        echo '</li>';
    } 
    echo '</ul>';
} 
?>
<br />

<p class="caption"><?php echo $lbl_privacy;?></p>
</div>

<script>
$(document).ready(function(){
    var x = $('#photoBlock').width();
    var y = $('#photoBlock').height() + 120;

    parent.$.colorbox.resize({height:y});

});
</script>
</body>
</html>
<?php require_once('close.php');?>