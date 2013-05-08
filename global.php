<?php
// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');

error_reporting(E_ALL);
ini_set("display_errors", 1); 
date_default_timezone_set('America/Halifax');

// Enforce https on production
/*
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}
*/

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');

//LOAD MYSQL
require_once('MySql.php');
$db = new Mysql();
$db->connect();

//LOAD FACEBOOK
require_once('sdk/src/facebook.php');

if(!isset($_SESSION)) {
     session_start();
}

$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
  'sharedSession' => true,
  'trustForwarded' => true,
));

//GET SIGNED REQUEST
if(isset($_REQUEST["signed_request"]) && $_REQUEST["signed_request"] != ""){
    $_SESSION['signed_request'] = $_REQUEST["signed_request"];
    $signed_request = $_REQUEST["signed_request"];
} else {
    if(isset($_SESSION['signed_request'])){
        $signed_request = $_SESSION['signed_request'];
    } else {
        $signed_request = "";
    }
}


//GET USER ID
$parsed_signed_request = get_signed_request($signed_request);
if(isset($parsed_signed_request['user_id']) && $parsed_signed_request['user_id'] > 0){
    $user_id = $parsed_signed_request['user_id'];
} else {
    $user_id = 0;
}

//SET USERS LANGUAGE
if(isset($parsed_signed_request['user']['locale'])){
    $locale = $parsed_signed_request['user']['locale'];
} else {
    $locale = "en-us";
}

//GET SHORT 2 HOUR ACCESS TOKEN
$access_token = "";
if(isset($parsed_signed_request['oauth_token'])){
    //$access_token = $parsed_signed_request['oauth_token'];
}

//UPDATE SHORT ACCESS TOKEN WITH STORED ACCESS TOKEN THAT DOES NOT EXPIRE FOR 2 MONTHS
//QUERY DB FOR USERS LONG ACCESS TOKEN
$sql = "SELECT access_token FROM users WHERE fbId=" . $user_id;
$rs = $db->query($sql);
$row = $db->fetchArray($rs);
if($row['access_token']!=""){ $access_token = $row['access_token']; }

//Set variables
$msg = "";
if (isset($_SERVER['HTTPS'])){$http = 'https://';} else {$http = 'http://';}
$SharePhoto = AppInfo::redirectUrl() . "/images/ShareIcon_90.jpg";

//LOAD RESOURCES
require_once('resources.php');
?>