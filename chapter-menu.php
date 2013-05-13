<?php

require("../phpscripts/functions.php");
require("../phpscripts/config.php");

session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PronuncieBienElIngles.com</title>
<style type="text/css">
html body  {
  background: #001343;
}

#tabnav {
  margin: 0;
  top: 0;
  right: 0;
  position: absolute;
}
</style>
</head>
<body>
<div id="top_box">
  <form name="nav">
  <select id="tabnav" name="SelectURL" onChange="top.location.href=document.nav.SelectURL.options[document.nav.SelectURL.selectedIndex].value">
  <option value="" selected>Skip to a Chapter:</option>
<?php
$options = array("<option value='../Chapter1/Chapter1.php'>Capitulo 1</option>",
"<option value='../Chapter2/Chapter2.php'>Capitulo 2</option>",
"<option value='../Chapter3/Chapter3.php'>Capitulo 3</option>",
"<option value='../Chapter4/Chapter4.php'>Capitulo 4</option>",
"<option value='../Chapter5/Chapter5.php'>Capitulo 5</option>",
"<option value='../Chapter6/Chapter6.php'>Capitulo 6</option>",
"<option value='../Chapter7/Chapter7.php'>Capitulo 7</option>",
"<option value='../Chapter8/Chapter8.php'>Capitulo 8</option>",
"<option value='../Chapter9/Chapter9.php'>Capitulo 9</option>",
"<option value='../Chapter10/Chapter10.php'>Capitulo 10</option>",
"<option value='../Chapter11/Chapter11.php'>Capitulo 11</option>",
"<option value='../Chapter12/Chapter12.php'>Capitulo 12</option>",
"<option value='../Chapter13/Chapter13.php'>Capitulo 13</option>",
"<option value='../Chapter14/Chapter14.php'>Capitulo 14</option>",
"<option value='../Chapter15/Chapter15.php'>Capitulo 15</option>",
"<option value='../Chapter16/Chapter16.php'>Capitulo 16</option>",
"<option value='../Chapter17/Chapter17.php'>Capitulo 17</option>",
"<option value='../Chapter18/Chapter18.php'>Capitulo 18</option>",
"<option value='../Chapter19/Chapter19.php'>Capitulo 19</option>",
"<option value='../Chapter20/Chapter20.php'>Capitulo 20</option>");
for($i=0; $i<getUserPrivilages($_SESSION['email']); $i++) {
 echo $options[$i];
}
?>
  </select>
  </form>
</div>
</body>
</html>