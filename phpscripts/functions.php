<?php

require("config.php");

function login($subemail, $subpass, $subremember) {
  $err = array();
	if (empty($_COOKIE['test'])) {
		array_push($err, "Your browser must accept cookies to log in, please emable them in the preferences of your browser and try again.");
	}
  if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
    array_push($err, "Falta su e-mail. Escriba su e-mail.");
  } else {
    $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"."\.([a-z]{2,}){1}$";
    if(!eregi($regex,$subemail)){
      array_push($err, "El e-mail no es válido. Escriba su e-mail.");
	  }
  }
  if (!$subpass) {
    array_push($err, "Falta contraseña, Escriba su contraseña.");
  }
  if(count($err) != 0) {
    return $err;
  }
  
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT * FROM users WHERE email = '$subemail'";
  $result = mysql_query($q, $link) or die(mysql_error());
  if(!$result || (mysql_numrows($result) < 1)){
    array_push($err, "Este e-mail no está registrado. Escriba un e-mail registrado o registre este e-mail.");
    return $err;
  }
  $dbarray = mysql_fetch_array($result);
  if($subpass != $dbarray['password']){
    array_push($err, "Contraseña incorrecta.");
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
  if (getUserPrivilages($_SESSION['email']) <= 3) {
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

function getUserPrivilages($email) {
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT privilages FROM users WHERE email = '$email'";
  $result = mysql_query($q, $link);
  $dbarray = mysql_fetch_array($result);
  return $dbarray['privilages'];
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
    array_push($err, "Escriba su e-mail.");
  } else {
    $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"."\.([a-z]{2,}){1}$";
    if(!eregi($regex, $regemail)){
      array_push($err, "Escriba su e-mail válido.");
    }
  }
  if(!$regpass){
    array_push($err, "Escoja una contraseña.");
  } else {
    if($regpass != $regpass2) {
      array_push($err, "The password fields did not match. Please try again.");
	  return $err;
    }
    if(strlen($regpass) < 5){
      array_push($err, "Escoja una contraseña con más de 4 caracteres.");
    }
    if(!eregi("^([0-9a-z])+$", ($regpass = trim($regpass)))){
      array_push($err, "Escoja una contraseña que contenga sólo letras y números");
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
    array_push($err, "Este e-mail ya ha sido registrado Sírvase escribir otro e-mail.");
  }
  if(count($err) != 0) {
    return $err;
  }
  $date = date("Y-m-d");
  $last_login = date("Y-m-d H:i:s");
  $ip=$_SERVER['REMOTE_ADDR'];
  $newuserid = makeUserID();
  $exp_date = date("Y-m-d", mktime(0,0,0,date("m"),date("d")+90,date("Y")));
  $q = "INSERT INTO users VALUES ('', '$regemail', '$regpass', '$newuserid', 03, '$date', '$last_login', '$exp_date', '$ip')";
  if (!mysql_query($q, $link)) {
    array_push($err, "Ha ocurrido un error en la base de datos. Sírvase intentarlo más tarde.");
  }
  $_SESSION['email'] = $regemail;
  $_SESSION['userid'] = $newuserid;
  $_SESSION['loggedin'] = true;
  return $err;
}

function getPass($email) {
  $err = array();
  if(!$email || strlen($email = trim($email)) == 0){
    array_push($err, "Escriba su e-mail.");
  }
  $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"."\.([a-z]{2,}){1}$";
  if(!eregi($regex, $email)){
    array_push($err, "Escriba su e-mail válido.");
  }
  if(count($err) != 0) {
    return $err;
  }
  $link = mysql_connect(DBSERV, DBUSER, DBPASS);
  mysql_select_db(DBNAME, $link);
  $q = "SELECT password FROM users WHERE email = '$email'";
  $result = mysql_query($q, $link) or die(mysql_error());
  if (mysql_numrows($result) == 0) {
    array_push($err, "Este e-mail ya ha sido registrado Sírvase escribir otro e-mail.");
  }
  if(count($err) != 0) {
    return $err;
  }
  $dbarray = mysql_fetch_array($result);
  $password = $dbarray['password'];
  $message = "Estimado(a) alumno(a),\n\nLe enviamos este email porque Ud. nos informó que no recordaba su palabra de pase para pronunciebienelingles.com\n\n   Email: '$email'\n\n   Contrase&ntilde;a: '$password'\n\nImprima una copia para su archivo.\n\nNo responda a este email.";
  $message = wordwrap($message, 70);
  $header = "To: '$email' From: webmaster@pronunciebienelingles.com";
  mail($email, "Su palabra de pase para pronunciebienelinglés.com", $message, $header);
  return $err;
}

function mailTime ($email, $name, $message) {
  $err = array();
  if(!$email || strlen($email = trim($email)) == 0) {
    array_push($err, "Escriba su e-mail.");
  } else {
    $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"."\.([a-z]{2,}){1}$";
    if(!eregi($regex, $email)){
      array_push($err, "Escriba su e-mail válido.");
    }
  }
  if(!$name || strlen($name = trim($name)) == 0) {
    array_push($err, "Escriba su nombre.");
  }
  if(!$message || strlen($message = trim($message)) == 0) {
    array_push($err, "Escriba su mensaje.");
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
?>