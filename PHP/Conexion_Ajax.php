<?php
date_default_timezone_set("America/Mexico_City");
include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Logout":
session_start();
session_destroy();
break;
case "DownList":
DownList();
break;
case "Autocompletar":
Autocompletar();
break;
		
}

function DownList()
{
	$Tabla=$_POST['Tabla'];
	$Columna=$_POST['Columna'];
	$Id=$_POST['Id'];
	$conexion=Abrir_Conexion();
	$q = mysql_query("select $Id,$Columna from $Tabla",$conexion);

	while($row =mysql_fetch_assoc($q))
  {
	  
	  echo '<option value="'.$row[$Id].'">'.$row[$Columna].'</option>';
	}


	Cerrar_Conexion($conexion);
}

/*Dataset Organismos*/
function Autocompletar()
{
	$conexion=Abrir_Conexion();
	$cad=$_REQUEST['term'];
	$Tabla=$_REQUEST['tabla'];
	$Columna=$_REQUEST['columna'];
	$Query="SELECT $Columna FROM $Tabla WHERE $Columna LIKE '%{$cad}%' group by $Columna";
	if($Tabla=="Librerias")
	{
		session_start();
		$ID=$_SESSION['Id_Cuenta'];
		$Query="SELECT $Columna FROM $Tabla WHERE Id_Cuentas= $ID AND $Columna LIKE '%{$cad}%' group by $Columna";
	}
	if($Tabla=="Cuentas")
	{
		session_start();
		$ID=$_SESSION['Id_Cuenta'];
		$Query="SELECT $Columna FROM $Tabla WHERE Id_Cuentas != $ID AND $Columna LIKE '%{$cad}%' AND Tipo_Cuenta LIKE 'User' group by $Columna";
	}
	$q = mysql_query($Query,$conexion);

if(mysql_num_rows($q)<200 && mysql_num_rows($q)>0 )
{
	while($row =mysql_fetch_assoc($q))
  {
	  
	  $results[] =  $row[$Columna];
	}
}
else if(mysql_num_rows($q)==0)
{
	//$results[]="There are not elements";
}
else{$results[]="There are more than 200 elements";}
 if(!empty($results))
 {
echo json_encode($results);
 }
	Cerrar_Conexion($conexion);
}
/*Llenar tablas Anotacion, Subtype, Subdomain*/

/*Nueva Librería*/


?>
