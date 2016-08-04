<?php
include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Genomas":
Genomas();
break;	
}
function Genomas()
{
        session_start();
	$Conexion=Abrir_Conexion();
	$q = mysql_query("select Id_Cuenta from Cuenta where Id_Cuenta = ".$_SESSION['Id_Cuenta'] ,$Conexion);
	$row =mysql_fetch_assoc($q);
  	$Usuario="Usuario".$row['Id_Cuenta'];
	$Resultado=mysql_query("SELECT * FROM Genoma",$Conexion);	
	echo '<option value="" disabled="disabled" id="dropdown" required selected="selected">Please select a Genome</option>';
	while($row=mysql_fetch_assoc($Resultado))
	{ 
		$q = mysql_query("SELECT * FROM Organismo O inner join Genoma G on G.Id_Organismo = O.Id_Organismo WHERE Id_Genoma = ".$row['Id_Genoma'] ,$Conexion);
		$row =mysql_fetch_assoc($q);
		echo '<option value="./Users/'.$Usuario.'/JBrowse/'.$row['Id_Genoma'].'index.html"  id="dropdown" >'.$row['Organismo']." (".$row['Nombre']."_".$row['Version'].")".'</option>';
	}	
	Cerrar_Conexion($Conexion);
}
?>
