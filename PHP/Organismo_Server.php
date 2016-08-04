<?php

include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Alta_Organismo":
Nuevo_Organismo();
break;
case "Mostrar_Organismo":
Mostrar_Organismo();
break;
case "Borrar_Organismo":
Borrar_Organismo();
break;
case "Actualizar_Organismo":
Actualizar_Organismo();
break;
case "Actualizar_Ecotipo":
Actualizar_Ecotipo();
break;
}
function Nuevo_Organismo()
{
	
	session_start();
	$Id_Cuenta= $_SESSION['Id_Cuenta'];
	$Nombre_Organismo=$_POST['Nombre_Organismo'];
	$Ecotipo=$_POST['Ecotipo_Organismo'];
	$Conexion=Abrir_Conexion();
	mysql_query("INSERT INTO Organismo VALUES (NULL, '$Nombre_Organismo', '$Ecotipo', NOW() , $Id_Cuenta )" ,$Conexion);
	
	Cerrar_Conexion($Conexion);
}
function Mostrar_Organismo()
{
$conexion=Abrir_Conexion();

$q = mysql_query("SELECT * FROM Organismo ",$conexion);
Cerrar_Conexion($conexion);	
echo "<table id='Tabla_Organismos' style='text-align:center; vertical-align:middle;' cellspacing='0' border='0' class='table display'>
<thead>
<tr>
<th style='text-align:center;'>Organism</th>
<th style='text-align:center;'>Variety Ecotype</th>
<th style='text-align:center;'>Date</th>
<th style='text-align:center;'>User</th>
<th style='text-align:center;'>Username</th>
<th style='text-align:center;'>Delete</th>
</tr>
</thead>
<tbody>";
 while($row =mysql_fetch_assoc($q))
  {
	  $Informacion_Propietario=Consultar_Propietario($row['Id_Cuenta']);
		echo '<tr id="hidetr'.$row['Id_Organismo'].'">';
		echo '<td style="cursor:pointer;" Id_Organismo="'.$row['Id_Organismo'].'" onclick="Actualizar_Nombre_Organismo(this)">' . $row['Organismo'] . "</td>";
		echo '<td style="cursor:pointer;" Id_Organismo="'.$row['Id_Organismo'].'" onclick="Actualizar_Ecotipo_Organismo(this)">' . $row['Ecotipo'] . "</td>";
		echo '<td >' . $row['Fecha_Alta'] . "</td>";
		echo '<td >' .$Informacion_Propietario['Nombre']." ".$Informacion_Propietario['Apellido']."</td>";
		echo '<td >' .$Informacion_Propietario['Cuenta']."</td>";
		echo '<td > <input type="button" class="btn btn-danger" onclick="Borrar_Organismo(this)" Id_Organismo="'.$row['Id_Organismo'].'" value="Delete">  </td>';
		echo "</tr>";
  }
  
echo "
</tbody>
</table>";
}
function Consultar_Propietario($Id_Cuenta)
{
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Cuenta where Id_Cuenta = $Id_Cuenta",$Conexion);
	Cerrar_Conexion($Conexion);
	$row= mysql_fetch_assoc($Resultado);
	return $row;
}
function Borrar_Organismo()
{
	$Id_Organismo=$_POST['Id_Organismo'];
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Genoma WHERE Id_Organismo = $Id_Organismo", $Conexion);
	Cerrar_Conexion($Conexion);
	while($row=mysql_fetch_assoc($Resultado))
	{
		Borrar_Genoma($row['Id_Genoma']);
	}
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("DELETE FROM Organismo WHERE Id_Organismo = $Id_Organismo", $Conexion);
	
}
function Borrar_Genoma($Id_Genoma)
{
	
	$Conexion = Abrir_Conexion(); 	
	$Resultado=mysql_query("SELECT * FROM Genoma WHERE Id_Genoma = $Id_Genoma", $Conexion);
	$row=mysql_fetch_assoc($Resultado);
	shell_exec("rm -fr  ./../Indexes/".$row['Id_Genoma']);
	
	$Resultado=mysql_query("SELECT * FROM Cuentas WHERE Tipo_Cuenta = 'User'",$Conexion);

	while($Usuario=mysql_fetch_array($Resultado))
		{		
			shell_exec("rm -fr ./../Users/Usuario".$Usuario['Id_Cuenta']."/JBrowse/".$row['Id_Genoma']."index.html");	
			shell_exec('./../Scripts/Delete.sh "DELETE FROM Molecula'.$Usuario['Id_Cuenta'].' WHERE Id_Genoma = '.$Id_Genoma.';" > /dev/null 2>&1 &');
		}
	mysql_query("DELETE FROM Genoma WHERE Id_Genoma = $Id_Genoma",$Conexion);
	
	 Cerrar_Conexion($Conexion);
}
function Actualizar_Organismo()
{
	$Conexion=Abrir_Conexion();
	$Organismo=$_POST['Organismo'];
	$Id_Organismo=$_POST['Id_Organismo'];
	mysql_query("UPDATE Organismo SET Organismo = '$Organismo' WHERE Id_Organismo = '$Id_Organismo'",$Conexion);
	Cerrar_Conexion($Conexion);
}
function Actualizar_Ecotipo()
{
	$Conexion=Abrir_Conexion();
	$Ecotipo=$_POST['Ecotipo'];
	$Id_Organismo=$_POST['Id_Organismo'];
	mysql_query("UPDATE Organismo SET Ecotipo = '$Ecotipo' WHERE Id_Organismo = '$Id_Organismo'",$Conexion);
	Cerrar_Conexion($Conexion);
}
?>
