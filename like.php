<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

//GET REQUEST VARIABLES
$user_id = mysql_real_escape_string($_REQUEST['user_id']);
$action = mysql_real_escape_string($_REQUEST['action']);
$object_id = mysql_real_escape_string($_REQUEST['object_id']);

$like_result = "";
$args = array(
	'access_token' => $access_token 
);

if ($user_id != 0) {

    //UPLOAD PHOTO TO FACEBOOK
    try{
        if($action == "CREATE"){
            // CREATE LIKE
            $like_result = $facebook->api('/'. $object_id . '/likes', 'POST', $args); 
            $like_result = "CREATED";

        } else if($action == "DELETE"){
            // DELETE LIKE
            $like_result = $facebook->api('/'. $object_id . '/likes', 'DELETE', $args);
            $like_result = "DELETED";
        }

                
    } catch(FacebookApiException $e) {
        $like_result = $e->getMessage();
    }

    
} //END IF USER ID > 0

echo $like_result;

require_once('close.php');
?>