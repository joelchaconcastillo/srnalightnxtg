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
   $Ruta_Archivo=$_SESSION['Ruta']."/".$_SESSION['Archivo'];
   $Comando=Minion($Ruta_Archivo, $_SESSION['Ruta']);
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 1); 
   Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);     
}
function Minion($Ruta_Archivo, $Ruta)
{
   $Comando.="./../bin/minion search-adapter -i $Ruta_Archivo ";
   if($_POST['Option_write_fasta'])$Comando.="-write-fasta $Ruta/Adapters.fa ";
   if($_POST['Option_show'])$Comando.="-show ".$_POST['Option_show']." ";
   if($_POST['Option_do'])$Comando.="-do ".$_POST['Option_do']." ";
   if($_POST['Option_adapter'])$Comando.="-adapter ".$_POST['Option_adapter']." ";
   return $Comando;
	   
}
?>
