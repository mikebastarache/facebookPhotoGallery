<?php
//LOAD ALL GLOBAL LOGIC AND VARIABLES
require_once('global.php');

//QUERY DB FOR ACCESS TOKEN
$sql = "SELECT access_token FROM users WHERE fbId=" . $user_id;
$rs = $db->query($sql);
$row = $db->fetchArray($rs);
$access_token = $row['access_token'];

$user_args = array(
	'access_token' => $access_token 
);
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

<h1><?php echo $lbl_Uploadtitle;?></h1>
<p><?php echo $lbl_UploadInstructions;?></p>
<form name="form" id="form" action="uploadFb.php?signed_request=<?php echo $signed_request;?>" method="post" enctype="multipart/form-data">
<table id="fbtable">
<tr>
    <td><input type="file" name="multiUpload" id="multiUpload" multiple accept="image/*" required /></td>
    <td align="right"><input type="submit" name="submitHandler" id="submitHandler" value="<?php echo $btn_upload;?>" class="buttonUpload" /></td>
</tr>
<tr>
    <td colspan="2" id="val_File" class="fberrorbox"><?php echo $error_upload;?></td>
</tr>
</table>
</form>
<div id="fbupload"><img src="images/uploading.gif" border="0" width="195" height="13"></div>
<p class="caption"><?php echo $lbl_privacy;?></p>
    
<script>
    $(document).ready(function () {
        $("#submitHandler").click(function (event) {
            if (!$('input[type="file"]').val()) {
                event.preventDefault();
                $("#val_File").css('display', 'table-cell');
                alert("<?php echo $error_upload;?>");
            } else {
                $("#fbtable").css('display', 'none');
                $("#fbupload").css('display', 'block');
            }
        });
    });
</script>
</body>
</html>
<?php require_once('close.php');?>