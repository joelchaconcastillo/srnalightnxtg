<?php
//include("MySQL.php");
include("New_Server.php");
switch($_REQUEST['Origen'])
{
	case "Consultar_Informacion":
	Consultar_Informacion();
	break;
	case "Actualizar_Informacion":
	Actualizar_Informacion();
	break;
	case "Actualizar_Pass":
	Actualizar_Pass();
	break;
}
function Consultar_Informacion()
{
	session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Conexion = Abrir_Conexion();
	$Resultado = mysql_query("SELECT * FROM Cuenta WHERE Id_Cuenta = '$Id_Cuenta'; ", $Conexion);
	$row = mysql_fetch_assoc($Resultado);
	echo json_encode($row);
	Cerrar_Conexion($Conexion);
}
function Actualizar_Informacion()
{
	session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Cuenta = $_POST['Cuenta'];
	$Nombre = $_POST['Nombre'];
	$Apellido = $_POST['Apellido'];
	$Email = $_POST['Email'];
	$_SESSION['Cuenta'] = $_POST['Cuenta'];
	$_SESSION['Nombre'] = $_POST['Nombre'];
	$_SESSION['Apellido'] = $_POST['Apellido'];
	$_SESSION['Email'] = $_POST['Email'];
	$Conexion = Abrir_Conexion();
	$Resultado = mysql_query("UPDATE Cuenta SET Cuenta = '$Cuenta', Nombre = '$Nombre', Apellido = '$Apellido', Email = '$Email' WHERE Id_Cuenta = '$Id_Cuenta'; ", $Conexion);
	Cerrar_Conexion($Conexion);
}
function Actualizar_Pass()
{
	session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Pass = $_POST['Pass'];
	$Actual_Pass = $_POST['Actual_Pass'];
	$Conexion = Abrir_Conexion();
	$Resultado = mysql_query("SELECT * FROM Cuenta WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	$row = mysql_fetch_assoc($Resultado);
	if($row['Pass'] != $Actual_Pass)
		echo json_encode(array('Cambio' => 'false'));
	else
	{
		mysql_query("UPDATE Cuenta SET Pass = '$Pass' WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
		echo json_encode(array('Cambio' => 'true'));
	}
	Cerrar_Conexion($Conexion);
}
?>
