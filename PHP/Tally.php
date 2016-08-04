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
   $Comando=Tally($Ruta_Archivo, $_SESSION['Ruta']);
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 1);
   $Comando="mv ".$Ruta_Archivo."tmp ".$Ruta_Archivo;
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
   Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);

      
}
function Tally($Ruta_Archivo, $Ruta)
{
	$Comando="./../bin/tally -i ".$Ruta_Archivo." ";
	if(!empty($_POST['Option_With_Quality']))$Comando.="--with-quality  ";
	if(!empty($_POST['Option_l']))$Comando.="-l ".$_POST['Option_l']." ";
	if(!empty($_POST['Option_u']))$Comando.="-u ".$_POST['Option_u']." ";
	if(!empty($_POST['Option_tri']))$Comando.="-tri ".$_POST['Option_tri']." ";
	if(!empty($_POST['Option_si']))$Comando.="-si ".$_POST['Option_si']." ";
        if(!empty($_POST['Option_dsi']))$Comando.="-dsi ".$_POST['Option_dsi']." ";
	if(!empty($_POST['Option_No_Tally']))$Comando.="--no-tally  ";
        if(basename($Ruta_Archivo)=="File_Fasta")$Comando.="--fasta-in --fasta-out ";
	$Comando.="-o ".$Ruta_Archivo."tmp --nozip ";		
        	
	return $Comando;
	
}
?>
