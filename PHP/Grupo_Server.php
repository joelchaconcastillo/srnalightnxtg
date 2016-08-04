<?php

include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Modificar_Grupos":
MGrupos();
break;
case "Contenido_Grupo":
Contenido_Grupo();
break;
case "Nuevo_Miembro_Grupo":
Nuevo_Miembro_Grupo();
break;
case "Borrar_Grupo":
Borrar_Grupo();
break;
case "Borrar_Libreria_Grupo":
Borrar_Libreria_Grupo();
break;
case "Drop_Member":
Drop_Member();
break;
case "Consulta_Libreria_Grupo":
Consulta_Libreria_Grupo();
break;
case "Libreria_Grupos":
Libreria_Grupos();
break;
case "Append_Library":
Append_Library();
break;
case "Nuevo_Grupo":
Nuevo_Grupo();
break;
case "Descargar":
Descargar();
break;
case "Administrador":
Administrador();
break;
case "Mostrar_Miembros":
Mostrar_Miembros();
break;
case "Invitacion":
Enviar_Invitacion();
break;
case "Autocompletar":
Autocompletar();
break;
}
function Autocompletar()
{
		$conexion=Abrir_Conexion();
		$cad=$_REQUEST['term'];
		
			session_start();
			$Id_Cuenta = $_SESSION['Id_Cuenta'];
			$Query="SELECT * FROM Cuenta WHERE Id_Cuenta != $Id_Cuenta AND Cuenta LIKE '%{$cad}%' AND Tipo_Cuenta LIKE 'User' group by Cuenta";
		
		$q = mysql_query($Query,$conexion);

	if(mysql_num_rows($q)<200 && mysql_num_rows($q)>0 )
	{
		while($row =mysql_fetch_assoc($q))
		{
			   $results[] = array( 
			'id' => $row['Id_Cuenta']
			, 'label' => $row['Cuenta'].' ('.$row['Nombre'].' '.$row['Apellido'].')'
			, 'value' => $row['Cuenta']
				);
		}
	}
	else if(mysql_num_rows($q)==0)
	{
		//$results[]="There are not elements";
	}
	else{$results[]="There are more than 200 elements";}
	 if(!empty($results))
	 {
	echo json_encode($results);
	 }
	Cerrar_Conexion($conexion);
}

function Enviar_Invitacion()
{
	session_start();
    $Conexion=Abrir_Conexion();
	
        $q = mysql_query("SELECT * FROM Cuenta WHERE  Id_Cuenta= ".$_SESSION['Id_Cuenta'],$Conexion);
	$row =mysql_fetch_assoc($q);
        
		$Trash=basename($_POST['URL']);
    $URL=substr($_POST['URL'],0,-1*(strlen($Trash)+1));
		$cabeceras = 'From: TransciptomeGene@chacon.com' . "\r\n" ;
        $mensaje = $row['Nombre'].' '.$row['Apellidos'].' has invited you to use the plataform for transcriptomes "sRNA"\nPlease check the next link and register you account: '.$URL.'/Register_Invitation.php?Id_Grupo='.base64_encode($_POST['Id_Grupo']);
        mail($_POST['Email'],'Invitation',$mensaje,$cabeceras);
		Cerrar_Conexion($Conexion);

	
}
function Administrador()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Id_Grupo=$_POST['Id_Grupo'];
	$Id_Usuario=$_SESSION['Id_Cuenta'];
	$Resultado= mysql_query("SELECT * FROM Grupo WHERE Id_Cuenta_Propietario = $Id_Usuario AND Id_Grupo= $Id_Grupo ;",$Conexion);
	echo mysql_num_rows($Resultado);
	Cerrar_Conexion($Conexion);
}
function Descargar()
{
	$Conexion=Abrir_Conexion();
	$Resultado = mysql_query("select Id_Cuenta from Libreria where Id_Libreria = ".$_POST['Id_Libreria']);
    $row =mysql_fetch_assoc($Resultado);
	Cerrar_Conexion($Conexion);	
	$Query="select Id_MoleculaN , Chr, Inicio, Fin, Sentido, Missmatches, Numero_Moleculas, Tipo, Id_Biologico, Secuencia from Molecula".$row['Id_Cuenta']." where Id_Libreria = ".$_POST['Id_Libreria'];
	Consulta_Bash($Query);
}
function Nuevo_Grupo()
{
	session_start();
 $Conexion=Abrir_Conexion();
 $q = mysql_query("insert into Grupo values(NULL,'".$_POST['Nombre_Grupo']."','".$_POST['Descripcion']."','".date('Y-m-d H:i:s')."',".$_SESSION['Id_Cuenta'].")",$Conexion);
//shell_exec(mysql_error());
//$Id_grp=mysql_insert_id();
//$q = mysql_query("insert into Libreria_Grupo values('".$Id_grp."','".$_POST['opc']."')",$Conexion);
//$row =mysql_fetch_assoc($q);
 Cerrar_Conexion($Conexion);	
}

function Append_Library()
{
	$Id_Grupo=$_POST['Id_Grupo'];
	$Id_Libreria=$_POST['Id_Libreria'];
	//$Descripcion=$_POST['Descripcion_Libreria'];
	$Conexion=Abrir_Conexion();
	mysql_query("insert into Grupo_Libreria values($Id_Grupo,$Id_Libreria)",$Conexion);
	echo $Id_Grupo." ".$Id_Libreria;
	
	Cerrar_Conexion($Conexion);
}
function Libreria_Grupos()
{
	session_start();
	$Conexion=Abrir_Conexion();
$q = mysql_query("select * from Libreria where Libreria.Id_Cuenta = ".$_SESSION['Id_Cuenta'],$Conexion);

while($row =mysql_fetch_assoc($q))
  {
echo '<option value="'.$row['Id_Libreria'].'">'.$row['Nombre'].'</option>'; 
  }
	Cerrar_Conexion($Conexion);
}
function Consulta_Libreria_Grupo()
{
	session_start();
	$Id=$_POST['Id_Grupo'];
	$Conexion=Abrir_Conexion();
echo '<div class="panel panel-default" style="margin:10px;">
  <div class="panel-body" style="overflow:auto;">';
$q = mysql_query("SELECT L.* FROM Libreria L inner join Grupo_Libreria LG on L.Id_Libreria= LG.Libreria_Id_Libreria WHERE Grupo_Id_Grupo = $Id and L.Id_Cuenta = ".$_SESSION['Id_Cuenta'],$Conexion);
echo "<table id='Tabla_Librerias_Grupo'style='text-align:center; vertical-align:middle;'  cellspacing='0' border='0' class='table display'  >
<thead>
	<tr>
		<th>Owner</th>
		<th>Library</th>
		<th>KeyWords</th>
		<th>Tissue</th>
		<th>Sequence platform</th>
		<th>Protocol</th>
		<th>Visualize</th>
		<th>Text Browser	</th>
		<th>Database</th>
		<th></th>
	</tr>
</thead>
	<tbody>
	";
	 while($row =mysql_fetch_assoc($q))
	  {
		echo '<tr id="hidetr'.$row['Id_Libreria'].'">';

		echo '<td id="'.$row['Id_Libreria'].'Propietario" >' . Propietario($row['Id_Libreria']) . '</td>';
		echo '<td id="'.$row['Id_Libreria'].'Nombre_Libreria" >' . $row['Nombre'] . '</td>';
		echo '<td id="'.$row['Id_Libreria'].'Palabras_Clave" >' . $row['Palabras_Clave'] . "</td>";
		echo '<td id="'.$row['Id_Libreria'].'Tejido" >' . $row['Tejido'] . "</td>";
		echo '<td id="'.$row['Id_Libreria'].'Plataforma" >' . $row['Plataforma'] . "</td>";
		echo '<td id="'.$row['Id_Libreria'].'Descripcion" >' . $row['Descripcion'] . "</td>";
		echo '<td id="'.$row['Id_Libreria'].'Tipo_Libreria">
		<div onclick=JBrowse("./Comodin/'.$row['Ruta'].'/JBrowse/index.html") class="btn btn-primary" data-toggle="modal" data-target="#Modal_Contenedor_JBrowse">
		JBrowse
		</div>
		</td>';
		echo '<td ><div id="'.$row['Id_Libreria'].'Tipo_Libreria" class="btn btn-default" Id_Libreria="'.$row['Id_Libreria'].'" onclick="Mostrar_Ventana(this)">Text Browser</div></td>';
		echo '<td id="'.$row['Id_Libreria'].'Tipo_Libreria" >

		<div class="btn btn-success" Id_Libreria="'.$row['Id_Libreria'].'" onclick="Descargar(this)">Download</div>
		</td>';
		echo '<td ><div class="btn btn-danger" id="'.$row['Id_Libreria'].'" onclick="Delete_Library('.$row['Id_Libreria'].')">  Delete  </div></td>';
		echo "</tr>";
		}
		//Consulta para usuarios
		$q = mysql_query("SELECT L.* FROM Libreria L inner join Grupo_Libreria LG on L.Id_Libreria= LG.Libreria_Id_Libreria WHERE Grupo_Id_Grupo = $Id and L.Id_Cuenta != ".$_SESSION['Id_Cuenta'],$Conexion);

		while($row =mysql_fetch_assoc($q))
		{
		echo '<tr id="hidetr'.$row['Id_Libreria'].'">';
		echo '<td id="'.$row['Id_Libreria'].'Propietario" >' . Propietario($row['Id_Libreria']) . '</td>';
		echo '<td id="'.$row['Id_Libreria'].'Nombre_Libreria" >' . $row['Nombre'] . '</td>';
		echo '<td id="'.$row['Id_Libreria'].'Palabras_Clave" >' . $row['Palabras_Clave'] . "</td>";
		echo '<td id="'.$row['Id_Libreria'].'Tejido" >' . $row['Tejido'] . "</td>";
		echo '<td id="'.$row['Id_Libreria'].'Plataforma" >' . $row['Plataforma'] . "</td>";
		echo '<td id="'.$row['Id_Libreria'].'Descripcion" >' . $row['Descripcion'] . "</td>";
		echo '<td id="'.$row['Id_Libreria'].'Tipo_Libreria"> 
		<div class="btn btn-primary" onclick=JBrowse("./Comodin/'.$row['Ruta'].'/JBrowse/index.html") class="btn btn-primary btn-lg" data-toggle="modal" data-target="#Modal_Contenedor_JBrowse">
		JBrowse
		</div>
		</td>';
		  echo '<td ><div id="'.$row['Id_Libreria'].'Tipo_Libreria" class="btn btn-default" Id_Libreria="'.$row['Id_Libreria'].'" onclick="Mostrar_Ventana(this)">Text Browser</div></td>';

		echo '<td id="'.$row['Id_Libreria'].'Tipo_Libreria"> 

		<div class="btn btn-success" Id_Libreria="'.$row['Id_Libreria'].'" onclick="Descargar(this)"> 
		Download
		</div>

		</td>';
		echo '<td id="'.$row['Id_Libreria'].'">
		<!--<div class="btn btn-danger" onclick="DeleteA(this)" class="Delete">
		Delete  
		</div>-->
		</td>';
		echo "</tr>";
	  }
	echo "
	</tbody>
</table>
</div>
</div>
";

Cerrar_Conexion($Conexion);
}
function Propietario($Id_Libreria)
{
$Conexion=Abrir_Conexion();
$Resultado=mysql_query(" select C.Cuenta from Cuenta C inner join Libreria L on L.Id_Cuenta=C.Id_Cuenta where L.Id_Libreria= $Id_Libreria;",$Conexion);
$row=mysql_fetch_assoc($Resultado);
return $row['Cuenta'];
Cerrar_Conexion($Conexion);	
}
function Drop_Member()
{
	$Conexion=Abrir_Conexion();
	mysql_query("delete from Miembro_Grupo where Id_Cuenta=".$_POST['Id_Cuenta']." and Id_Grupo = ".$_POST['Id_Grupo'],$Conexion);
	Cerrar_Conexion($Conexion);
}
function Borrar_Libreria_Grupo()
{
	$Conexion=Abrir_Conexion();
	mysql_query("delete from Grupo_Libreria where Libreria_Id_Libreria= ".$_POST['Id']." and Grupo_Id_Grupo = ".$_POST['Grupo'],$Conexion);
	Cerrar_Conexion($Conexion);
}
function Borrar_Grupo()
{
	$Conexion=Abrir_Conexion();
	mysql_query("delete from Miembro_Grupo where Id_Grupo= ".$_POST['Id'],$Conexion);
	mysql_query("delete from Grupo_Libreria where Grupo_Id_Grupo= ".$_POST['Id'],$Conexion);
	mysql_query("delete from Grupo where Id_Grupo= ".$_POST['Id'],$Conexion);
	Cerrar_Conexion($Conexion);
}
function Nuevo_Miembro_Grupo()
{
	session_start();
	$Conexion=Abrir_Conexion();
		
	 mysql_query("insert into Miembro_Grupo values(".$_POST['Id_Grupo'].",".$_POST['Id_Cuenta'].");",$Conexion);
	Cerrar_Conexion($Conexion);	
}
function Contenido_Grupo()
{
	session_start();
	$Conexion=Abrir_Conexion();
	
	$q = mysql_query("SELECT C.* FROM Cuenta C inner join Miembro_Grupo M on C.Id_Cuenta = M.Id_Cuenta where M.Id_Grupo =".$_POST['Id_Grupo'],$Conexion);
	while($row =mysql_fetch_assoc($q))
  {
	  echo "<div class='Names' id='User".$row['Id_Cuenta']."'><script>Balloon_Help('Information".$row['Id_Cuenta']."','Name: ".$row['Nombre']." ".$row['Apellido']."<br>Email: ".$row['Email']." <br> Nickname: ".$row['Cuenta']."'); </script><span id='Information".$row['Id_Cuenta']."' class='glyphicon glyphicon-info-sign'></span><div  style='position:relative; float:right; cursor:pointer' class='glyphicon glyphicon-remove-sign' Onclick=Drop_Member('".$row['Id_Cuenta']."')></div> ".$row['Cuenta']."</div>";
  }
  
	/*echo '<div class="Names"><div class="Close" Onclick=Drop_Member(\''.$row['Id_Cuenta'].'\')>x</div>'.$_POST['Id_Grupo'].' <br>asas</div>';*/
	Cerrar_Conexion($Conexion);	
}
function Mostrar_Miembros()
{
	session_start();
	$Conexion=Abrir_Conexion();
	
	$q = mysql_query("SELECT C.* FROM Cuenta C inner join Miembro_Grupo M on C.Id_Cuenta = M.Id_Cuenta where M.Id_Grupo =".$_POST['Id_Grupo'],$Conexion);
	while($row =mysql_fetch_assoc($q))
  {
	  echo "<div class='Names' id='".$row['Id_Cuenta']."'><script>Balloon_Help('VistaInformation".$row['Id_Cuenta']."','Name: ".$row['Nombre']." ".$row['Apellido']."<br>Email: ".$row['Email']." <br> Nickname: ".$row['Cuenta']."'); </script><span id='VistaInformation".$row['Id_Cuenta']."' class='glyphicon glyphicon-info-sign'></span>".$row['Cuenta']."</div>";
  }
	
	 Cerrar_Conexion($Conexion);
}
function MGrupos()
{
	session_start();
$Conexion=Abrir_Conexion();

echo "<table id='Tabla_Grupos' style='text-align:center; vertical-align:middle;' cellpadding='0' border='0' class='table display'>
	<thead>
			<tr>
			<th>Group</th>
			<th>Owner</th>
			<th>Description</th>
			<th>Date</th>
			<th>Show</th>
			<th></th>
			</tr>
	</thead>";
	echo '<tbody>';
	/*Información de los grupos creados por el administrador*/
	$q = mysql_query("SELECT * FROM Grupo G inner join Cuenta C on G.Id_Cuenta_Propietario= C.Id_Cuenta where C.Id_Cuenta = ".$_SESSION['Id_Cuenta'],$Conexion);
		 while($row =mysql_fetch_assoc($q))
		  {
		  
		  echo '<tr id="hidetr'.$row['Id_Grupo'].'">';
		  echo '<td id="'.$row['Nombre_Grupo'].'Propietario" onclick="UpdateT(\''.$row['Nombre_Grupo'].'\',\'Nombre_Grupo\')">' . $row['Nombre_Grupo'] . "</td>";
		  echo '<td id="'.$row['Cuenta'].'Propietario" onclick="UpdateT(\''.$row['Cuenta'].'\',\'Id_Cuenta_Propietario\')">' .$row['Cuenta']. "</td>";
		  echo '<td id="'.$row['Descripcion'].'Propietario" onclick="UpdateT(\''.$row['Descripcion'].'\',\'Id_Cuenta_Propietario\')"><div style="overflow:auto; height:35px; width:350px">' .$row['Descripcion']. "</div></td>";
		  echo '<td id="'.$row['Fecha'].'Propietario" onclick="UpdateT(\''.$row['Fecha'].'\',\'Id_Cuenta_Propietario\')">' .$row['Fecha']. "</td>";
		  echo'
		  <td id="'.$row['Id_Grupo'].'"  >
		  
		  	<button onclick=Mostrar_Grupo(\''.$row['Id_Grupo'].'\') class="btn btn-primary " data-toggle="modal" data-target=".bs-example-modal-lg">
				 Open
		    </button>
		  </td>';
		  echo '<td ><div class="btn btn-danger" id="'.$row['Id_Grupo'].'" onclick="Delete(\''.$row['Id_Grupo'].'\')">Delete</div></td>';
		  echo "</tr>";
		  
		  }
  
  
   /*Información de los grupos en los que se invita al administrador*/
		$q = mysql_query("SELECT * FROM Grupo G inner join Miembro_Grupo M on G.Id_Grupo = M.Id_Grupo inner join Cuenta C on C.Id_Cuenta = M.Id_Cuenta  where C.Id_Cuenta = ".$_SESSION['Id_Cuenta'],$Conexion);
		   
		   while($row =mysql_fetch_assoc($q))
		  {
			$cons = mysql_query("select * from Cuenta where Id_Cuenta = ".$row['Id_Cuenta_Propietario'],$Conexion);
			$row2 =mysql_fetch_assoc($cons);
		  echo '<tr id="hidetr'.$row['Id_Grupo'].'">';
		   echo '<td id="'.$row['Nombre_Grupo'].'Propietario" onclick="UpdateT(\''.$row['Nombre_Grupo'].'\',\'Nombre_Grupo\')">' . $row['Nombre_Grupo'] . "</td>";
		  echo '<td id="'.$row['Id_Cuenta_Propietario'].'Propietario" onclick="UpdateT(\''.$row['Id_Cuenta_Propietario'].'\',\'Id_Cuenta_Propietario\')">' .$row2['Cuenta']. "</td>";
		  echo '<td id="'.$row['Descripcion'].'Propietario" onclick="UpdateT(\''.$row['Descripcion'].'\',\'Id_Cuenta_Propietario\')"><div style="overflow:auto; height:35px; width:350px">' .$row['Descripcion']. "</div></td>";
		  echo '<td id="'.$row['Fecha'].'Propietario" onclick="UpdateT(\''.$row['Fecha'].'\',\'Id_Cuenta_Propietario\')">' .$row['Fecha']. "</td>";
		  echo'<td id="'.$row['Id_Grupo'].'" onclick=Mostrar_Grupo(\''.$row['Id_Grupo'].'\') ">
		  	<button class="btn btn-primary " data-toggle="modal" data-target=".bs-example-modal-lg">
				 Open
		    </button>
		  </td>';
		  echo '<td id="'.$row['Id_Grupo'].'"  ><div class="btn btn-danger" onclick="Exit_Member(\''.$row['Id_Cuenta'].'\',\''.$row['Id_Grupo'].'\')">Exit</div></td>';
		  echo "</tr>";
		  }
   echo '</tbody>';
echo "</table>";

Cerrar_Conexion($Conexion);	
}
function Consulta_Bash($Query)
{
	 system( "echo \"$Query\" > ./../Consultas/uno");
	$id=uniqid();
	//En caso de que se interrumpa algún proceso

	$R3='./../Consultas/'.$id.'.zip';
	
	 Temporizador($R3);
   shell_exec('./../Scripts/Select.sh "'.$Query.'" ./../Consultas/'.$id.'.txt');

	shell_exec('./../bin/BDtoFasta ./../Consultas/'.$id.'.txt ./../Consultas/'.$id.'.fa');
	shell_exec('zip -j ./../Consultas/'.$id.' ./../Consultas/'.$id.'.fa');
	unlink('./../Consultas/'.$id.'.txt');
	unlink('./../Consultas/'.$id.'.fa');
	$name=urlencode('./../Consultas/'.$id.'.zip');
	$url= './Consultas/'.$id.'.zip';
	$size=filesize('./../Consultas/'.$id.'.zip');
	$Reads="The elements of this view only soport 10000 lines and can't download only copy ";
	$Obj= array('Url'=>$url,'Size'=>formatSizeUnits($size),'Name'=>$name,'Reads'=>$Reads);
	
	echo json_encode($Obj);
	
}
function Temporizador($Ruta)
{
	shell_exec("(sleep 10h; rm -fr $Ruta) > /dev/null 2>&1 & echo $!");
}

function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
?>
