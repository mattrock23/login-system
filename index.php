<?php

require("phpscripts/functions.php");
require("phpscripts/config.php");

session_start();

if(isset($_POST['subemail']) || isset($_POST['subpass']) || isset($_POST['remember'])) {
  $loginerrors = login($_POST['subemail'], $_POST['subpass'], isset($_POST['remember']));  
  if(count($loginerrors) != 0) {
    $alert = '<p class="errormessage">';
    for($i=0; $i<count($loginerrors); $i++) {
      $alert .= "Error: " . $loginerrors[$i] . "<br />";
	  }
  }
} else if(isset($_POST['regemail']) || isset($_POST['regpass1']) || isset($_POST['regpass2'])) {
  $registererrors = register($_POST['regemail'], $_POST['regpass1'], $_POST['regpass2']);
  if(count($registererrors) != 0) {
    $regerrmsg = '<p class="errormessage">';
    for($i=0; $i<count($registererrors); $i++) {
	    $regerrmsg .= "Error: " . $registererrors[$i] . "<br />";
	  }
	$regerrmsg .= "</p>";
  }
} else {
  checkLogin();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login/Register</title>
<link rel="stylesheet" type="text/css" href="extra.css" title="Default" />
<script type="text/javascript">
function setCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
setCookie('test', 'test', 1);
</script>
<style type="text/css">
html body #logo {
margin-bottom: 0;
}
</style>
</head>
<body>

<div id="maincontent" class="clearfix">
<?php
if($_SESSION['loggedin']) { ?>
<p>Logged in as <?php echo $_SESSION['email'] ?> <a href='../logout.php'>Logout</a></p>
Free Content<br />
<?
$privileges = getUserPrivileges($_SESSION['email']);
if ($privileges) {//if full paid subscriber
?>
Paid content<br />
<?
} else { //if not a full subscriber
?>
Pay and get more content<br />
<?php
}} else {//If not logged in
?>
<p>Please login to view the content.</p>
<div id="registerbox">
  <h4>Resgister Free Here!</h4>
  <form action="index.php" method="post">
  <span class="label">Email</span><span class="element"><input type="text" name="regemail" maxlength="30" /></span><br /><br />
  <span class="label">Password</span><span class="element"><input type="password" name="regpass1" maxlength="30" /></span><br /><br />
  <span class="label">Password Again</span><span class="element"><input type="password" name="regpass2" /></span><br /><br />
  <span class="label">&nbsp;</span><span class="element"><input type="submit" value="Register" /></span></form>
  <?php if ($regerrmsg) { echo "<br />" . $regerrmsg; } ?>
</div>
<div id="loginbox">
  <h4>Login</h4>
  <form action="index.php" method="post">
  <span class="label">Email</span><span class="element"><input type="text" name="subemail" maxlength="30" /></span><br /><br />
  <span class="label">Password</span><span class="element"><input type="password" name="subpass" maxlength="30" /></span><br /><br />
  <span class="label">Remember me</span><span class="element"><input type="checkbox" name="remember" /></span><br /><br />
  <span class="label">&nbsp;</span><span class="element"><input type="submit" value="Login" /></span></form><br /><br />
  <a href="../forgotpass.php">Forgot Your Password?</a><?php if($alert) { echo "<br />" . $alert; } ?>
</div>
<?php } ?>
</div>

<div id="footer">
 <p>Designed by Matt Whitlock 2007</p>
</div>

</body>
</html>
