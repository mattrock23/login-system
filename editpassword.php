<?php

require("phpscripts/functions.php");
require("phpscripts/config.php");

if(isset($_POST['subemail']) || isset($_POST['subpass']) || isset($_POST['remember'])) {
  $setpasserrors = setPassword($_POST['email'], $_POST['pass1'], $_POST['pass2']);
  $alert = '<p class="errormessage">';
  if(count($setpasserrors) == 0) {
    $alert .= "Password updated successfully. Please return home and login.")
  } else {
    for($i=0; $i<count($setpasserrors); $i++) {
      $alert .= "Error: " . $setpasserrors[$i] . "<br />";
    }
  }
  $alert .= "</p>";
}

$token = $_GET['token'];
if($token) {
	//check db for the token hashed
	$link = mysql_connect(DBSERV, DBUSER, DBPASS);
    mysql_select_db(DBNAME, $link);
    $q = "SELECT * FROM passwordreset WHERE token = '$token'";
    $result = mysql_query($q, $link) or die(mysql_error());
    if (mysql_numrows($result) == 0) {
      $alert = "We did not find a request to reset your password in the database, please try again.<br />");
    }
    $dbarray = mysql_fetch_array($result);
	if ($dbarray['timestamp'] < time()) {
	  $alert .= "Your password reset request has expired, please try again.<br />";
	}
	//if not in db, error this email did not request reset contact webmaster if you actually did try to reset
	//if hashed token is in the db, echo the associated email address and a hidden field with the email address

} else { // no token means user just wants to change password
	//echo a field for the email
	//echo a current password field
  $alert = "There seems to be an error. You must have come to this page the wrong way. Please try again.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login/Register</title>
<link rel="stylesheet" type="text/css" href="extra.css" title="Default" />
</head>
<body>

<div id="maincontent" class="clearfix">
<p>Please login to view the content.</p>
<div id="registerbox">
  <h4>Please select a new password now</h4>
  <form action="editpass.php" method="post">
  <span class="label">Email</span><span class="element"><?php echo $dbarray['email'] ?></span><br /><br />
  <span class="label">Password</span><span class="element"><input type="password" name="pass1" maxlength="30" /></span><br /><br />
  <span class="label">Password Again</span><span class="element"><input type="password" name="pass2" /></span><br /><br />
  <span class="label">&nbsp;</span><span class="element"><input type="submit" value="Register" /></span>
  <?php echo '<input type="hidden" name="email" value="' . $dbarray['email'] . '" /></form>';
  if($alert) { echo "<br />" . $alert; } ?>
  </div>

<div id="footer">
 <p>Designed by Matt Whitlock 2007</p>
</div>

</body>
</html>
