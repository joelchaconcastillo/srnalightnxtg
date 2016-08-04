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
	$Ruta_Archivo=$_SESSION['Ruta']."/".$_SESSION['Archivo'];
	Limpiar_Directorio($_SESSION['Ruta'], $_SESSION['Archivo']);
	$Comando="fastx_trimmer ";
	if($_POST['Option_f'])$Comando.="-f ".$_POST['Option_f']." ";
	if($_POST['Option_l'])$Comando.="-l ".$_POST['Option_l']." ";
	if($_POST['Option_t'])$Comando.="-t ".$_POST['Option_t']." ";
	if($_POST['Option_m'])$Comando.="-m ".$_POST['Option_m']." ";
	$Comando.="-i ".$Ruta_Archivo." -o ".$Ruta_Archivo."tmp -v ";
	Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 1);
	$Comando="mv ".$Ruta_Archivo."tmp ".$Ruta_Archivo;
	Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);

	Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);

}
?>
