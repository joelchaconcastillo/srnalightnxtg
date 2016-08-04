<?php
include("MySQL.php");
function Delete_Library($Id_Libreria)
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Id_Cuenta=$_SESSION["Id_Cuenta"];
	
	$q = mysql_query("select * from Cuenta, Libreria where Cuenta.Id_Cuenta= $Id_Cuenta and Libreria.Id_Libreria= $Id_Libreria and Libreria.Id_Cuenta=Cuenta.Id_Cuenta",$Conexion);
	$row=mysql_fetch_assoc($q);
	if(isset($Id_Libreria) && isset($Id_Cuenta))
	shell_exec("rm -fr ./../Users/Usuario$Id_Cuenta/$Id_Libreria");
	mysql_query("delete from Libreria WHERE Id_Libreria = $Id_Libreria",$Conexion);
	$Resultado=mysql_query("select * from Genoma",$Conexion);
	//Se borran lo archivo JBrowse que se encuentran en todos los genomas disponibles
	while($row=mysql_fetch_assoc($Resultado))
	{
		shell_exec("rm -f ./../Indexes/".$row['Id_Genoma']."/JBrowse/".$Id_Libreria."trackList.json");
		Borrar_Track("./../Indexes/".$row['Id_Genoma']."/JBrowse/Usuario".$Id_Cuenta."trackList.json", $Id_Libreria);
	}
	shell_exec('./../Scripts/Delete.sh "DELETE FROM Molecula'.$Id_Cuenta.' WHERE Id_Libreria = '.$Id_Libreria.';" > /dev/null 2>&1 &');
	Cerrar_Conexion($Conexion);
	
	
}
function Borrar_Track($Path,$Id_Libreria)
{
	
	$Documento = file($Path);
	$Nuevo="";
	$Posicion="";
	foreach($Documento as $i =>$Linea)
	{
		if(preg_match("/\"label\" : \" $Id_Libreria/i",$Linea)) 
		{
			$Posicion=$i;
			break;
		}
	}
	for($i=$Posicion;$i>0;$i--)
	{
		if(preg_match("/\},/i",$Documento[$i]))
		{
			$Apertura_Bloque=$i;
		   break;
		}
	}
	
	if($Posicion!=0)
	{
			foreach($Documento as $i =>$Linea)
			{
				  if($i<$Apertura_Bloque || $i >$Apertura_Bloque+6)
				{
					$Nuevo[]=$Linea;
				}
			}
			file_put_contents($Path,$Nuevo);
	}
	
	
	
	
}
?>
