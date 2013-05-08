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

$user_args = array(
	'access_token' => $fanpage_access_token
);

//GET REQUEST VARIABLES
$LastRecord = mysql_real_escape_string($_REQUEST['LastRecord']);
$sort = mysql_real_escape_string($_REQUEST['sort']);

if($sort == 0){
   //Get all photos
   $fql = 'SELECT object_id, owner, modified, src,src_big_width,src_big_height,caption,created,comment_info,like_info,pid,src_small,src_big, aid FROM photo WHERE aid IN (SELECT aid FROM album WHERE object_id IN (' . AppInfo::albumList() . '))  ORDER BY object_id DESC LIMIT ' . $LastRecord . ',10';
   
} else if ($sort == 1) {
   //Get top photos by likes
   //query album, sort by ['like_info']["like_count"]

   $fql = 'SELECT object_id, owner, modified, src,src_big_width,src_big_height,caption,created,comment_info,like_info,pid,src_small,src_big, aid FROM photo WHERE aid IN (SELECT aid FROM album WHERE object_id IN (' . AppInfo::albumList() . ')) AND like_info > 0  ORDER BY like_info DESC LIMIT ' . $LastRecord . ',10';

} else if ($sort == 2) {
    //Get royale photos. query db.
    //query id based on pid in MySql DB where royale is checked

    try{
        //QUERY DB FOR ROYALE PHOTOS
        $sql = "SELECT group_concat(photoId) as photoIds FROM photos WHERE fbId=" . AppInfo::fanpage();
        $rs = $db->query($sql);
        $row = $db->fetchArray($rs);
        $photoList = $row['photoIds'];

    } catch (Exception $e) {
        $msg .= "<li>MySQL SELECT ROYALE PHOTO IDS - " . $e->getMessage() . "</li>";
    }

    $fql = 'SELECT object_id, owner, modified, src,src_big_width,src_big_height,caption,created,comment_info,like_info,pid,src_small,src_big, aid FROM photo WHERE object_id IN (' . $photoList . ')  ORDER BY object_id DESC LIMIT ' . $LastRecord . ',10';

} else if ($sort == 3) {
    //Get my photos
    //query id based on pid in MySql DB where user_id is me

    try {
        //QUERY DB FOR MY PHOTOS
        $sql = "SELECT group_concat(photoId) as photoIds FROM photos WHERE fbId=" . $user_id;
        $rs = $db->query($sql);
        $row = $db->fetchArray($rs);
        $photoList = $row['photoIds'];

    } catch (Exception $e) {
        $msg .= "<li>MySQL SELECT MY PHOTO IDS - " . $e->getMessage() . "</li>";
    }

    $fql = 'SELECT object_id, owner, modified, src,src_big_width,src_big_height,caption,created,comment_info,like_info,pid,src_small,src_big, aid FROM photo WHERE object_id IN (' . $photoList . ')  ORDER BY object_id DESC LIMIT ' . $LastRecord . ',10';

} else if ($sort == 4) {
    //Get my friends photos
    //query id based on pid in MySql DB where friend_id 

    $friendList = "";

    //---------------------------------------------//
    //Get my friends
    try {
	    $friends = $facebook->api('/'. $user_id . '/friends' , 'get', $user_args);
        $fData = $friends['data'];
        $out = array();

        foreach ($fData as $value) { 
            array_push($out, $value['id']);
        }
        $friendList = implode(', ', $out);
    
    } catch (FacebookApiException $e) {
        $msg .= "<li>Get Friends - " . $e->getMessage() . "</li>";
    }
    //---------------------------------------------//

    if($friendList == "") 
        $friendList = 0;

    try {
        //QUERY DB FOR FRIENDS PHOTOS
        $sql = "SELECT group_concat(photoId) as photoIds FROM photos WHERE fbId IN (" . $friendList . ")";
        $rs = $db->query($sql);
        $row = $db->fetchArray($rs);
        $photoList = $row['photoIds'];

    } catch (Exception $e) {
        $msg .= "<li>MySQL SELECT FRIENDS PHOTO IDS - " . $e->getMessage() . "</li>";
    }

    $fql = 'SELECT object_id,  src,pid, aid FROM photo WHERE object_id IN (' . $photoList . ')  ORDER BY object_id DESC LIMIT ' . $LastRecord . ',10';
}

/*
OLD WAY OF DOING FQL
$app_photos = $facebook->api(array(
    'method' => 'fql.query',
    'query' => $fql
));
*/

$fql_url = "https://graph.facebook.com/fql?q=" . urlencode($fql) . "&access_token=" . $fanpage_access_token ;
$response = getSslPage($fql_url);

//CONVERT JSON DATA INTO PHP ARRAY
$response2 = preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', $response);
$app_photos = json_decode($response2, TRUE);
$app_photosData = "";
if(isset($app_photos['data']))
    $app_photosData = $app_photos['data'];

$currentRow = $LastRecord;
$data = "";

if($app_photosData){
    foreach($app_photosData as $photo)
    {
	    $currentRow = $currentRow + 1;
	    echo '<li class="gallery-item" id="' . $currentRow . '">';
                          
		$newHeight = number_format($photo['src_big_height'] / ($photo['src_big_width'] / 215),0);
                
		echo '<a class="iframe" pid="' . $photo['object_id'] . '" data-toggle="modal"><img src="' . $photo['src'] . '" width="215" height="' . $newHeight . '"></a>';
		echo '<div class="fbbluebox">';
            echo  '<span class="fbUserLikeCount">';
		    if($photo['like_info']["like_count"] > 0){
                if($photo['like_info']["like_count"] == 1){ $lblTmpPeople = $lblPersonLikes; } else { $lblTmpPeople = $lblPeopleLike; }
                echo '<div class="gridText"><img src="images/facebook_like_thumb.png" class="fbLikeIcon" /></div>';
                echo '<div class="gridTextCount"><span class="counter" data-count="' . $photo['like_info']["like_count"] . '">' . $photo['like_info']["like_count"] . '</span> ' . $lblTmpPeople . '</div>';
            } else { echo '<span class="counter" data-count="0"></span>'; }
            echo  '</span>';

            echo  '<span class="fbUserLikePhotoBlock">';
            if ($user_id == 0) {
                //If user is NOT logged in
                echo '<a href="javascript:facebookLogin();" class="like-action">'. $btnLike .'</a>';

            } else {
                //If user is logged in
                if($photo['like_info']["user_likes"] == "true"){
                    echo '<a title="'. $btnUnlikeComment .'" user_id="'. $user_id .'" object_id="'. $photo['object_id'] .'" like="DELETE" class="like-action">'. $btnUnlike .'</a>';
                } else {
                    echo '<a title="'. $btnLikeComment .'" user_id="'. $user_id .'" object_id="'. $photo['object_id'] .'" like="CREATE" class="like-action">'. $btnLike .'</a>';
                }
            }
            echo  '</span>';

        echo '</div>';
        echo '</li>';
    }
}
echo $data;

require_once('close.php');
?>