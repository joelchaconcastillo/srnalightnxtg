<?php
include("MySQL.php");
$Id_HTOP = $argv[1];
Procesar_Comandos($Id_HTOP);
//Send_Email($argv[1],$argv[2], $argv[3]);

function Procesar_Comandos($Id_HTOP)
{
	$Ruta_Relativa="";
	$Mostrar_Graficos="";
	Limpiar_Directorio();
	$Conexion=Abrir_Conexion();
	mysql_query("UPDATE HTOP SET Activo = 1 WHERE Id_HTOP = $Id_HTOP",$Conexion);
	#mysqli_query($Conexion, "UPDATE HTOP SET Activo = 1 WHERE Id_HTOP = $Id_HTOP");
	$Query="SELECT P.*, L.*, Li.*, H.* FROM Libreria Li inner join HTOP H on Li.Id_Libreria = H.Id_Libreria inner join PIPE P on P.Id_HTOP = H.Id_HTOP inner join Lista_Comandos L on L.Id_PIPE = P.Id_PIPE";
	$Query.=" WHERE H.Id_HTOP = $Id_HTOP";
	$Resultado=mysql_query($Query, $Conexion);
	#$Resultado=mysqli_query($Conexion, $Query);
	Cerrar_Conexion($Conexion);
	
	while($row=mysql_fetch_assoc($Resultado))
	{
		$Ruta_Relativa=$row['Ruta'];
		
		$Mostrar_Graficos=$row['Mostrar_Graficos'];
		$Comando=$row['Comando'];
		if($row['Guardar_Salida_Estandar']=="1")
		{
			$STDOUT = "$Ruta_Relativa/Reporte_STDOUT";
			$Comando.=" > $STDOUT  2>&1 ";
			Ejecutar_Comando($Comando);
			if(file_exists($STDOUT))
			{
				$File = file_get_contents($STDOUT);
				$File = addslashes($File);
				$Conexion = Abrir_Conexion();	
				mysql_query("UPDATE PIPE SET Stdout = '$File' WHERE Id_PIPE =".$row['Id_PIPE'], $Conexion);
				#mysqli_query($Conexion, "UPDATE PIPE SET Stdout = '$File' WHERE Id_PIPE =".$row['Id_PIPE']);
				Cerrar_Conexion($Conexion);
			}
		}
		else
		Ejecutar_Comando($Comando);
	}
	$Conexion=Abrir_Conexion();
	//Borra la lista de comandos
	mysql_query("DELETE L.* FROM HTOP H inner join PIPE P on P.Id_HTOP = H.Id_HTOP inner join Lista_Comandos L on L.Id_PIPE = P.Id_PIPE WHERE H.Id_HTOP =  ".$Id_HTOP, $Conexion);
	#mysqli_query($Conexion, "DELETE L.* FROM HTOP H inner join PIPE P on P.Id_HTOP = H.Id_HTOP inner join Lista_Comandos L on L.Id_PIPE = P.Id_PIPE WHERE H.Id_HTOP =  ".$Id_HTOP);
	//Actualizar reporte archivo
	mysql_query("UPDATE HTOP SET Activo = 0, Mostrar_Reporte = 1, Ventana_Abierta = 1, Visto = 1 WHERE Id_HTOP = $Id_HTOP",$Conexion);
	#mysqli_query($Conexion, "UPDATE HTOP SET Activo = 0, Mostrar_Reporte = 1, Ventana_Abierta = 1, Visto = 1 WHERE Id_HTOP = $Id_HTOP");
	Cerrar_Conexion($Conexion);	
	
	if($Mostrar_Graficos =="1")
	Analisis_Datos( Revisar_Archivo($Ruta_Relativa), $Ruta_Relativa);
	
	//Generar_Reporte();
	//if($row['Eviar_Email']=="1")
	//Enviar_Email();
	//Agregar_Comando(Analisis_Datos($Ruta_Archivo, $Ruta), $Id_HTOP	);
	//Revisar el formato de salida en el archivo
	
}
function Limpiar_Directorio()
{
	global $Id_HTOP;
	
	$Conexion = Abrir_Conexion();
	$Reusltado = mysql_query("SELECT L.* FROM HTOP H inner join Libreria L on L.Id_Libreria = H.Id_Libreria WHERE Id_HTOP = $Id_HTOP", $Conexion);
	#$Reusltado = mysqli_query($Conexion, "SELECT L.* FROM HTOP H inner join Libreria L on L.Id_Libreria = H.Id_Libreria WHERE Id_HTOP = $Id_HTOP");
	$row = mysql_fetch_assoc($Resultado);
	Cerrar_Conexion($Conexion);
	 foreach( glob($row['Ruta']."/{*}",GLOB_BRACE) as $Elemento )
	 {
		 if(basename($Elemento) == basename($row['Archivo'])) continue;
		 else if("BigWig" == basename($Archivo)) continue;
		 else if(preg_match("/Users/", $Elemento)) shell_exec("rm $Elemento");
	 }
}
function Revisar_Archivo($Ruta_Relativa)
{
	foreach(glob($Ruta_Relativa."/{*}",GLOB_BRACE) as $Elemento)
	{
		if(basename($Elemento) == "File_Fasta")
			return $Elemento;
		else if(basename($Elemento) == "File_CSFasta")
			return $Elemento;
		else if(basename($Elemento) == "File_FastaQ")
			return $Elemento;
		else if(basename($Elemento) == "File_CSFastaQ")
			return $Elemento;
	}
}
function Send_Email($Email,$Ruta, $Nombre_Libreria)
{
	print $Email;
		$cabeceras = 'From: TransciptomeGene@chacon.com' . "\r\n" ;
		$mensaje = "Your experiment ".$Nombre_Libreria." has been finshed successfully, please check you results $Ruta";

	mail($Email,'Notification of process',$mensaje,$cabeceras);
}
function Ejecucion_Proceso($Id_HTOP, $PID)
{
   $Conexion=Abrir_Conexion();
   mysql_query("update HTOP set Activo = 1, PID = $PID where Id_HTOP = $Id_HTOP",$Conexion);
   #mysqli_query($Conexion, "update HTOP set Activo = 1, PID = $PID where Id_HTOP = $Id_HTOP");
   Cerrar_Conexion($Conexion);
}
function Analisis_Datos($Ruta_Archivo, $Ruta_Relativa)
{
	Ejecutar_Comando("fastx_quality_stats -i ".$Ruta_Archivo." -o ".$Ruta_Relativa."/Reporte_Estadisticas ");
}
function Ejecutar_Comando($Comando)
{
	global $Id_HTOP;
	$Error = exec("$Comando ; echo $?");
	if($Error > 0) 
	{
		$Conexion=Abrir_Conexion();
		mysql_query("UPDATE HTOP SET Activo = 2, Mostrar_Reporte = 1, Visto = 1, Ventana_Abierta = 1 WHERE Id_HTOP = $Id_HTOP",$Conexion);
		Cerrar_Conexion($Conexion);
		exit();
	}	
}
?>
