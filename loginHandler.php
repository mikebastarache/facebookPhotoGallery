<?php
// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');

error_reporting(E_ALL);
ini_set("display_errors", 1); 
date_default_timezone_set('America/Halifax');

if(!isset($_SESSION)) {
     session_start();
}

require_once('MySql.php');
$db = new Mysql();
$db->connect();


// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');

require_once('sdk/src/facebook.php');

$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret()
));

$msg = "";
$user_id = 0;
$access_token = "";
$fanpage_token = "";
$access_token_date = date('Y-m-d H:i:s');

if (isset($_REQUEST["code"]))
{
	if($_SESSION['state'] && ($_SESSION['state'] === $_REQUEST['state'])) {
        $redirect_url = AppInfo::redirectUrl() . "/loginHandler.php";
		$token_url = "https://graph.facebook.com/oauth/access_token?". "client_id=" . AppInfo::appID() . "&client_secret=" . AppInfo::appSecret() . "&code=" . $_REQUEST["code"]. "&redirect_uri=" . urlencode($redirect_url);
		
        try {
            $response = getSslPage($token_url);
		    $params = null;
            parse_str($response, $params);
            if (array_key_exists('access_token', $params)) {
		        $access_token = $params['access_token'];
            }

        } catch(FacebookApiException $e) {
            $msg .= "<li>Get Facebook Login - " . $e->getType() . " - " . $e->getMessage() . "</li>";
        }

	} else {
		$msg .= "<li>The state does not match. You may be a victim of CSRF.<li>";
	}

    if($access_token != ""){
        $params = array(
		    'access_token' => $access_token
	    );
        try {
    	    $accounts = $facebook->api('/me', 'GET', $params);
    
            foreach($accounts as $user)
            {
                if(sizeof($user) == 1){ 
                    $user_id = $user;
                    break;
                }
            }

        } catch(FacebookApiException $e) {
            $msg .= "<li>" . $e->getType() . " - " . $e->getMessage() . "</li>";
        }
    }

    if($user_id > 0 && $access_token != ""){

        //QUERY DB TO GET ACCESS TOKEN INFORMATION
        $sql = "SELECT * FROM users WHERE fbId=$user_id";
        $rs = $db->query($sql);
        $total = $db->getRowsNum($rs);

        //UPDATE DB with new access token

        //CHECK TO SEE IF WE NEED TO INSERT OR UPDATE
        if($total==0){
            $sql = "INSERT INTO users (access_token, datecreated, fbId) VALUES ('" . $access_token. "', '" . $access_token_date . "', '" . $user_id . "')";
        } else {
            $sql = "UPDATE users SET access_token = '" . $access_token. "', datecreated = '" . $access_token_date. "' WHERE fbId = '" . $user_id . "'";
        }
        
        //Execute sql
        $rs = $db->query($sql);
        
    }
}

if($msg != ""){
    echo '<link rel="stylesheet" href="stylesheets/file.css" media="all" type="text/css" />'; 
    echo '<ul class="fberrorbox">' . $msg . '</ul>';

} else if ($user_id == 0){ 
    //LOAD RESOURCES
    $locale = "en-us";
    require_once('resources.php');

    echo '<link rel="stylesheet" href="stylesheets/file.css" media="all" type="text/css" />'; 
    echo '<ul class="fberrorbox"><li>' . $lbl_session_description . '</li></ul>';

} else { 
    echo '<script type="text/javascript">window.close();</script>';
}

//close MySql
$db->close();
?>