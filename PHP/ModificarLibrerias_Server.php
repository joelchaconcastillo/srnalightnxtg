<?php
date_default_timezone_set("America/Mexico_City");
include("Administrar_Librerias.php");
switch($_REQUEST['Origen'])
{
case "Update_Libreria":
Update_Libreria();
break;
case "MLibrary":
MLibrary();
break;
case "Delete_Library":
Delete_Library($_POST['Id_Libreria']);
break;
}
function Update_Libreria()
{
	$conexion=Abrir_Conexion();
	$row=$_POST['Row'];
	$column=$_POST['Column'];
	$data=$_POST['Data'];
	$q = mysql_query("UPDATE Libreria SET $column = '$data' WHERE Id_Libreria LIKE '$row'",$conexion);
	
	echo "Error: ".mysql_error();
	Cerrar_Conexion($conexion);
}
function MLibrary()
{
	session_start();
	$Conexion=Abrir_Conexion();
	/*if(isset($_SESSION['Nombre_Libreria']))
	{
		$q = mysql_query("SELECT L.*, C.Nombre as Nombre_Cuenta, C.Cuenta FROM Libreria L inner join Cuenta C on C.Id_Cuenta = L.Id_Cuenta WHERE L.Nombre = '".$_SESSION['Nombre_Libreria']."' AND L.Id_Cuenta = ".$_SESSION['Id_Cuenta'],$Conexion);
		unset($_SESSION['Nombre_Libreria']);
	}
	else*/
		$q = mysql_query("SELECT L.*, C.Nombre as Nombre_Cuenta, C.Cuenta FROM Libreria L inner join Cuenta C on C.Id_Cuenta = L.Id_Cuenta WHERE L.Id_Cuenta = ".$_SESSION['Id_Cuenta'],$Conexion);
 echo '<div class="panel panel-primary">
		<div class="panel-heading">My data</div>
		<div class="panel-body" style="width:100%; overflow:auto;">';	
echo '<table id="Tabla_Librerias" style="text-align:center; vertical-align:middle;" cellspacing="0" border="0" class="table display"  >
<thead> 
<tr>
<th>Library</th>
<th>KeyWords</th>
<th>Tissue</th>
<th>Sequence platform</th>
<th>Protocol</th>
<th>Date</th>
<th>Visualize</th>
<th></th>
<th></th>
<th></th>
</tr>';
echo '</thead>';
echo '<tbody>'; 
 while($row =mysql_fetch_assoc($q))
  {
	  $Resultado=mysql_query("SELECT COUNT(*) as Cuenta FROM HTOP WHERE Id_Libreria = ".$row['Id_Libreria'],$Conexion);
	  $Numero=mysql_fetch_assoc($Resultado);
	  if($Numero['Cuenta']>0) continue;
	  if($row['Configurado']==0) continue;
	 
	echo '<tr id="hidetr'.$row['Id_Libreria'].'">';
	echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Nombre" onclick="Actualizar(this)">'.$row['Nombre'].'</td>';
	echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Palabras_Clave" onclick="Actualizar(this)">'.$row['Palabras_Clave'].'</td>';
	echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Tejido" onclick="Actualizar(this)">'.$row['Tejido'].'</td>';
	echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Plataforma" onclick="Actualizar(this)">'.$row['Plataforma'].'</td>';
	echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Descripcion" onclick="Actualizar(this)">'.$row['Descripcion'].'</td>';
	echo '<td >' . $row['Fecha'] . "</td>";
	echo '<td ><input type="button"  class="btn btn-primary" data-toggle="modal" data-target="#Modal_JBrowse_Modificar_Librerias" value="JBrowse" onclick="JBrowse(\'./Users/Usuario'.$row['Id_Cuenta'].'/'.$row['Id_Libreria'].'/JBrowse/index.html\')"> </td>';
	echo '<td ><input type="button" class="btn btn-primary" Id_Libreria="'.$row['Id_Libreria'].'" onclick="Mostrar_Ventana(this)" value="Text Browser"></td>';
	echo '<td><div Id_Libreria="'.$row['Id_Libreria'].'" class="btn btn-primary" onclick="Descargar(this)" >Download</div></td>';
	echo '<td> <input type="button" class="btn btn-danger" Id_Libreria="'.$row['Id_Libreria'].'" onclick="Borrar_Libreria(this)" value="Delete"></td>';
  echo "</tr>";
  }
  echo '</tbody>'; 
echo "</table>";
echo "</div";
echo "</div>";
Cerrar_Conexion($Conexion);

}
?>
