<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

//GET REQUEST VARIABLES
$comment_id = mysql_real_escape_string($_REQUEST['comment_id']);

$comment_result = "";
$args = array(
	'access_token' => $access_token,
);

if ($user_id != 0) {
    try{
            // CREATE LIKE
            $comment_result = $facebook->api('/'. $comment_id, 'DELETE', $args); 

    } catch(FacebookApiException $e) {
        $comment_result = $e->getMessage();
    }
  
} //END IF USER ID > 0

echo $comment_result;

require_once('close.php');
?>