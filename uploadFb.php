<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

$fanpageUrl = AppInfo::fanpageUrl();
$user_full_name = AppInfo::fanpageName();
$msg = "";
$photoSource = "";
$photoHeight = 0;
$photoWidth = 0;
$photoDescription = "";
$pid = 0;
$share_link = 'https://www.facebook.com/photo.php?fbid=';
$share_icon = AppInfo::getUrl() . 'images/ShareIcon_90.jpg';
$user_args = array(
	'access_token' => $access_token 
);

if ($user_id) {
  
    $facebook->setFileUploadSupport(true);

    //QUERY DB TO GET ACCESS TOKEN INFORMATION
    //$sql = "SELECT * FROM users WHERE fbid = " . $user_id;
    $sql = "SELECT * FROM app";
    $rs = $db->query($sql);
    $row = $db->fetchArray($rs);
    $total = $db->getRowsNum($rs);
    $fanpage_access_token = $row['fanpage_access_token'];

    if ($user_id != 0) {
             
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            if(isset($_FILES['multiUpload'])) {

                $file = $_FILES['multiUpload']['name'];
                $file_w_path = "uploads/" . $user_id . "-" . $file;
                $file_ext_name = $user_id . "-" . $file;
                $errors     = array();
                $maxsize    = 15097152;
                $acceptable = array(
                    'image/jpeg',
                    'image/jpg',
                    'image/gif',
                    'image/png'
                );
		        
                $tmpimage = getimagesize($_FILES['multiUpload']['tmp_name']);
                $mime = $tmpimage['mime'];
                
                if(($_FILES['multiUpload']['size'] >= $maxsize) || ($_FILES['multiUpload']["size"] == 0)) {
                    $msg .= '<li>' . $msg_FileTooLarge . '</li>';
                }
                
                if(!in_array($mime, $acceptable)) {
                    $msg .= '<li>' . $msg_FileTypeInvalid . '</li>';
                }
                
                if($msg == "") {

	                if(move_uploaded_file($_FILES['multiUpload']['tmp_name'], "uploads/" . $user_id . "-" . $_FILES['multiUpload']['name'])){
            
                        //GET users full name
                        try{
                            $userResult = $facebook->api('/me', 'get', $user_args);
                            $user_full_name = $userResult['name'];

                        } catch(FacebookApiException $e) {
                            $msg .= "<li>User - " . $e->getMessage() . "</li>";
                        }


	                    $args = array(
	 		                'owner' => $user_id,
			                'message' => $share_description2 . ' ' . $fanpageUrl,
			                'image' => '@'.realpath($file_w_path),
			                'aid' => AppInfo::albumID(),
			                'no_story' => AppInfo::postFanpage(),
			                'access_token' => $fanpage_access_token 
	                    );

                
                        //UPLOAD PHOTO TO FACEBOOK
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

                            //INSERT DB
                            $sql = "INSERT INTO photos (fbId,photo,photoId,albumId,dateCreated) values ($uploader_id,'$file_ext_name',$pid," . AppInfo::albumID() . ",'$photoDate')";
                            $rs = $db->query($sql);

                        } catch(FacebookApiException $e) {
                            $msg .= "<li>UPLOAD - " . $e->getMessage() . "</li>";
                        }

                        //IF PHOTO UPLOADED
                        if($pid > 0){
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
                                $msg .= "<li>Photo - " . $e->getMessage() . "</li>";
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
                                $msg .= "<li>Post - " . $e->getMessage() . "</li>";
                            }

                        } //END PID
                    }
            
                } //END OF UPLOAD ERROR CHECK
                
            }
        } //END POST
    
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
 if($msg != "" || $pid == 0){ 
     //ERROR
    echo '<h1>'. $lbl_error_title . '</h1>';
    echo '<ul class="fberrorbox">' . $msg . '</ul>';

} else if ($user_id == 0){ 
    //USER IS NOT LOGGED IN FACEBOOK
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