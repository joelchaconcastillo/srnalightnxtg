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
   $Comando.="fastx_clipper ";
   if($_POST['Option_a'])$Comando.="-a ".$_POST['Option_a']." ";
   if($_POST['Option_l'])$Comando.="-l ".$_POST['Option_l']." ";
   if($_POST['Option_d'])$Comando.="-d ".$_POST['Option_d']." ";
   if($_POST['Option_c'])$Comando.="-c ";
   if($_POST['Option_C'])$Comando.="-C ";
   if($_POST['Option_k'])$Comando.="-k ";
   if($_POST['Option_n'])$Comando.="-n ";
   if($_POST['Option_M'])$Comando.="-M ".$_POST['Option_M']." ";

    //Ficheros de entreda y salida
   $Ruta_Archivo=$_SESSION['Ruta']."/".$_SESSION['Archivo'];
   $Comando.="-i ".$Ruta_Archivo." -o ".$Ruta_Archivo."tmp -v ";
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], "1");
   
   
    $Comando="mv ".$Ruta_Archivo."tmp ".$Ruta_Archivo;
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0); 
   Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);

      
}
?>
