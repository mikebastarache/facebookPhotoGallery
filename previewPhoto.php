<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

$pData = array();
$pSource = "";
$pHeight = 0;
$pWidth = 0;
$pDate = date("Y-m-d H:i:s");
$pDescription = "";
$user_args = array(
	'access_token' => $access_token 
);

//GET REQUEST VARIABLES
$aid = mysql_real_escape_string($_GET['aid']);
$pid = mysql_real_escape_string($_GET['pid']);

if ($user_id) {
    //GET Album info from Facebook
    try{
        $photo = $facebook->api('/' . $pid, 'get', $user_args);
       
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
<div id="photoWrapper">
<?php 
 if($msg != ""){ 
    echo '<h1>'. $lbl_error_title . '</h1>';
    echo '<p class="fberrorbox">' . $msg . '</p>';

} else if ($user_id == 0){ 
    echo '<h1>'. $lbl_error_title . '</h1>';
    echo '<p class="fberrorbox">' . $lbl_session_description . '</p>';

} else { 
    //---------------------------------------------
    //Display Photo
    echo '<div id="photoMatt">';
    echo '<span class="photoLarge" style="background-image:url(' . $pSource . '); height:'.$pHeight.'px;"></span>';
    echo '</div>';
    //---------------------------------------------

    
    echo '<div id="photoBlock">';
    echo '<a href="photos.php?aid=' . $aid . '&signed_request=' . $signed_request . '" class="btn-grey">' . $btn_back . '</a>';
    echo '<span style="float:right;"><a href="javascript: addPhoto(' . $pid . ');" class="btn-blue" style="color:white;">' . $btn_add . '</a></span>';
    echo '<br /><br /><p>'.$pDescription.'</p>';
    echo '<br /><p class="caption">'. $lbl_privacy .'</p>';
    echo '</div>';
} 
?>
</div>
</div>

<script>
    $(document).ready(function(){
        var x = $('#photoWrapper').width();
        var y = $('#photoWrapper').height() + 120;

        parent.$.colorbox.resize({height:y});

    });

    function addPhoto(id) {
        document.body.style.cursor='wait'; 
        window.location = "addFbPhoto.php?pid=" + id + "&signed_request=<?php echo $signed_request;?>";
        return true;
    }
</script>
</body>
</html>
