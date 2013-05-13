<?php

require("../phpscripts/functions.php");
require("../phpscripts/config.php");

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PronuncieBienElIngles.com - Ingrese</title>
<link rel="stylesheet" type="text/css" href="../style.css" title="Default" />
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
<script type="text/javascript" src="swfobject.js"></script>
<style type="text/css">
html body #logo {
margin-bottom: 0;
}
</style>
</head>
<body>
<div id="top_box">
  <h5><a href="#maincontent">Skip to the main content</a></h5>
  <ul id="tabnav">
    <li><a href="../escribanos/index.php">Escribanos</a></li>
    <li><a href="../info/index.php">Informacion</a></li>
    <li><a href="index.php" class="active">Ingrese</a></li>
    <li><a href="../index.php">Home</a></li>
  </ul>
</div>

<div id="header">
  <a href="../index.php"><img src="../images/logo.jpg" alt="Logo" name="logo" width="700" height="150" id="logo" longdesc="http://pronunciebienelingles.com/images/logo.jpg" /></a>
</div>

<div id="maincontent" class="clearfix">
<?php
if($_SESSION['loggedin']) {
?>
<p>Ud. está conectado(a) : <?php echo $_SESSION['email'] . " <a href='../logout.php'>Salir</a>"; ?></p>
<p>Este curso requiere  Flash Player 9...</p>
<div id="flashcontent">
  <p>Sólo demora 2 minutos bajar la versión más reciente de Flash Player.<br />
  Allí encontrará todas las instrucciones necesarias.</p>
  <p><a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash&Lang=LatinAmerica&P5_Language=Spanish&ogn=LA-gntray_dl_getflashplayer_la" target="_blank"><img src="../images/getplayer.jpg" alt="Obtener Flash Player" id="getplayer" width="168" height="48" border="0" longdesc="http://pronunciebienelingles.com/images/getplayer.jpg" /></a></p>
</div>
<script type="text/javascript">
   var so = new SWFObject("ready.swf", "ready", "550", "50", "9", "#FFFFFF");
   so.write("flashcontent");
</script>
<noscript>
<p><a href="http://www.adobe.com/go/getflashplayer/"><img src="http://pronunciebienelingles.com/images/download_now.gif" border=0 alt="Get Flash"><br />Download required Flash Player here.</a></p>
</noscript>
<p style="margin-left: 2em;">
<?php
$chapters = array("<b>Vea 3 capítulos gratis como demostración</b><br /><br /><a class='chapter' href='../Chapter1/Chapter1.php'>Capitulo 1</a> El Inglés tiene 35 sonidos y el Español 20.  Los órganos del habla.<br />",
"<a class='chapter' href='../Chapter2/Chapter2.php'>Capitulo 2</a> Los 11 sonidos vocales del Inglés. Diferencia entre sonidos sordos y sonoros.<br />",
"<a class='chapter' href='../Chapter3/Chapter3.php'>Capitulo 3</a> Tres sonidos vocales: #1 [i], #3 [ɛ] y #4 [e]. Final de la demostración gratis.<br />",
"<br /><b>8 sonidos vocales restantes y las consonantes oclusivas</b><br /><br /><a class='chapter' href='../Chapter4/Chapter4.php'>Capitulo 4</a> Otros tres sonidos vocales: #6 [a], #8 [o] y #10 [u]. Práctica con sonidos presentados.<br />",
"<a class='chapter' href='../Chapter5/Chapter5.php'>Capitulo 5</a> Cuatro nuevos sonidos vocales: #2 [I], #5 [æ], #7 [ɔ] y #9 [U]. Comparaciones.<br />",
"<a class='chapter' href='../Chapter6/Chapter6.php'>Capitulo 6</a> Sonidos consonantes oclusivos: [p], [b]; [t], [d]; [k], [g]. Sonidos sordos y sonoros.<br />",
"<a class='chapter' href='../Chapter7/Chapter7.php'>Capitulo 7</a> Ultimo sonido vocal: #11 “schwa” [ə]. Consonantes [f], [v] y [l]<br />",
"<br /><b>Las consonantes nasales y las silbantes</b><br /><br /><a class='chapter' href='../Chapter8/Chapter8.php'>Capitulo 8</a> Sílabas tónicas y átonas. Sonido fricativo linguo-dental sordo y sonoro de TH.<br />",
"<a class='chapter' href='../Chapter9/Chapter9.php'>Capitulo 9</a> Sonidos nasales: labial [m], alveolar [n] y velar [ng].<br />",
"<a class='chapter' href='../Chapter10/Chapter10.php'>Capitulo 10</a> Sonidos silbantes sordo [s] y sonoro [z]. Pronunciación de S final.<br />",
"<a class='chapter' href='../Chapter11/Chapter11.php'>Capitulo 11</a> Silbante de canal ancho sordo sh y sonoro zh.  Africado sordo tsh y sonoro dzh.<br />",
"<br /><b>Ultimas consonantes y las semivocales</b><br /><br /><a class='chapter' href='../Chapter12/Chapter12.php'>Capitulo 12</a> Sonido retroflexo [r] en diversas posiciones. Sonido [h].<br />",
"<a class='chapter' href='../Chapter13/Chapter13.php'>Capitulo 13</a> Semivocales [w] y [j]. Pronunciación de terminación –ed de  verbos en tiempo pasado.<br />",
"<a class='chapter' href='../Chapter14/Chapter14.php'>Capitulo 14</a> Pronunciación de las palabras de importancia secundaria. Uniones de palabras.<br />",
"<br /><b>Revisión de los sonidos del Inglés y casos especiales</b><br /><br /><a class='chapter' href='../Chapter15/Chapter15.php'>Capitulo 15</a> Revisión de los 35 sonidos del Inglés. Palabras de igual pronunciación.<br />",
"<a class='chapter' href='../Chapter16/Chapter16.php'>Capitulo 16</a> Diferente acentuación según la función de la palabra. Sustantivos compuestos.<br />",
"<a class='chapter' href='../Chapter17/Chapter17.php'>Capitulo 17</a> Verbos compuestos de dos palabras. Práctica adicional con [v] y con [r].<br />",
"<br /><b>Prácticas de entonación.</b><br /><br /><a class='chapter' href='../Chapter18/Chapter18.php'>Capitulo 18</a> Práctica con 13 canciones norteamericanas.<br />",
"<a class='chapter' href='../Chapter19/Chapter19.php'>Capitulo 19</a> La entonación de las frases en inglés. según su contenido.<br />",
"<a class='chapter' href='../Chapter20/Chapter20.php'>Capitulo 20</a> Práctica final de velocidad y con textos literarios.");
$privilages = getUserPrivilages($_SESSION['email']);
for($i=0; $i<$privilages; $i++) {
 echo $chapters[$i];
}
if($privilages == 99) {
  echo "<br /><br /><a href='../phpscripts/subscribers.php'>View Subscribers and Registered Visitors</a>";
}
if($privilages < 20) {
?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="fbrown30@hotmail.com" />
<input type="hidden" name="item_name" value="Pronunciebienelingles Course" />
<input type="hidden" name="amount" value="19.70" />
<input type="hidden" name="no_shipping" value="0" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="lc" value="PE" />
<input type="hidden" name="bn" value="PP-BuyNowBF" />
<input type="image" src="../images/subscribe.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<p>Si Ud. no tiene tarjeta PayPal, puede hacer el depósito o
transferencia bancaria a la cuenta:<br />
Scotiabank - cuenta corriente moneda extranjera<br />
Beneficiario: Bibliolatin SAC<br />
No. de cuenta: 000-3046590<br />
Código interbancario: 00906400000304659070<br />
Si tiene alguna consulta, llamar al tf. 243-2857</p>
<?php
}
?>
</p>
<?php
} else {
?>
<p>Debe ingresar para poder ver el curso.</p>
<div id="registerbox">
  <h4>Registrese gratis ahora!</h4>
  <form action="index.php" method="post">
  <span class="label">Su Email</span><span class="element"><input type="text" name="regemail" maxlength="30" /></span><br /><br />
  <span class="label">Contraseña</span><span class="element"><input type="password" name="regpass1" maxlength="30" /></span><br /><br />
  <span class="label">Escriba contraseña otra vez</span><span class="element"><input type="password" name="regpass2" /></span><br /><br />
  <span class="label">&nbsp;</span><span class="element"><input type="submit" value="Registrese" /></span></form>
  <?php if ($regerrmsg) { echo "<br />" . $regerrmsg; } ?>
</div>
<div id="loginbox">
  <h4>Ingresar otra vez</h4>
  <form action="index.php" method="post">
  <span class="label">Su Email</span><span class="element"><input type="text" name="subemail" maxlength="30" /></span><br /><br />
  <span class="label">Su contraseña</span><span class="element"><input type="password" name="subpass" maxlength="30" /></span><br /><br />
  <span class="label">Recuérdeme la próxima vez</span><span class="element"><input type="checkbox" name="remember" /></span><br /><br />
  <span class="label">&nbsp;</span><span class="element"><input type="submit" value="Ingresar" /></span></form><br /><br />
  <a href="../forgotpass.php">Olvidó su contraseña?</a><?php if($alert) { echo "<br />" . $alert; } ?>
</div>
<?php
}
?>
</div>

<div id="footer">
 <p>Diseñado por Matthew Whitlock Copyright 2007 por Fortunato Brown Reservados todos los derechos</p>
</div>

</body>
</html>
