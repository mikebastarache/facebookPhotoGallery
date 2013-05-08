<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

//GET REQUEST VARIABLES
$pid = mysql_real_escape_string($_REQUEST['pid']);
$status = mysql_real_escape_string($_REQUEST['status']);

$comment_result = "";
$userFullName = $user_id;
$user_args = array(
	'access_token' => $access_token 
);
$args = array(
	'access_token' => $access_token,
    'from' => $user_id,
    'message' => $status 
);


if ($user_id != 0) {
    
    //GET USER NAME
    try{
        $userData = $facebook->api("/" . $user_id,'get',$user_args);
        $userFullName = $userData['name'];

    } catch(FacebookApiException $e) {
        $comment_result = $e->getMessage();
    }
    
    //CHECK USERS PERMISSION TO POST
    try{
        //Get user permissions to ensure they can post
        $permissions = $facebook->api("/me/permissions",'get',$user_args);
    
        if( array_key_exists('publish_stream', $permissions['data'][0]) ) {
            try{
                    // CREATE LIKE
                    $comment_result = $facebook->api('/'. $pid . '/comments', 'POST', $args); 

                    $commentId = $comment_result;
                    $commentorData = $user_id;
                    $thumbnail = "http://graph.facebook.com/". $user_id ."/picture";
                    $userLink = "http://facebook.com/". $user_id;

                    $comment_result = '<div class="fbbluebox">';
                    $comment_result .= '<table id="fbCommenttable">';
                    $comment_result .= '<tr>';
                    $comment_result .= '<td class="fbCommentProfileBlock"><a href="'. $userLink .'" target="_blank"><img src="'.$thumbnail.'" width="40" height="40" border="0"></a></td>';
	                $comment_result .= '<td>';
                    $comment_result .= '<span class="commentBlock">';
                    $comment_result .= '<a class="remove" comment_id="' . $commentId['id'] . '" title="delete"></a>';
                    $comment_result .= '<a href="'. $userLink .'" target="_blank">'. $userFullName .'</a> ' . $status . '</span><br />';
	                $comment_result .= '<span class="commentDate">' . $timeStampString;
                
                    if($user_id > 0) {
                        $comment_result .= '<span class="fbSpacer">&#8226;</span>';
                        $comment_result .= '<span class="fbuserLikeBlock">';
                        $comment_result .= '<a title="'. $btnLikeComment .'" user_id="'. $user_id .'" object_id="'. $commentId['id'] .'" like="CREATE" class="like-action">'. $btnLike .'</a>'; 
                        $comment_result .= '</span>';
                    }
                    $comment_result .= '</span></td>';
                    $comment_result .= '</tr>';
                    $comment_result .= '</table>';
                    $comment_result .= '</div>';

                
            } catch(FacebookApiException $e) {
                $comment_result = $e->getMessage();
            }
        } else {
            $comment_result = $errorPermission;
        }

    } catch(FacebookApiException $e) {
        $comment_result = $e->getMessage();
    }

} //END IF USER ID > 0

echo $comment_result;

require_once('close.php');
?>