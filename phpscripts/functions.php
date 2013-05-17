<?php

require("config.php");

function login($subemail, $subpass, $subremember) {
  $err = array();
	if (empty($_COOKIE['test'])) {
		array_push($err, "Your browser must accept cookies to log in, please emable them in the preferences of your browser and try again.");
	}
  if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
    array_push($err, "Please enter your email.");
  } else {
    $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"."\.([a-z]{2,}){1}$";
    if(!eregi($regex,$subemail)){
      array_push($err, "The email you entered is not valid. Please check that your email is correct");
	  }
  }
  if (!$subpass) {
    array_push($err, "Please enter your password.");
  }
  if(count($err) != 0) {
    return $err;
  }
  $subpass = sha1($subpass);
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT * FROM users WHERE email = '$subemail'";
  $result = mysql_query($q, $link) or die(mysql_error());
  if(!$result || (mysql_numrows($result) < 1)){
    array_push($err, "This email is not registered. Please check that you entered your email correctly or register.");
    return $err;
  }
  $dbarray = mysql_fetch_array($result);
  if($subpass != $dbarray['password']){
    array_push($err, "Incorrect password.");
	  return $err;
  }
  $date = date("Y-m-d");
  if ($date > $dbarray['exp_date']) {
    array_push($err, "Your account has expired.");
    return $err;
  }
  $newuserid = makeUserID();
  $ip=$_SERVER['REMOTE_ADDR'];
  mysql_select_db(DBNAME, $link);
  $q = "UPDATE users SET userid = '$newuserid' WHERE email = '$subemail'";
  mysql_query($q, $link) or die(mysql_error());
  $q = "UPDATE users SET ip = '$ip' WHERE email = '$subemail'";
  mysql_query($q, $link) or die(mysql_error());
  if ($subremember) {
    setcookie("email", $subemail, time()+60*60*24*100);
	  setcookie("userid", $newuserid, time()+60*60*24*100);
  }
  ini_set('session.cookie_lifetime', '0');
  $_SESSION['email'] = $subemail;
  $_SESSION['userid'] = $newuserid;
  $_SESSION['loggedin'] = true;
  return $err;
}

function makeUserID() {
  $randstr = "";
  for($i=0; $i<16; $i++){
    $randnum = mt_rand(0,61);
    if($randnum < 10){
      $randstr .= chr($randnum+48);
    } else if($randnum < 36) {
      $randstr .= chr($randnum+55);
    } else {
      $randstr .= chr($randnum+61);
    }
  }
  return md5($randstr);
}

function checkLogin() {
  if(isset($_COOKIE['email']) && isset($_COOKIE['userid'])) {
    $_SESSION['email'] = $_COOKIE['email'];
    $_SESSION['userid'] = $_COOKIE['userid'];
  }

  if(isset($_SESSION['email']) && isset($_SESSION['userid'])) {
    $ip=$_SERVER['REMOTE_ADDR'];
    if(confirmUserID($_SESSION['email'], $_SESSION['userid']) || checkExpDate($_SESSION['email']) || checkIP($_SESSION['email'], $ip)) {
      $_SESSION['loggedin'] = true;
    } else {
      unset($_SESSION['email']);
      unset($_SESSION['userid']);
	    unset($_SESSION['loggedin']);
	  }
  }
}

function checkExpDate($subemail) {
  if (getUserPrivileges($_SESSION['email']) <= 3) {
    return true;
  }
  $date = date("Y-m-d");
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT exp_date FROM users WHERE email = '$subemail'";
  $result = mysql_query($q, $link) or die(mysql_error());
  if(!$result || mysql_numrows($result) < 1) {
    return false;
  }
  $dbarray = mysql_fetch_array($result);
  if ($date < $dbarray['exp_date']) {
    return true;
  } else {
    return false;
  }
}

function checkIP($subemail, $ip) {
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT ip FROM users WHERE email = '$subemail'";
  $result = mysql_query($q, $link) or die(mysql_error());
  if(!$result || mysql_numrows($result) < 1) {
    return false;
  }
  $dbarray = mysql_fetch_array($result);
  if ($ip == $dbarray['ip']) {
    return true;
  } else {
    return false;
  }
}

function getUserPrivileges($email) {
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT privileges FROM users WHERE email = '$email'";
  $result = mysql_query($q, $link);
  $dbarray = mysql_fetch_array($result);
  return $dbarray['privileges'];
}

function confirmUserID($subemail, $subuserid) {
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT userid FROM users WHERE email = '$subemail'";
  $result = mysql_query($q, $link) or die(mysql_error());
  if(!$result || mysql_numrows($result) < 1) {
    return false;
  }
  $dbarray = mysql_fetch_array($result);
  if($subuserid == $dbarray['userid']) {
    return true;
  } else {
    return false;
  }
}

function logout() {
  if(isset($_COOKIE['email']) && isset($_COOKIE['userid'])){
    setcookie("email", "", time()-3600);
    setcookie("userid", "", time()-3600);
  }
  unset($_SESSION['loggedin']);
  unset($_SESSION['username']);
  unset($_SESSION['userid']);
  session_destroy();
}

function register($regemail, $regpass, $regpass2) {
  $err = array();
  if(!$regemail || strlen($regemail = trim($regemail)) == 0){
    array_push($err, "Please enter your email.");
  } else {
    $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"."\.([a-z]{2,}){1}$";
    if(!eregi($regex, $regemail)){
      array_push($err, "Please enter a valid email address.");
    }
  }
  if(!$regpass){
    array_push($err, "Please enter a password.");
  } else {
    if($regpass != $regpass2) {
      array_push($err, "The password fields did not match. Please try again.");
	  return $err;
    }
    if(strlen($regpass) < 5){
      array_push($err, "Your password must be at least 5 characters long.");
    }
    if(!eregi("^([0-9a-z])+$", ($regpass = trim($regpass)))){
      array_push($err, "Your password can only be letters and numbers");
    }
  }
  if(count($err) != 0) {
    return $err;
  }
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT email FROM users WHERE email = '$regemail'";
  $result = mysql_query($q, $link);
  if (mysql_numrows($result) > 0) {
    array_push($err, "The email address you entered is already registered.");
  }
  if(count($err) != 0) {
    return $err;
  }
  $regpass = sha1($regpass);
  $date = date("Y-m-d");
  $last_login = date("Y-m-d H:i:s");
  $ip=$_SERVER['REMOTE_ADDR'];
  $newuserid = makeUserID();
  $exp_date = date("Y-m-d", mktime(0,0,0,date("m"),date("d")+90,date("Y")));
  $q = "INSERT INTO users VALUES ('', '$regemail', '$regpass', '$newuserid', 03, '$date', '$last_login', '$exp_date', '$ip')";
  if (!mysql_query($q, $link)) {
    array_push($err, "There was an error in the database, please try again later.");
  }
  $_SESSION['email'] = $regemail;
  $_SESSION['userid'] = $newuserid;
  $_SESSION['loggedin'] = true;
  return $err;
}

function resetPass($email) {
  $err = array();
  if(!$email || strlen($email = trim($email)) == 0){
    array_push($err, "Please enter an email address.");
  }
  $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"."\.([a-z]{2,}){1}$";
  if(!eregi($regex, $email)){
    array_push($err, "Please enter a valid email address.");
  }
  if(count($err) != 0) {
    return $err;
  }
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT email FROM users WHERE email = '$email'";
  $result = mysql_query($q, $link) or die(mysql_error());
  if (mysql_numrows($result) == 0) {
    array_push($err, "This email address is not registered.");
  }
  if(count($err) != 0) {
    return $err;
  }
  $token = md5(uniqid(rand(), true));
  $timestamp = time() + (20 * 60);
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "INSERT INTO passwordreset VALUES ('$email', '$token', '$timestamp')";
  mysql_query($q, $link);
  $message = 'You are receiving this email because you requested to reset your password. Click here to reset your password. <a href="editpassword.php?token=' . $token . '">editpassword.php?token=' . $token . '</a>';
  $message = wordwrap($message, 70);
  $header = "To: '$email' From: webmaster@pronunciebienelingles.com";
  mail($email, "Reset Password", $message, $header);
  return $err;
}

function mailTime ($email, $name, $message) {
  $err = array();
  if(!$email || strlen($email = trim($email)) == 0) {
    array_push($err, "Please enter your email address.");
  } else {
    $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"."\.([a-z]{2,}){1}$";
    if(!eregi($regex, $email)){
      array_push($err, "Please enter a valid email address.");
    }
  }
  if(!$name || strlen($name = trim($name)) == 0) {
    array_push($err, "Please enter your name.");
  }
  if(!$message || strlen($message = trim($message)) == 0) {
    array_push($err, "Please enter your message.");
  }
  if ( ereg( "[\r\n]", $name ) || ereg( "[\r\n]", $email ) ) {
	array_push($err, "Please only 1 email and/or name.");
  }
  if(count($err) != 0) {
    return $err;
  }
  $message = $message . "\nFrom: " . $name;
  $message = wordwrap($message, 70);
  mail( "webmaster@pronunciebienelingles.com", "Feedback Form Results", $message, "From: $email" );
  header( "Location: thankyou.html" );
}

function setPassword ($email, $pass, $pass2) {
  $err = array();
  if(!$pass){
    array_push($err, "Please enter a password.");
  } else {
    if($pass != $pass2) {
      array_push($err, "The password fields did not match. Please try again.");
    return $err;
    }
    if(strlen($pass) < 5){
      array_push($err, "Your password must be at least 5 characters long.");
    }
    if(!eregi("^([0-9a-z])+$", ($pass = trim($regpass)))){
      array_push($err, "Your password can only be letters and numbers");
    }
  }
  if(count($err) != 0) {
    return $err;
  }
  $pass = sha1($pass);
  $date = date("Y-m-d");
  $last_login = date("Y-m-d H:i:s");
  $ip=$_SERVER['REMOTE_ADDR'];
  $newuserid = makeUserID();
  $exp_date = date("Y-m-d", mktime(0,0,0,date("m"),date("d")+90,date("Y")));
  $q = "INSERT INTO users VALUES ('', '$regemail', '$regpass', '$newuserid', 03, '$date', '$last_login', '$exp_date', '$ip')";
  if (!mysql_query($q, $link)) {
    array_push($err, "There was an error in the database, please try again later.");
  }
  $_SESSION['email'] = $regemail;
  $_SESSION['userid'] = $newuserid;
  $_SESSION['loggedin'] = true;
  return $err;
}
?>