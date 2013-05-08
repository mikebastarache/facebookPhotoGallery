<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

$fanpageUrl = AppInfo::fanpageUrl();
$user_full_name = "my full name";
$msg = "";
$pSource = "";
$photoSource = "";
$file = "";
$photoHeight = 0;
$photoWidth = 0;
$photoDescription = "";
$share_link = 'https://www.facebook.com/photo.php?fbid=';
$share_icon = AppInfo::getUrl() . 'images/ShareIcon_90.jpg';
$user_args = array(
	'access_token' => $access_token 
);

//GET REQUEST VARIABLES
$pid = mysql_real_escape_string($_REQUEST['pid']);

if ($user_id) {
  
    $facebook->setFileUploadSupport(true);

    //QUERY DB TO GET ACCESS TOKEN INFORMATION
    $sql = "SELECT * FROM app";
    $rs = $db->query($sql);
    $row = $db->fetchArray($rs);
    $total = $db->getRowsNum($rs);
    $fanpage_access_token = $row['fanpage_access_token'];

    if ($user_id != 0) {
  
        //GET users full name
        try{
            $userResult = $facebook->api('/me', 'get', $user_args);
            $user_full_name = $userResult['name'];

        } catch(FacebookApiException $e) {
            $msg .= "<li>" . $e->getMessage() . "</li>";
        }


        //GET PHOTO FROM USER FACEBOOK ALBUM
        try{
            $photo = $facebook->api('/' . $pid, 'get', $user_args);
            $pSource = $photo['source'];
            $file = 'uploads/' . $pid . ".jpg";

            //save photo to local server
            file_put_contents($file, file_get_contents($pSource));

        } catch(FacebookApiException $e) {
            $msg .= "<li>" . $e->getMessage() . "</li>";
        }

        //If users image is saved locally, ready to upload to Facebook album
        if($file != ""){

	        $args = array(
	 		    'owner' => $user_id,
			    'message' => $share_description2 . ' ' . $fanpageUrl,
			    'image' => '@'.realpath($file),
			    'aid' => AppInfo::albumID(),
			    'no_story' => AppInfo::postFanpage(),
			    'access_token' => $fanpage_access_token 
	        );

            //UPLOAD PHOTO TO FACEBOOK FANPAGE ALBUM
            try{
                $photo = $facebook->api('/'. AppInfo::albumID() . '/photos', 'post', $args);
                
                //GET FACEBOOK PHOTO ID
                $pid = $photo['id'];
                $share_link .= $pid;

                //INSERT INFORMATION INTO DB
                $photoDate=date("Y-m-d H:i:s");
                
                //IF ADMIN IS UPLOADING IMAGES, USER PAGE ID (ROYALE) USED IN DB
                $adminIds = explode(',', AppInfo::adminID());
                $uploader_id = $user_id;
                if (in_array($user_id,$adminIds)) {
                    $uploader_id = AppInfo::fanpage();
                }

                try {
                    //SETUP DB
                    $sql = "INSERT INTO photos (fbId,photo,photoId,albumId,dateCreated) values ($uploader_id,'$file',$pid," . AppInfo::albumID() . ",'$photoDate')";
                    $rs = $db->query($sql);

                } catch (Exception $e) {
                    $msg .= "<li>MySQL INSERT - " . $e->getMessage() . "</li>";
                }
                
            } catch(FacebookApiException $e) {
                $msg .= "<li>" . $e->getMessage() . "</li>";
            }

            /*
            // RETRIEVE THE PHOTOS INFORMATION TO DISPLAY IMAGE
            try {
                $photoDetails = $facebook->api('/'. $pid , 'get', $user_args);

                $photoSource = $photoDetails['source'];
                $photoHeight = $photoDetails['height'];
                $photoWidth = $photoDetails['width'];
                if(isset($photoDetails['name'])){
                    $photoDescription = $photoDetails['name'];
                }

            } catch(FacebookApiException $e) {
                $msg .= "<li>" . $e->getMessage() . "</li>";
            }
            */

            // POST TO USERS FEED THAT THEY UPLOADED PHOTO
            try {
                $attachment = array(
                    'message' => $share_description2,
                    'name' => $share_name,
                    'caption' => $share_caption,
                    'description' => $share_description,
                    'link' => $fanpageUrl,
                    'picture' => $share_icon,
                    'actions' => array(
                        array(
                            'name' => $share_name,
                            'link' => $fanpageUrl
                        )
                    )
                );

                if(AppInfo::postUserFeed() == 1){
                    $result = $facebook->api('/'.$user_id.'/feed/', 'post', $attachment);
                }
                
            } catch(FacebookApiException $e) {
                $msg .= "<li>" . $e->getMessage() . "</li>";
            }
        }

    } //END IF USER ID > 0
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
    //REDIRECT USER TO PHOTO
    header("Location: getPhoto.php?pid=" . $pid );
    exit;
}
?>
<br>
<p class="caption"><?php echo $lbl_privacy;?></p>
</div>

<script>
$(document).ready(function(){
    var x = $('#photoBlock').width();
    var y = $('#photoBlock').height() + 150;

    parent.$.colorbox.resize({height:y});

});
</script>
</body>
</html>
<?php require_once('close.php');?>