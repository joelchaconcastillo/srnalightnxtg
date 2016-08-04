<?php
include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Insertar_Organismo":
Insertar_Organismo();
break;
case "Organismos_Disponibles":
Cargar_Organismos();
break;
case "Informacion_Genomas_Disponibes":
Cargar_Informacion_Genomas();
break;
case "Preparar_Archivos":
Preparar_Archivos();
break;
case "Descomprimir_Archivos":
Descomprimir_Archivos();
break;
case "Indexar_Fasta":
Indexar_Fasta();
break;
case "Preparar_JBrowse":
Preparar_JBrowse();
break;
case "Borrar_Genoma":
Borrar_Genoma();
break;
case "Actualizar_Campo":
Actualizar_Campo();
break;
}
function Preparar_Archivos()
{
	session_start();
	$Id_Genoma=Almacenar_Informacion();
	$_SESSION['Id_Genoma']=$Id_Genoma;
	$Ruta_Relativa="./../Indexes/$Id_Genoma";//.$_POST['Nombre_Genoma']."_".$_POST['Version_Genoma'];
	Generar_Directorios($Ruta_Relativa);
	Ordenar_Archivos($Ruta_Relativa);
	echo json_encode(array('Exito'=>'Exito'));
}
function Descomprimir_Archivos()
{
	session_start();
	//$Id_Genoma=$_SESSION['Id_Genoma'];
	$Id_Genoma=$_SESSION['Id_Genoma'];
	$Ruta_Relativa="./../Indexes/$Id_Genoma";//.$_POST['Nombre_Genoma']."_".$_POST['Version_Genoma'];
	$Genoma_Fasta=$Ruta_Relativa."/".$_POST['Genoma_Fasta'];
	$Genoma_GFF=$Ruta_Relativa."/".$_POST['Genoma_GFF'];
	Descomprimir($Ruta_Relativa, $Genoma_Fasta);
	Descomprimir($Ruta_Relativa, $Genoma_GFF);
	if(!Revisar_Fasta($Genoma_Fasta)) 
	{
		if($_POST['Nombre_Genoma'])
		shell_exec("rm -fr $Ruta_Relativa");
		echo json_encode(array('Error'=> 'Incorrect Fasta file'));
		exit();
	}
	shell_exec("mv $Genoma_Fasta $Ruta_Relativa/FASTA");
	shell_exec("mv $Genoma_GFF $Ruta_Relativa/Anotacion/GFF.gff");
	echo json_encode(array('Error'=> ''));
}
function Indexar_Fasta()
{
	session_start();
	$Id_Genoma=$_SESSION['Id_Genoma'];
	//$Id_Genoma=Consultar_Id_Genoma();
	$Ruta_Relativa="./../Indexes/$Id_Genoma";//.$_POST['Nombre_Genoma']."_".$_POST['Version_Genoma'];
	Indexar_Archivo_Fasta($Ruta_Relativa);
	echo json_encode(array('Error'=> ''));
}
function Preparar_JBrowse()
{
	session_start();
	$Id_Genoma=$_SESSION['Id_Genoma'];
	//$Id_Genoma=Consultar_Id_Genoma();
	//Nota: no se refiere como "./../Indexes" por que en script Indexar_JSON.sh se ejecuta desdeel directorio 
	//		origen del sistema, esto es debido a que no se pueden ejecutar script de JBrowse desde otras rutas
	//		por la instalación.
	$Ruta_Relativa="Indexes/$Id_Genoma";//.$_POST['Nombre_Genoma']."_".$_POST['Version_Genoma'];
	Tam_Cromosomas($Ruta_Relativa);
	Convertir_JSON($Ruta_Relativa);
	
	echo json_encode(array('Error'=> ''));

}
function Almacenar_Informacion()
{
	session_start();
	$Id_Genoma=$_SESSION['Id_Genoma'];
	//$Id_Genoma=Consultar_Id_Genoma();
	$Id_Cuenta=$_SESSION['Id_Cuenta'];
	$Conexion=Abrir_Conexion();
	$Nombre=$_POST['Nombre_Genoma'];
	$Version=$_POST['Version_Genoma'];
	$Descripcion=$_POST['Descripcion_Genoma'];
	$Origen=$_POST['Origen_Genoma'];
	$Organismo=$_POST['Organismo'];
	$Resultado=mysql_query("INSERT INTO Genoma VALUES(NULL, '$Nombre', '$Version', '$Descripcion', NOW(), '$Origen', $Organismo, $Id_Cuenta )", $Conexion);
	$Id_Genoma= mysql_insert_id();
	Cerrar_Conexion($Conexion);
	if (!$Resultado) 
	{
		Ejecutar_Comando("rm -fr ./../Indexes/$Id_Genoma");
		echo json_encode(array('Error' =>'Invalid query: ' . mysql_error()));
		exit();
	}
	
	return $Id_Genoma;
}
function Cargar_Informacion_Genomas()
{
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Genoma",$Conexion);
	Cerrar_Conexion($Conexion);
	echo '
	<table id="Tabla_Genomas" style="text-align:center; vertical-align:middle;" cellspacing="0" border="0" class="table display">
		<thead >
			<th style="text-align:center;">Genome name</th>
			<th style="text-align:center;">Version</th>
			<th style="text-align:center;">Organismo</th>
			<th style="text-align:center;">Description</th>
			<th style="text-align:center;">Source</th>
			<th style="text-align:center;">Date</th>
			<th style="text-align:center;">User</th>
			<th style="text-align:center;">Show</th>
			<th style="text-align:center;">Delete</th>
		</thead>
	<tbody>
	';
	while($row=mysql_fetch_assoc($Resultado))
	{
		echo '
		<tr> 
			<td style="cursor:pointer;" Id_Genoma="'.$row['Id_Genoma'].'" Campo="Nombre" onclick="Actualizar(this)">'.$row['Nombre'].'</td>
			<td style="cursor:pointer;" Id_Genoma="'.$row['Id_Genoma'].'" Campo="Version" onclick="Actualizar(this)">'.$row['Version'].'</td>
			<td style="cursor:pointer;" Id_Genoma="'.$row['Id_Genoma'].'" Campo="Id_Organismo" onclick="Actualizar_Organismo(this)">'.Consultar_Organismo($row['Id_Organismo']).'</td>
			<td style="cursor:pointer;" Id_Genoma="'.$row['Id_Genoma'].'" Campo="Descripcion" onclick="Actualizar(this)">'.$row['Descripcion'].'</td>
			<td style="cursor:pointer;" Id_Genoma="'.$row['Id_Genoma'].'" Campo="Origen" onclick="Actualizar(this)">'.$row['Origen'].'</td>
			<td>'.$row['Fecha_Alta'].'</td>
			<td >'.Consultar_Usuario($row['Id_Cuenta']).'</td>
			<td><input type="button" Id_Genoma="'.$row['Id_Genoma'].'" value="JBrowse" class="btn btn-primary" data-toggle="modal" data-target="#Modal_JBrowse_Genoma" onclick="JBrowse(\'./JBrowse/index.html?data=./../Indexes/'.$row['Id_Genoma'].'/JBrowse\')" ></td>
			<td><input type="button" Id_Genoma="'.$row['Id_Genoma'].'" value="Delete" class="btn btn-danger" onclick="Borrar_Genoma(this);"></td>
			
		 </tr>';
	}//<td>'.Consultar_Usuario($row['Id_Cuenta']).'</td>
	echo '
		</tbody>
	</table>';	
}
function Consultar_Organismo($Id_Organismo)
{
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Organismo WHERE Id_Organismo = $Id_Organismo", $Conexion);
	Cerrar_Conexion($Conexion);
	$row=mysql_fetch_assoc($Resultado);
	return $row['Organismo'];
}
function Consultar_Usuario($Id_Cuenta)
{
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Cuenta WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	Cerrar_Conexion($Conexion);
	$row=mysql_fetch_assoc($Resultado);
	return $row['Cuenta'];
}

function Cargar_Organismos()
{
	$Conexion = Abrir_Conexion();  
		$Resultado=mysql_query("select * from Organismo",$Conexion);
		echo "<option selected disabled>Please select an organism</option>";
		 while($row=mysql_fetch_array($Resultado))
		 {
			 echo "<option value='".$row['Id_Organismo']."'>".$row['Organismo']."</option>";
		 }	
     Cerrar_Conexion($Conexion);
}
function Generar_Directorios($Ruta_Relativa)
{
	if(!is_dir($Ruta_Relativa))
   shell_exec("mkdir $Ruta_Relativa");
   shell_exec("mkdir $Ruta_Relativa/Anotacion");
   shell_exec("mkdir $Ruta_Relativa/Illumina");
   shell_exec("mkdir $Ruta_Relativa/Solid");
   
}
function Ordenar_Archivos($Ruta_Relativa)
{
   $Genoma_Fasta=$Ruta_Relativa."/".$_FILES["Genoma_Fasta"]["name"];
   move_uploaded_file($_FILES["Genoma_Fasta"]["tmp_name"],$Genoma_Fasta);
   $Genoma_GFF=$Ruta_Relativa."/".$_FILES["Genoma_GFF"]["name"];
   move_uploaded_file($_FILES["Genoma_GFF"]["tmp_name"],$Genoma_GFF);
}

function Descomprimir($Ruta_Relativa, $Archivo)
{
		   $Path_parts=pathinfo(basename($Archivo));
	if(strtoupper($Path_parts['extension'])=="ZIP")
	{
	   shell_exec("unzip $Archivo -d ".$Ruta_Relativa);
	   shell_exec("rm $Archivo");
	   $Files =  glob($Ruta_Relativa."/{*.zip}",GLOB_BRACE);
	   if(strlen($Files[0])>0)Descomprimir($Files[0],$Ruta_Relativa);
	}
	else if(strtoupper($Path_parts['extension'])=="GZ")
	{
	   shell_exec("gunzip $Archivo -d ".$Ruta_Relativa);
	   shell_exec("rm $Archivo");
	   $Files =  glob($Ruta_Relativa."/{*.gz}",GLOB_BRACE);
	   if(strlen($Files[0])>0)Descomprimir($Files[0],$Ruta_Relativa);
	}
	else if(strtoupper($Path_parts['extension'])=="TGZ")
	{
	   shell_exec("tar xvf $Archivo -C ".$Ruta_Relativa);
	   shell_exec("rm $Archivo");
	   $Files =  glob($Ruta_Relativa."/{*.gz}",GLOB_BRACE);
	   if(strlen($Files[0])>0)Descomprimir($Files[0],$Ruta_Relativa);
	}

}
function Revisar_Fasta($Path)
{
	$Temp=fopen($Path,"r");
	$c1=fgets($Temp);
	$c2=fgets($Temp);
		if(!preg_match("@^>(.)+@",$c1)){fclose($Temp);return false;}
		if(!preg_match("/^[atgcATGCuU]+(\s)*$/",$c2)){fclose($Temp);return false;}
	
	fclose($Temp);
	
	return true;
}
function Indexar_Archivo_Fasta($Ruta_Relativa)
{	
	$basename=split("_",basename($Ruta_Relativa));
	$basename=$basename[0];
	Ejecutar_Comando("./../bin/bowtie-build -f $Ruta_Relativa/FASTA $Ruta_Relativa/Illumina/".$basename, "There was a problem indexing format illumina");
	Ejecutar_Comando("./../bin/bowtie-build -f $Ruta_Relativa/FASTA -C $Ruta_Relativa/Solid/".$basename."_C", "There was a problem indexing format Solid");
}
function Convertir_JSON($Ruta_Relativa)
{
	if(!file_exists("./../$Ruta_Relativa/Anotacion/GFF.gff"))
	Generar_GFF("./../".$Ruta_Relativa);
	Ejecutar_Comando("mkdir  ./../$Ruta_Relativa/JBrowse ", "There was a problem making directory JBrowse");
	Ejecutar_Comando("./../Scripts/Indexar_JSON.sh $Ruta_Relativa/FASTA $Ruta_Relativa/Anotacion/GFF.gff $Ruta_Relativa/JBrowse","There was a problem indexing JSON JBrowse");
	
	$Conexion = Abrir_Conexion();  	
	$Resultado=mysql_query("select * from Cuenta where Tipo_Cuenta = 'User'",$Conexion);
	Cerrar_Conexion($Conexion);
    	while($row=mysql_fetch_array($Resultado))
		{
			Ejecutar_Comando("cp ./../$Ruta_Relativa/JBrowse/trackList.json ./../$Ruta_Relativa/JBrowse/Usuario".$row['Id_Cuenta']."trackList.json", "There was a problem copying JBrowse files");
			Ejecutar_Comando("./../Scripts/Import_JBrowse_User.sh Usuario".$row['Id_Cuenta']." ./../$Ruta_Relativa/JBrowse ".basename($Ruta_Relativa), "There was a problem importing JBrowse files");
		}
	
}
function Generar_GFF($Ruta_Relativa)
{
	$Size = fopen("$Ruta_Relativa/Anotacion/Sizes", "r");
	$FILE = fopen("$Ruta_Relativa/Anotacion/GFF.gff","w+");
	while(!feof($Size))
	{
		$Line = fgets($Size);
		$Line = explode("\t",trim($Line));
		fwrite($FILE,$Line[0]."\tElemen\tElement\t1\t");
		fwrite($FILE,$Line[1]."\t.\t.\t.\tID=".$Line[0].";Name=".$Line[0]."\n");
	}
	fclose($FILE);
	fclose($Size);
}
function Tam_Cromosomas($Ruta_Relativa)
{
	$File_Fasta=fopen("./../$Ruta_Relativa/FASTA", "r");
	$File_Sizes=fopen("./../$Ruta_Relativa/Anotacion/Sizes", "w");
	$Cont=0;
	$Head="";
		while($String = fgets($File_Fasta))
		{
			$String=trim($String);
			if(preg_match("@^>(.)+@",$String))
			{
				if($Cont)
				{
					fwrite($File_Sizes,"$Head\t$Cont\n");
				}
				$Head=str_replace(">","",$String);

				$Cont=0;
				
			}
			else
			{
				$Cont+=strlen($String);	
			}
			

		}
		fwrite($File_Sizes,"$Head\t$Cont\n");
		fclose($File_Fasta);
}
function Ejecutar_Comando($Comando, $Error)
{
	$Comando=shell_exec("$Comando ; echo $?");
	if($Comando > 0)
	{
		session_start();
		$Id_Genoma=$_SESSION['Id_Genoma'];
		//$Id_Genoma=Consultar_Id_Genoma();
		echo json_encode(array('Error'=> "$Error"));
		if(isset($Id_Genoma))
		shell_exec("rm -fr ./../Indexes/$Id_Genoma");
		$Conexion=Abrir_Conexion();
		mysql_query("DELETE FROM Genoma WHERE Id_Genoma = $Id_Genoma",$Conexion);
		Cerra_Conexion($Conexion);
		exit();
	}
}
function Consultar_Id_Genoma()
{
	$Nombre_Genoma=$_POST['Nombre_Genoma'];
	$Nombre_Version=$_POST['Version_Genoma'];
	$Conexion= Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Genoma WHERE Nombre = '$Nombre_Genoma' AND Version = '$Nombre_Version'", $Conexion);
	Cerrar_Conexion($Conexion);
	$row=mysql_fetch_assoc($Resultado);
	return $row['Id_Genoma'];
}
function Borrar_Genoma()
{
	$Id_Genoma=$_POST['Id_Genoma'];
	$Conexion = Abrir_Conexion(); 	
	$Resultado=mysql_query("SELECT * FROM Genoma WHERE Id_Genoma = $Id_Genoma", $Conexion);
	$row=mysql_fetch_assoc($Resultado);
	shell_exec("rm -fr  ./../Indexes/".$row['Id_Genoma']);
	
	$Resultado=mysql_query("SELECT * FROM Cuentas WHERE Tipo_Cuenta = 'User'",$Conexion);

	while($Usuario=mysql_fetch_array($Resultado))
		{		
			shell_exec("rm -fr ./../Users/Usuario".$Usuario['Id_Cuenta']."/JBrowse/".$row['Id_Genoma']."index.html");	
			shell_exec('./../Scripts/Delete.sh "DELETE FROM Molecula'.$Usuario['Id_Cuenta'].' WHERE Id_Genoma = '.$Id_Genoma.';" > /dev/null 2>&1 &');
		}
	mysql_query("DELETE FROM Genoma WHERE Id_Genoma = $Id_Genoma",$Conexion);
	
	 Cerrar_Conexion($Conexion);
}
function Actualizar_Campo()
{
	$Campo=$_POST['Campo'];
	$Id_Genoma=$_POST['Id_Genoma'];
	$Valor=$_POST['Valor'];
	$Conexion=Abrir_Conexion();
	mysql_query("UPDATE Genoma SET $Campo = '$Valor' WHERE Id_Genoma = $Id_Genoma" ,$Conexion);
	Cerrar_Conexion($Conexion);
}

?>
