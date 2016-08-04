<?php
include("Procesos_Derecho_Server.php");
switch($_REQUEST['Origen'])
{
   case "Ejecutar":
   Ordenar_Campos();
   break;
   case "Guardar":
   Ordenar_Campos();
   break;
}
function Ordenar_Campos()
{
   session_start();
   Limpiar_Directorio($_SESSION['Ruta'], $_SESSION['Archivo']);
   $Comando.="fastq_masker ";
   if($_POST['Option_q'])$Comando.="-q ".$_POST['Option_q']." ";
   if($_POST['Option_r'])$Comando.="-r ".$_POST['Option_r']." ";
   if($_POST['Option_Q'])$Comando.="-Q".$_POST['Option_Q']." ";
    //Ficheros de entreda y salida
   $Ruta_Archivo=$_SESSION['Ruta']."/".$_SESSION['Archivo'];
   $Comando.="-i ".$Ruta_Archivo." -o ".$Ruta_Archivo."tmp -v ";
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 1);
   $Comando="mv ".$Ruta_Archivo."tmp ".$Ruta_Archivo;
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0); 
   Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);

      
}
?>
