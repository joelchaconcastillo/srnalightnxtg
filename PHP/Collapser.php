<?php 
include("Procesos_Derecho_Server.php");
switch($_REQUEST["Origen"])
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
   //Ficheros de entreda y salida
   $Ruta_Archivo=$_SESSION['Ruta']."/".$_SESSION['Archivo'];
   $Comando="fastx_collapser ";
   //Procesamiento con Collapser
   $Comando.="-i ".$Ruta_Archivo." -o ".$Ruta_Archivo."tmp -v ";
    Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 1);

   $Comando="rm ".$Ruta_Archivo;
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
   
    $Comando="mv ".$Ruta_Archivo."tmp ".$_SESSION['Ruta']."/File_Fasta ";
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
   
   $Ruta_Archivo=$_SESSION['Ruta']."/File_Fasta";
   $_SESSION['Archivo']="File_Fasta";
   Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);  
   
}

?>
