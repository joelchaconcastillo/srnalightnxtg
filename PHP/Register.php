
<html
<title>Activation</html>
<head>
</head>
<body>

<?php
include("MySQL.php");
$Correo= base64_decode($_REQUEST['Id']);
$Pass=base64_decode($_REQUEST['Pass']);
$User=base64_decode($_REQUEST['User']);
$conexion = Abrir_Conexion();
$q = mysql_query("UPDATE Cuentas SET Confirmacion = 1 WHERE Email = '$Correo'",$conexion);

  mysql_close($conexion);
  echo "Congratulation you email ".$Correo." has been confirmed <br> ";
  echo 'Your Username: '.$User."<br>";
  echo 'Your Pass: '.$Pass."<br>"; 
 mkdir("mkdir Users/$User",0777)
 ?>
</body>
<html
