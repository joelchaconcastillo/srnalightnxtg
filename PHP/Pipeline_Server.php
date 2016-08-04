<?php 
include("MySQL.php");
switch($_REQUEST['Origen'])
{
   case "Registrar_Pipe":
   Registrar_Pipe();
   break;
}
function Registrar_Pipe()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Pipe=$_POST['Pipe'];
        $Cont=0;
	$Numero_Pipes=count($Pipe);
	mysql_query("INSERT INTO HTOP VALUES(DEFAULT,-1,0,1,$Numero_Pipes,".$_SESSION['Id_Cuenta'].",".$_SESSION['Id_Libreria'].",'')",$Conexion);
	$Id_HTOP=mysql_insert_id();
	foreach($Pipe as $Script)
	{
                if($Cont>=2 && $Cont < $Numero_Pipes) mysql_query("INSERT INTO PIPE VALUE(DEFAULT,'Reporte.html','No set',$Id_HTOP)",$Conexion);
		mysql_query("INSERT INTO PIPE VALUE(DEFAULT,'$Script','No set',$Id_HTOP)",$Conexion);
	        $Cont++;
        }
	Cerrar_Conexion($Conexion);
	echo json_encode(array("Id_HTOP" => $Id_HTOP));
}

?>
