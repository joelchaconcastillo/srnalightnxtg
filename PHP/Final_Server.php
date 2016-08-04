<?php
include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Borrar":
Borrar_Archivo();
break;
case "Genomas":
Genomas();
break;
case "Id_Genoma":
Id_Genoma();
break;
}
function Id_Genoma()
{
	$Conexion=Abrir_Conexion();
	$Ruta=$_POST['Genoma'];
	$Split=explode("_",basename($Ruta));
        		$Version=end($Split);
				$Genoma=substr(basename($Ruta),0,-1*(strlen($Version)+1));
	$Resultado = mysql_query("select * from Genoma where Nombre='$Genoma' and Version = '$Version';" ,$Conexion);
				$row =mysql_fetch_assoc($Resultado);
				echo $row['Id_Genoma'];
	Cerrar_Conexion($Conexion);
}
function Borrar_Archivo()
{
 unlink($_POST['Archivo']);	
 $array=glob($_POST['Path']."/{*}",GLOB_BRACE);
 if(count($array)==0)
 {	
	  echo '<script> Esconder_Upload();</script>';
 }
}
function Genomas()
{
$conexion=Abrir_Conexion();
$array=glob("Indexes/{*}",GLOB_BRACE);
echo '<option value="" disabled="disabled" id="dropdown" required selected="selected">Please select a Genome</option>';

			foreach($array as $line)
			{
				$Split=explode("_",basename($line));
        		$Version=end($Split);
				$Genoma=substr(basename($line),0,-1*(strlen($Version)+1));
				$q = mysql_query("select * from Organismo O inner join Genoma G on G.Id_Organismo = O.Id_Organismo where G.Nombre= '".$Genoma."' and Version='".$Version."';" ,$conexion);
				$row =mysql_fetch_assoc($q);
				echo '<option value="'.$line.'"  id="dropdown" >'.$row['Organismo']." (".basename($line).")".'</option>';
                        
			}
 Cerrar_Conexion($conexion);
}
?>
