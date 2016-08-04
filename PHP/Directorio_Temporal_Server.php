<?php
include("MySQL.php");
switch($_REQUEST["Origen"])
{
   case "Abrir_Folder_Temporal":
   Consultar_Directorio_Temporal();
   break;
   case "Listar_Librerias":
   Listar_Librerias();
   break;
   case "Listar_Historial":
   Listar_Historial();
   break;
   case "Borrar_Archivo":
   Borrar_Archivo();
   break;
   case "Borrar_Archivo_Historial":
   Borrar_Archivo_Historial();
   break;
}
function Borrar_Archivo_Historial()
{
	$Id_Historial_Libreria = $_POST['Id_Historial_Libreria'];
	$Archivo = $_POST['Archivo'];
	$Conexion = Abrir_Conexion();
	if(is_dir(dirname($Archivo)) && file_exists($Archivo))
	shell_exec("rm -fr ".dirname($Archivo));
	mysql_query("DELETE FROM Historial_Libreria WHERE Id_Historial_Libreria = $Id_Historial_Libreria" ,$Conexion);
	Cerrar_Conexion($Conexion);
}
function Listar_Historial()
{
	session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Historial_Libreria WHERE Id_Cuenta = $Id_Cuenta ", $Conexion);
  
  		echo '<table style="text-align:center;" id="Tabla_Historial_Libreria" cellpadding="0" cellspacing="0" border="0" class="table display">
		<thead>
		<tr>
		<th style="text-align:center;">Library</th>
		<th style="text-align:center;">File</th>
		<th style="text-align:center;">Date</th>
		<th style="text-align:center;">Format</th>
		<th style="text-align:center;">Number of lines</th>
		<th style="text-align:center;">Size</th>
		<th style="text-align:center;">Download</th>
		<th></th>
		</tr>
		</thead>';
		
		echo '<tbody>'; 
		
		 while( $row=mysql_fetch_assoc($Resultado))
			   {
				    echo '<tr>'; 
				    echo '<td >' . $row['Nombre_Libreria']. '</td>';
				    echo '<td >' . basename($row['Archivo']). '</td>';
				    echo '<td >' .  $row['Fecha']. '</td>';
				    echo '<td >' .  $row['Formato']. '</td>';
				    echo '<td >' .  intval($row['Numero_Lineas']). '</td>';
				    echo '<td >' . formatbytes($row['Archivo'],"MB"). '</td>';
				    echo '<td ><button class="btn btn-success" Archivo="'.$row['Archivo'].'" onclick="Descargar_Archivo(this)" >Download</button></td>';
				  //  echo '<td ><button class="btn btn-success" Archivo="'.$Archivo.'" onclick="Visualizar_Archivo(\''.$Archivo.'\')" >Text Browse</button></td>';
				    echo '<td ><button class="btn btn-danger" Id_Historial_Libreria="'.$row['Id_Historial_Libreria'].'" Archivo="'.$row['Archivo'].'" onclick="Borrar_Archivo_Historial(this)" >Delete</button></td>';
					echo '</tr>';  
			   }
		echo '</tbody>'; 
		echo "</table>";
	
	 
	Cerrar_Conexion($Conexion);
}
function Borrar_Archivo()
{
	$Archivo = $_POST['Archivo'];
	if(file_exists($Archivo))
	{
		shell_exec("rm $Archivo");
		if(file_exists("$Archivo.zip"))
		shell_exec("rm -fr $Archivo.zip");
		shell_exec("$Archivo.zip");
		$Ruta=dirname($Archivo);
		if(!count(glob("$Ruta/{*}",GLOB_BRACE)))
		shell_exec("rm -fr $Ruta");
	}
}
function Listar_Librerias()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Consulta=Array();
	$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Cuenta = ".$_SESSION['Id_Cuenta'], $Conexion);
	Cerrar_Conexion($Conexion);
	while($row=mysql_fetch_array($Resultado))
	{
		$Ruta=glob($row['Ruta']."/Temp/{*}",GLOB_BRACE);
		if(!count($Ruta))
		continue;
	  $Consulta[]=$row;
	}
	echo json_encode($Consulta);
}
function Consultar_Directorio_Temporal()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Libreria = ".$_POST['Id_Libreria'],$Conexion);
  	$row=mysql_fetch_assoc($Resultado);
  		echo '<table style="text-align:center; heigh:80%;" id="Tabla_Archivos_Temporales" cellspacing="0" border="0" class="table display">
		
		<thead>
		<tr>
		<th>File</th>
		<th>Last modification</th>
		<th>Size</th>
		<th>Download</th>
		<th></th>
		</tr>
		</thead>';
		echo '<tbody>'; 
		
		 foreach( glob($row['Ruta']."/Temp/{*}",GLOB_BRACE) as $Archivo)
			   {
				   if(pathinfo($Archivo, PATHINFO_EXTENSION) == "zip") continue;
				    echo '<tr>'; 
				    echo '<td >' . basename($Archivo). '</td>';
				    echo '<td >' .  date("F d Y H:i:s.", filemtime($Archivo)). '</td>';
				    echo '<td >' . formatbytes($Archivo,"MB"). '</td>';
				    echo '<td ><button class="btn btn-success" Archivo="'.$Archivo.'" onclick="Descargar_Archivo(this)" >Download</button></td>';
				  //  echo '<td ><button class="btn btn-success" Archivo="'.$Archivo.'" onclick="Visualizar_Archivo(\''.$Archivo.'\')" >Text Browse</button></td>';
				    echo '<td ><button class="btn btn-danger" Archivo="'.$Archivo.'" onclick="Borrar_Archivo_Directorio_Temporal(this)" >Delete</button></td>';
					echo '</tr>';  
			   }
		echo '</tbody>'; 
		echo "</table>";
	
	 
	Cerrar_Conexion($Conexion);
}

?>
