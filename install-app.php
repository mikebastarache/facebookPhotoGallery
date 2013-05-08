<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');
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

<h1><?php echo $lbl_title_install;?></h1>
<p><?php echo $lbl_instructions_install;?></p>

<div style="float:right; margin:20px;">
    <button id="submitHandler" name="submitHandler" type="button" autofocus="autofocus"><?php echo $btn_install;?></button>
</div>

<br clear="all" />
<p class="caption"><?php echo $lbl_privacy;?></p>
    
<script>
    $(document).ready(function () {
        var protocol = "http://";
        if (location.protocol === 'https:') {
            protocol = "https://";
        }

        $("#submitHandler").click(function (event) {
            var loginUrl="http://www.facebook.com/dialog/oauth/?"+
            "scope=user_photos,user_likes,publish_stream&"+
            "client_id=<?php echo AppInfo::appID(); ?>&"+
            "redirect_uri="+"<?php echo AppInfo::redirectUrl(); ?>/loginHandler.php&"+
            "response_type=code&"+
            "state=<?php echo $_SESSION['state']; ?>&"+
            "display=popup";

            //Forward link to Facebook login
            window.top.location.href = loginUrl;
        });
    });
</script>
</body>
</html>
