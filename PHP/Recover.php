
<html
<title>Recover</html>
<head>
</head>
<body>

<?php
$Correo= base64_decode($_REQUEST['Id']);
$Pass=base64_decode($_REQUEST['Pass']);
$User=base64_decode($_REQUEST['User']);

  echo "Congratulation you email ".$Correo." has been confirmed <br> ";
  echo 'Your Username: '.$User."<br>";
  echo 'Your Pass: '.$Pass."<br>"; 
 ?>
</body>
<html
