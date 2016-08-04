
<?php

include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Insert":
Insert();
break;
case "Select":
Consulta();
break;	
case "Delete":
Baja();
break;
case "Update":
Actualizar();
break;
}
function Insert()
{
	$Conexion=Abrir_Conexion();
	$usuario=$_POST['User'];
	$pass=$_POST['Pass'];
	$values="null,"."'$usuario','$pass','".$_POST['Permission']."','".$_POST['Name']."','".$_POST['LastName']."','".$_POST['Email']."',1";
	$q = mysql_query("insert into Cuenta values($values)",$Conexion);
	if(preg_match("/Duplicate/", mysql_error()))
	{
		echo "Username is already in use"; 
	}
	else
	{
		JBrowse_data($Conexion);
	}
	//shell_exec(mysql_error());
	Cerrar_Conexion($Conexion);
}
function Consulta()
{
	$Conexion=Abrir_Conexion();

$q = mysql_query("SELECT * FROM Cuenta ",$Conexion);
echo '<table id="Tabla_Cuentas" style="text-align:center; vertical-align:middle;" cellpadding="0" border="0" class="table display">
<thead>
<tr>
<th>Username</th>
<th>Pass</th>
<th>Permission</th>
<th>Name</th>
<th>LastNames</th>
<th>Email</th>
<th>Confirm	</th>
<th></th>
</tr>
</thead>
<tbody>
';
 while($row =mysql_fetch_assoc($q))
  {
	  $confirm="";
	  if($row['Confirmacion']){$confirm="Yes";}else{$confirm="No";}
		echo '<tr id="hidetr'.$row['Cuenta'].'">';
		echo '<td style="cursor:pointer;" id="'.$row['Cuenta'].'Cuenta" onclick="UpdateT(\''.$row['Cuenta'].'\',\'Cuenta\')">' . $row['Cuenta'] . '</td>';
		echo '<td style="cursor:pointer;" id="'.$row['Cuenta'].'Pass" onclick="UpdateT(\''.$row['Cuenta'].'\',\'Pass\')">' . $row['Pass'] . "</td>";
		echo '<td style="cursor:pointer;" id="'.$row['Cuenta'].'Tipo_Cuenta" onclick="UpdateT(\''.$row['Cuenta'].'\',\'Tipo_Cuenta\')">' . $row['Tipo_Cuenta'] . "</td>";
		echo '<td style="cursor:pointer;" id="'.$row['Cuenta'].'Nombre" onclick="UpdateT(\''.$row['Cuenta'].'\',\'Nombre\')">' . $row['Nombre'] . "</td>";
		echo '<td style="cursor:pointer;" id="'.$row['Cuenta'].'Apellido" onclick="UpdateT(\''.$row['Cuenta'].'\',\'Apellido\')">' . $row['Apellido'] . "</td>";
		echo '<td style="cursor:pointer;" id="'.$row['Cuenta'].'Email" onclick="UpdateT(\''.$row['Cuenta'].'\',\'Email\')">' . $row['Email'] . "</td>";
		echo '<td style="cursor:pointer;" id="'.$row['Cuenta'].'Confirmacion" onclick="UpdateT(\''.$row['Cuenta'].'\',\'Confirmacion\')">' . $confirm . "</td>";
		echo '<td><input type="button" class="btn btn-danger" value="Delete" Id_Cuenta="'.$row['Id_Cuenta'].'" onclick="DeleteA(this)"></td>';
		echo "</tr>";
  }
echo "
</tbody>
</table>";
Cerrar_Conexion($Conexion);
}
function Baja()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Id_Cuenta=$_POST['Id_Cuenta'];
	shell_exec("rm -fr ./../Users/Usuario$Id_Cuenta");
	//Eliminar todas las tablas dependientes de la tabla Cuenta, registros secundarios
	$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	while($Libreria=mysql_fetch_assoc($Resultado))
	{
		$Resultado2=mysql_query("SELECT * FROM HTOP WHERE Id_Libreria = ".$Libreria['Id_Libreria'], $Conexion);
		while($HTOP=mysql_fetch_assoc($Resultado2))
		{
			$Resultado3=mysql_query("SELECT * FROM Lista_Comandos WHERE Id_PIPE = ".$HTOP['Id_PIPE'], $Conexion);
			while($PIPE=mysql_fetch_assoc($Resultado3))
			{
				mysql_query("DELETE FROM Lista_Comandos WHERE Id_PIPE = ".$PIPE['Id_PIPE'], $Conexion);	
			}
			mysql_query("DELETE FROM PIPE WHERE Id_HTOP = ".$HTOP['Id_HTOP'], $Conexion);
			
		}
		mysql_query("DELETE FROM HTOP WHERE Id_Libreria = ".$HTOP['Id_Libreria'], $Conexion);
		
		mysql_query("DELETE FROM Grupo_Libreria WHERE Libreria_Id_Libreria = ".$HTOP['Id_Libreria'], $Conexion);
	}
	
	//Elminar archivos JBrowse dependientes
	$resultado = mysql_query("select * from Genoma",$Conexion);
	while($row=mysql_fetch_assoc($resultado))
	{
		shell_exec("rm -f ./../Indexes/".$row["Id_Genoma"]."/JBrowse/Usuario".$Id_Cuenta."trackList.json");
		
		$Eliminar_Libreria = mysql_query("select L.Id_Libreria from Cuenta C inner join Libreria L on L.Id_Cuenta = C.Id_Cuenta where C.Id_Cuenta = '$Id_Cuenta'",$Conexion);
			while($row2=mysql_fetch_assoc($Eliminar_Libreria))
		{
			shell_exec("rm -f ./../Indexes/".$row["Id_Genoma"]."/JBrowse/".$row2['Id_Libreria']."trackList.json");
		}
	}
	
	//Eliminar registros primarios
	
	mysql_query("DELETE FROM Miembro_Grupo WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	
	mysql_query("DELETE FROM Libreria WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	
	mysql_query("DELETE FROM Grupo WHERE Id_Cuenta_Propietario = $Id_Cuenta", $Conexion);
	
	mysql_query("DELETE FROM Query WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	
	mysql_query("DELETE FROM Cuenta WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	
	shell_exec('./../Scripts/Delete.sh "DROP table Molecula'.$Id_Cuenta.'; " &');
	Cerrar_Conexion($Conexion);
}
function Actualizar()
{
	$conexion=Abrir_Conexion();
	$row=$_POST['Row'];
	$column=$_POST['Column'];
	$data=$_POST['Data'];
	mysql_query("UPDATE Cuenta SET $column = '$data' WHERE Cuenta LIKE '$row'",$conexion);
	
	echo "Error: ".mysql_error();
	Cerrar_Conexion($conexion);
}
function JBrowse_data($Conexion)
{	
		$Id=mysql_insert_id();
		$Usuario="Usuario$Id";
        mysql_query("create table Molecula$Id like MoleculaN",$Conexion);
        shell_exec("mkdir ./../Users/$Usuario");
 	   
	    shell_exec("mkdir ./../Users/$Usuario/JBrowse");
        shell_exec("cp -r ./../JBrowse/src ./../Users/$Usuario/JBrowse/.");
	    shell_exec("cp -r ./../JBrowse/img ./../Users/$Usuario/JBrowse/.");
	    shell_exec("cp ./../JBrowse/jbrowse_conf.json ./../Users/$Usuario/JBrowse/.");
	 $Resultado = mysql_query("SELECT * FROM Genoma",$Conexion);
	  while( $row =mysql_fetch_assoc($Resultado))
        {
			$Id_Genoma=$row['Id_Genoma'];
			$Ruta="./../Indexes/".$Id_Genoma."/JBrowse";
			shell_exec("cp $Ruta/trackList.json $Ruta/".$Usuario."trackList.json");
			shell_exec("./../Scripts/Import_JBrowse_User.sh $Usuario $Ruta $Id_Genoma");
			shell_exec("chmod 777 -Rf $Ruta");
	    }
	    shell_exec("chmod 777 -R ./../Users/$Usuario");

}
?>
