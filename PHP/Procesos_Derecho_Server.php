<?php 

include("MySQL.php");
switch($_REQUEST['Origen'])
{
   case "Finalizar_Procesos":
   Finalizar_Procesos();
   break;
   case "Cargar_Procesos":
   Cargar_Procesos();
   break;
   case "Eliminar_Proceso":
   Eliminar_Proceso();
   break;
   case "Sesion_Datos":
   Sesion_Datos($_POST['Id_HTOP']);
   break;
   case "Proceso_Siguiente":
   Proceso_Siguiente();
   break;
   case "Cargar_Procesos_MySQL":
   Cargar_Procesos_MySQL();
   break;
   case "Cancelar_Consulta":
   Cancelar_Consulta();
   break;
   case "Descargar_Archivo_Query":
   Descargar_Archivo_Query();
   break;
   case "Descargar_Archivo":
   Descargar_Archivo();
   break;
   case "Actualizar_Enviar_Correo":
   Actualizar_Enviar_Correo();
   break;
   case "Consultar_Flujo":
   Consultar_Flujo();
   break;
   case "Reiniciar_PIPE":
   Reiniciar_PIPE();
   break;
   case "Revisar_Cambios":
   Revisar_Cambios();
   break;
   case "Limpiar_Visto":
   Limpiar_Visto();
   break;
}
function Limpiar_Visto()
{
		session_start();
		$Conexion = Abrir_Conexion();
		$Id_Cuenta = $_SESSION['Id_Cuenta'];
		$Select ="UPDATE HTOP H INNER JOIN Libreria L on H.Id_Libreria = L.Id_Libreria ";
		$Select.="INNER JOIN Cuenta C on C.Id_Cuenta = L.Id_Cuenta SET H.Visto = 0 ";
		$Select .="WHERE C.Id_Cuenta = $Id_Cuenta  ";
		mysql_query($Select, $Conexion);
		Cerrar_Conexion($Conexion);
}
function Revisar_Cambios()
{
		session_start();
		$Conexion = Abrir_Conexion();
		$Id_Cuenta = $_SESSION['Id_Cuenta'];
		
		$Select ="SELECT COUNT(*) FROM Cuenta C INNER JOIN Libreria L ON C.Id_Cuenta = L.Id_Cuenta ";
		$Select .=" INNER JOIN HTOP H on H.Id_Libreria = L.Id_Libreria ";
		$Select .=" WHERE C.Id_Cuenta = $Id_Cuenta AND Visto = 1  ";
		$Resultado = @mysql_query($Select, $Conexion);
		$Numero_Elementos = mysql_fetch_array($Resultado);// @mysql_num_rows($Resultado);
		if($Numero_Elementos){
			echo json_encode(array("Numero_Procesos_Listos" => $Numero_Elementos[0]));
		}
		
}
function Reiniciar_PIPE()
{
	session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Id_HTOP = $_POST['Id_HTOP'];
	$Id_Libreria = $_POST['Id_Libreria'];
	$Ruta_Relativa = "./../Users/Usuario$Id_Cuenta/$Id_Libreria";
	$Conexion = Abrir_Conexion();
	mysql_query("UPDATE HTOP SET Numero_Pipe_Actual = 1, Mostrar_Reporte = 0, Activo = 0 WHERE Id_HTOP = $Id_HTOP" , $Conexion);
	mysql_query("UPDATE PIPE SET Stdout = '' WHERE Id_HTOP = $Id_HTOP" , $Conexion);
	mysql_query("DELETE LC.* FROM Lista_Comandos LC inner join PIPE P on P.Id_PIPE = LC.Id_PIPE WHERE P.Id_HTOP = $Id_HTOP" , $Conexion);
	$Resultado = mysql_query("SELECT * FROM Historial_Libreria WHERE Id_Libreria = $Id_Libreria " , $Conexion);
	$row = mysql_fetch_assoc($Resultado);
	if(is_dir("$Ruta_Relativa/JBrowse"))
	Eliminar_Datos_Bowtie($Id_Libreria, $Conexion);
	shell_exec("cp ".$row['Archivo']."  $Ruta_Relativa");
	Cerrar_Conexion($Conexion);
}
function Consultar_Flujo()
{
	$Id_HTOP = $_POST['Id_HTOP'];
	$Conexion = Abrir_Conexion();
	$Resultado = mysql_query("SELECT * FROM PIPE P INNER JOIN HTOP H ON P.Id_HTOP = H.Id_HTOP INNER JOIN Libreria L on L.Id_Libreria = H.Id_Libreria WHERE P.Id_HTOP = ".$Id_HTOP, $Conexion);
	$Consulta = array();
	while($row = mysql_fetch_assoc($Resultado))
	{
		$row['Stdout']=htmlspecialchars($row['Stdout']);
		$row['Stdout']=str_replace("\n","<br>",$row['Stdout']);
		$row['Stdout']=addslashes($row['Stdout']);
		$Consulta[] = $row;
	}
	echo json_encode($Consulta);
	Cerrar_Conexion($Conexion);
}
function Descargar_Archivo()
{
	$Archivo=$_POST['Archivo'];
	$Archivo_zip=$_POST['Archivo'].".zip";
	
	shell_exec(" zip -j $Archivo_zip $Archivo");
	echo json_encode(array('Archivo_Comprimido' => "Comodin/".$Archivo_zip));
	
}
function Actualizar_Enviar_Correo()
{
	$Conexion=Abrir_Conexion();
	mysql_query("UPDATE HTOP SET Enviar_Email = ".$_POST['Enviar']." WHERE Id_HTOP = ".$_POST['Id_HTOP'],$Conexion);
	Cerrar_Conexion($Conexion);
}
function Descargar_Archivo_Query()
{
	$Conexion=Abrir_Conexion();
	
	$Resultado=mysql_query("SELECT * FROM Query WHERE Id_Query = '".$_POST['Id_Query']."'",$Conexion);
	Cerrar_Conexion($Conexion);
	$row=mysql_fetch_assoc($Resultado);
	$Archivo=$row['Archivo'];
	
	echo json_encode(array('Comprimido' => $Archivo.".zip"));
}
function Finalizar_Procesos()
{
	session_start();
	$Conexion=Abrir_Conexion();
	mysql_query("delete LC.* from Lista_Comandos LC inner join PIPE P on LC.Id_PIPE = P.Id_PIPE where  P.Id_HTOP =".$_SESSION['Id_HTOP'],$Conexion);
	mysql_query("delete from PIPE where Id_HTOP =".$_SESSION['Id_HTOP'],$Conexion);
	mysql_query("delete from HTOP where Id_HTOP =".$_SESSION['Id_HTOP'],$Conexion);
	Cerrar_Conexion($Conexion);
	echo json_encode(array('Libreria' => $_SESSION['Nombre_Libreria']));

}
function Cancelar_Consulta()
{
   $Conexion=Abrir_Conexion();
   $Resultado=mysql_query("SELECT * FROM Query WHERE Id_Query = ".$_POST['Id_Query'], $Conexion);
   mysql_query("DELETE FROM Query WHERE Id_Query = ".$_POST['Id_Query'], $Conexion);
   $row=mysql_fetch_assoc($Resultado);
   
   shell_exec('./../Scripts/Query.sh "kill '.$row['PID'].'" ');
   Cerrar_Conexion($Conexion);	
}
function Proceso_Siguiente()
{
	session_start();
	//Pop_Proceso($_SESSION['Id_HTOP']);
	Mover_Puntero($_SESSION['Id_HTOP']);
}
function Limpiar_Directorio($Ruta_Relativa, $Ruta_Archivo)
{
   $ls=glob($Ruta_Relativa."/{*}",GLOB_BRACE);	
   foreach($ls as $File)
   {
	   if(basename($File)!= $Ruta_Archivo )
	      unlink($File);   
   }
}
function Agregar_Comando($Comando, $Id_HTOP, $Bool_STDOUT)
{
	
	//Consultar la información del paso que se va a ejecutar, 
	//para agregar los comandos
	$row=Primer_Elemento_Proceso($Id_HTOP);
	$Conexion=Abrir_Conexion();
	mysql_query("INSERT INTO Lista_Comandos VALUES(NULL, '$Comando', ".$row['Id_PIPE'].", $Bool_STDOUT )", $Conexion);
	Cerrar_Conexion($Conexion);
}
function Generar_Pipe($Id_HTOP, $Ruta, $Ruta_Archivo)
{
   //$Comando="\"$Ruta\" ".$Comando;
   //Convertir archivo de salidax
	
   		
   		//Renombrar el archivo de salida
		if(basename($Ruta_Archivo)=="File_CSFastaQ+")
		Agregar_Comando("mv ".$Ruta_Archivo." ".$Ruta."/File_CSFastaQ ", $Id_HTOP, 0);
		
    $Conexion=Abrir_Conexion();
		 if($_POST['Make_Gph'])
			 mysql_query("UPDATE HTOP SET Mostrar_Graficos = 1 WHERE Id_HTOP = $Id_HTOP ",$Conexion);
		 else
			 mysql_query("UPDATE HTOP SET Mostrar_Graficos = 0 WHERE Id_HTOP = $Id_HTOP ",$Conexion);
	  Cerrar_Conexion($Conexion);

	// $Comando=" ".str_replace('\"','\\\"',$row['Comando'])." ".$Comando;
   
	if($_POST['Origen']=="Ejecutar")
	{ 
	 shell_exec('php Administracion_Background.php '.$Id_HTOP.' >> /dev/null &');
	}

}
//Funciones que son parte del switch..
function Sesion_Datos($Id_HTOP)
{
   session_start();
   
   //$Resultado=mysql_query("select * from HTOP H inner join Libreria L on L.Id_Libreria=H.Id_Libreria inner join PIPE P on P.Id_HTOP= H.Id_HTOP where H.Id_HTOP = ".$Id_HTOP,$Conexion);
   $row=Primer_Elemento_Proceso($Id_HTOP);
   //$row=mysql_fetch_assoc($Resultado);
   $_SESSION['Id_HTOP']=$row['Id_HTOP'];
   $_SESSION['Ruta']=$row['Ruta']; 
   $_SESSION['Archivo']=Archivo($row['Ruta']);
   $_SESSION['Id_Libreria']=$row['Id_Libreria'];
   $_SESSION['Nombre_Libreria']=$row['Nombre'];
   $Conexion = Abrir_Conexion();
   mysql_query("UPDATE HTOP SET Ventana_Abierta = 0 WHERE Id_HTOP = ".$Id_HTOP ,$Conexion);
   Cerrar_Conexion($Conexion);
   echo json_encode($row);
}
function Archivo($Ruta_Relativa)
{
     $Directorio=ls($Ruta_Relativa);
   foreach($Directorio as $Archivo)
   {
       if(basename($Archivo)=="File_Fasta")
          return "File_Fasta";
       else if(basename($Archivo)=="File_FastaQ")
          return "File_FastaQ";
       else if(basename($Archivo)=="File_CSFastaQ")
          return "File_CSFastaQ";
       else if(basename($Archivo)=="File_Unknown")
         return "File_Unknown";
       else if(basename($Archivo)=="File_Qual" || basename($Archivo)=="File_CSFasta")
         return Check_Solid($Archivo,$Ruta_Relativa);
      else return false;
   }
}
function Check_Solid($Archivo1,$Ruta_Relativa)
{
    $Directorio=ls($Ruta_Relativa);
    foreach($Directorio as $Archivo2)
    {
       if(basename($Archivo1) == "File_Qual" && basename($Archivo2) == "File_CSFasta")
          return "File_CSFastaQ+";
	   if(basename($Archivo1) == "File_CSFasta" && basename($Archivo2) == "File_Qual")
          return "File_CSFastaQ+";
    }
   return false;
}
function ls($Ruta_Relativa)
{
   if(is_dir($Ruta_Relativa))
   return glob($Ruta_Relativa."/{*}",GLOB_BRACE);
   else return false;
}
function Cargar_Procesos()
{
   session_start();
   $Conexion=Abrir_Conexion();
   $Id_Cuenta=$_SESSION['Id_Cuenta'];
   $Resultado=mysql_query("select * from HTOP H inner join Libreria L on L.Id_Libreria = H.Id_Libreria WHERE L.Id_Cuenta = ".$_SESSION['Id_Cuenta'],$Conexion);
    Cerrar_Conexion($Conexion);
	$Consulta=array();
   while( $row=mysql_fetch_assoc($Resultado))
   {
	   $Paso=Primer_Elemento_Proceso($row['Id_HTOP']);
	   $row['Comando']=$Paso['Comando'];
	   $row['Script']=$Paso['Script'];
	   $Consulta[]=$row;
   }
   echo json_encode($Consulta);  
}
function Cargar_Procesos_MySQL()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Id_Cuenta=$_SESSION['Id_Cuenta'];
	$Consulta=array();
	$Resultado=mysql_query("SELECT * FROM Query WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	while( $row=mysql_fetch_assoc($Resultado))
	{
		$MySQL=mysql_query("SELECT TIME, STATE FROM information_schema.processlist WHERE Id = ".$row['PID'], $Conexion);
		$row2=mysql_fetch_assoc($MySQL);
		$row['TIME']=$row2['TIME'];
		$row['STATE']=$row2['STATE'];
		$Consulta[]=$row;
	}
	Cerrar_Conexion($Conexion);
	echo json_encode($Consulta);
}
function Eliminar_Proceso()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM HTOP H inner join Libreria L on H.Id_Libreria = L.Id_Libreria ",$Conexion);
	$row=mysql_fetch_assoc($Resultado);
	if(is_dir($row['Ruta']."/JBrowse"))
	Eliminar_Datos_Bowtie($row['Id_Libreria'], $Conexion);
	if(preg_match("/Users/", $row['Ruta']))
	shell_exec("rm -fr ".$row['Ruta']);
	//DELETE en cascada activado
	mysql_query("delete from Libreria where Id_Libreria =".$row['Id_Libreria'],$Conexion);
	
	Cerrar_Conexion($Conexion);
}
function Eliminar_Datos_Bowtie($Id_Libreria, $Conexion)
{
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Resultado=mysql_query("select * from Genoma",$Conexion);
	//Se borran lo archivo JBrowse que se encuentran en todos los genomas disponibles
	while($row=mysql_fetch_assoc($Resultado))
	{
		shell_exec("rm -f ./../Indexes/".$row['Id_Genoma']."/JBrowse/".$Id_Libreria."trackList.json");
		Borrar_Track("./../Indexes/".$row['Id_Genoma']."/JBrowse/Usuario".$Id_Cuenta."trackList.json", $Id_Libreria);
	}
	shell_exec('./../Scripts/Delete.sh "DELETE FROM Molecula'.$Id_Cuenta.' WHERE Id_Libreria = '.$Id_Libreria.';" > /dev/null 2>&1 &');
	
}
function Primer_Elemento_Proceso($Id_HTOP)
{
	$Conexion=Abrir_Conexion();
    $Resultado=mysql_query("SELECT * FROM HTOP WHERE Id_HTOP = $Id_HTOP",$Conexion);
	$row=mysql_fetch_assoc($Resultado);
	$Resultado=mysql_query("SELECT * FROM Libreria L inner join HTOP H on L.Id_Libreria=H.Id_Libreria inner join PIPE P on H.Id_HTOP = P.Id_HTOP WHERE H.Id_HTOP = $Id_HTOP order by Id_PIPE LIMIT ".($row['Numero_Pipe_Actual']-1).",1",$Conexion);
	$row=mysql_fetch_assoc($Resultado);
   Cerrar_Conexion($Conexion);
   return $row;
}
function Mover_Puntero($Id_HTOP)
{
	$Conexion=Abrir_Conexion();
	mysql_query("UPDATE HTOP SET Numero_Pipe_Actual= Numero_Pipe_Actual+1, Mostrar_Reporte = 0 WHERE Id_HTOP = $Id_HTOP",$Conexion);
	Cerrar_Conexion($Conexion);
	$row=Primer_Elemento_Proceso($Id_HTOP);
	echo json_encode($row);
	
}
function Borrar_HTOP($Id_HTOP)
{
   $Conexion=Abrir_Conexion();
   mysql_query("delete from HTOP where Id_HTOP = $Id_HTOP",$Conexion);
   Cerrar_Conexion($Conexion);

}	
?>
	
