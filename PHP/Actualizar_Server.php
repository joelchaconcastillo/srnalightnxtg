<?php
include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Consulta":
Consulta();
break;
}
function Consulta()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("select * from Cuentas where Id_Cuentas = ".$_SESSION['Id_Cuenta'],$Conexion);
	$Consulta=mysql_fetch_assoc($Resultado);
	$Nombre="Name: ".$Consulta['Nombre']."<br>";
	$Last_Names="Last Name: ".$Consulta['Apellidos']."<br>";
	$Email="Email: ".$Consulta['Email']."<br>";
	$NickName="Nick Name: ".$Consulta['Cuenta']."<br>";
	echo json_encode(array('Name'=>$Nombre,'Last_Name'=>$Last_Names,'Email'=>$Email,'Nick_Name'=>$NickName));
	Cerrar_Conexion($Conexion);
}

?>
