<?php
date_default_timezone_set("America/Mexico_City");
include("MySQL.php");
switch($_REQUEST['Origen'])
{
case "Consultar_Librerias_Inicio":
Consultar_Librerias_Inicio();
break;
case "Borrar_Libreria_Inicio":
Borrar_Libreria_Inicio();
break;
case "CLibrary":
Revisar_Libreria();
break;
case "Libreria":
echo date('Y-m-d H:i:s');
break;
case "Preparar_Archivos":
Preparar_Archivos();
break;
case "Preprocesar_Archivos":
Preprocesar_Archivos();
break;
case "Borrar_Archivo_Directorio":
Borrar_Archivo_Directorio();
break;
case "Configurar_Libreria":
Configurar_Libreria();
break;
case "Almacenar_Tuberia":
Almacenar_Tuberia();
break;
case "Abrir_Folder_Temporal":
Consultar_Directorio_Temporal();
break;
case "Listar_Librerias":
Listar_Librerias();
break;
case "Listar_Historial":
Listar_Historial();
break;
case "Actualizar_Campo":
Actualizar_Campo();
break;
}
function Actualizar_Campo()
{
	$Campo=$_POST['Campo'];
	$Id_Libreria=$_POST['Id_Libreria'];
	$Valor=$_POST['Valor'];
	$Conexion=Abrir_Conexion();
	mysql_query("UPDATE Libreria SET $Campo = '$Valor' WHERE Id_Libreria = $Id_Libreria" ,$Conexion);
	Cerrar_Conexion($Conexion);
}
function Listar_Historial()
{
	session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Historial_Libreria WHERE Id_Cuenta = $Id_Cuenta ", $Conexion);
  
  		
	
	echo '<table style="text-align:center;" id="Tabla_Historial_Libreria" cellspacing="0" border="0" class="table display">
		<thead>
		<tr>
			<th style="text-align:center;">Library</th>
			<th style="text-align:center;">Date</th>
			<th style="text-align:center;">Format</th>
			<th style="text-align:center;">Number of lines</th>
			<th style="text-align:center;"></th>
		</tr>
		</thead>
		';
		
		echo '<tbody>'; 
		
		 while( $row=mysql_fetch_assoc($Resultado))
			   {
				    echo '<tr>'; 
				    echo '<td >' . $row['Nombre_Libreria']. '</td>';
				    echo '<td >' .  $row['Fecha']. '</td>';
				    echo '<td >' .  $row['Formato']. '</td>';
				    echo '<td >' .  intval($row['Numero_Lineas']). '</td>';
				    echo '<td ><button class="btn btn-success" Size="'.formatbytes($row['Archivo'],"MB").'" Archivo="'.$row['Archivo'].'" Nombre_Archivo="'.basename($row['Archivo']).'" onclick="Seleccionar_Archivo(this)" >Select</button></td>';
					echo '</tr>';  
			   }
		echo '</tbody>'; 
		echo "</table>";

	
	 
	Cerrar_Conexion($Conexion);
}
function Listar_Librerias()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Consulta=Array();
	$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Cuenta = ".$_SESSION['Id_Cuenta'], $Conexion);
	Cerrar_Conexion($Conexion);
	while($row=mysql_fetch_array($Resultado))
	{
		$Ruta=glob($row['Ruta']."/Temp/{*}",GLOB_BRACE);
		if(!count($Ruta))
		continue;
	  $Consulta[]=$row;
	}
	echo json_encode($Consulta);
}
function Consultar_Directorio_Temporal()
{
	session_start();
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Libreria = ".$_POST['Id_Libreria'],$Conexion);
  	$row=mysql_fetch_assoc($Resultado);
  		echo '<table style="text-align:center;" id="Tabla_Direcorio_Temporal" cellpadding="0" cellspacing="0" border="0" class="table display">
		<thead>
		<tr>
		<th>File</th>
		<th>Last modification</th>
		<th>Size</th>
		<th></th>
		</tr>
		</thead>
		';
		echo '<tbody>'; 
		
		 foreach( glob($row['Ruta']."/Temp/{*}",GLOB_BRACE) as $Archivo)
			   {
				   if(pathinfo($Archivo, PATHINFO_EXTENSION) == "zip") continue;
				    echo '<tr>'; 
				    echo '<td >' . basename($Archivo). '</td>';
				    echo '<td >' .  date("F d Y H:i:s.", filemtime($Archivo)). '</td>';
				    echo '<td >' . formatbytes($Archivo,"MB"). '</td>';
				    echo '<td ><button class="btn btn-success" Size="'.formatbytes($Archivo,"MB"). '" Archivo="'.$Archivo.'" Nombre_Archivo="'.basename($Archivo).'" onclick="Seleccionar_Archivo(this)" >Select</button></td>';
				 	echo '</tr>';  
			   }
		echo '</tbody>'; 
		echo "</table>";
	
	 
	Cerrar_Conexion($Conexion);
}
function Configurar_Libreria()
{
	session_start();
	$_SESSION['Id_Libreria'] = $_POST['Id_Libreria'];
}
function Preparar_Archivos()
{
	session_start();
	$Id_Cuenta=$_SESSION['Id_Cuenta'];
	$Conexion=Abrir_Conexion();
	$Nombre_Libreria = $_POST['Nombre_Libreria'];
	$Palabras_Clave = $_POST['Palabras_Clave'];
	$Tejido = $_POST['Tejido'];
	$Plataforma = $_POST['Plataforma'];
	$Descripcion = $_POST['Descripcion'];
	$Institucion_Procedencia = $_POST['Institucion_Procedencia'];
	
	mysql_query("INSERT INTO Libreria VALUES(NULL, '$Nombre_Libreria', '$Palabras_Clave', '$Tejido', '$Plataforma', '$Descripcion', NOW(), '$Institucion_Procedencia', ' ', ' ',' ', 0, $Id_Cuenta)", $Conexion);
	$Id_Libreria=mysql_insert_id();
	$_SESSION['Id_Libreria'] = $Id_Libreria;
	$Ruta_Relativa="./../Users/Usuario$Id_Cuenta/$Id_Libreria";
	mysql_query("UPDATE Libreria SET Ruta = '$Ruta_Relativa' WHERE Id_Libreria = $Id_Libreria ", $Conexion);
	Cerrar_Conexion($Conexion);
	Preparar_Ruta($Ruta_Relativa);
	echo json_encode(array('Error'=> ''	));
}
function Preparar_Ruta($Ruta_Relativa)
{
   Generar_Directorio($Ruta_Relativa);
   if(file_exists($_FILES["Archivo"]["tmp_name"]))
   {
	   $Archivo=$Ruta_Relativa."/".$_FILES["Archivo"]["name"];
	   move_uploaded_file($_FILES["Archivo"]["tmp_name"],$Archivo);
	}
	else
	{
		$Archivo = $_POST['Archivo_Servidor'];
		shell_exec("cp $Archivo $Ruta_Relativa/".basename($Archivo));
	}
}
function Preprocesar_Archivos()
{
	session_start();
	$Id_Cuenta=$_SESSION['Id_Cuenta'];
	$Id_Libreria=$_SESSION['Id_Libreria'];
	$Ruta_Relativa="./../Users/Usuario$Id_Cuenta/$Id_Libreria";
	$Archivo = str_replace(" ", "\ ", $_POST["Archivo"]);
	$Archivo = str_replace("(", "\(", $Archivo);
	$Archivo = str_replace(")", "\)", $Archivo);
	$Archivo=$Ruta_Relativa."/".$Archivo;
	Descomprimir($Archivo, $Ruta_Relativa);
	$Archivo = Identificar_Formatos($Ruta_Relativa);
	$Tipo_Archivo=Tipo_Archivo($Ruta_Relativa);
	$Conexion=Abrir_Conexion();
	mysql_query("UPDATE Libreria SET Formato = '$Tipo_Archivo', Archivo = '$Archivo' WHERE Id_Libreria = $Id_Libreria", $Conexion);
	if($Tipo_Archivo)
	{
		$Numero_Lineas = intval(shell_exec("wc -l $Archivo"));
		$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Libreria = $Id_Libreria", $Conexion);
		$row = mysql_fetch_assoc($Resultado);
		//Se realiza el registro en la tabla del historial
		mysql_query("INSERT INTO Historial_Libreria VALUES(NULL, '".$row['Nombre']."', '$Archivo' , NOW(), '$Tipo_Archivo', ' $Numero_Lineas ', $Id_Cuenta , $Id_Libreria )", $Conexion);
		$Id_Historial_Libreria = mysql_insert_id();
		$Directorio_Historial="./../Users/Usuario$Id_Cuenta/Historial$Id_Historial_Libreria";
		mysql_query("UPDATE Historial_Libreria SET Archivo = '$Directorio_Historial/".basename($Archivo)."' WHERE Id_Historial_Libreria = $Id_Historial_Libreria", $Conexion);
		shell_exec("mkdir $Directorio_Historial");
		shell_exec("cp $Archivo $Directorio_Historial");
		
	}
	else
	{
		mysql_query("DELETE FROM Libreria WHERE Id_Libreria = $Id_Libreria", $Conexion);
		shell_exec("rm -fr $Ruta_Relativa");
	}
	Cerrar_Conexion($Conexion);
	 echo json_encode(array("Directorio" => Directorio($Ruta_Relativa),"Valido"=>$Tipo_Archivo,"Base_Archivo"=>basename($Archivo), "Ruta_Relativa" => $Ruta_Relativa));

}
function Generar_Directorio($Ruta_Relativa)
{
	if(!is_dir($Ruta_Relativa))
	{
		mkdir($Ruta_Relativa,0777);
	}
}
function Descomprimir($Archivo,$Ruta_Relativa)
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
function Borrar_Libreria_Inicio()
{
	$Conexion=Abrir_Conexion();
	$Id_Libreria = $_POST['Id_Libreria'];
	$Resultado = mysql_query("SELECT * FROM Libreria WHERE Id_Libreria = $Id_Libreria" ,$Conexion);
	$row = mysql_fetch_assoc($Resultado);
	if(preg_match("/Users/", $row['Ruta']))
	shell_exec("rm -fr ".$row['Ruta']);
	mysql_query("DELETE FROM Libreria WHERE Id_Libreria = $Id_Libreria" ,$Conexion);
	Cerrar_Conexion($Conexion);
}
function Consultar_Librerias_Inicio()
{
	session_start();
	$Id_Cuenta= $_SESSION['Id_Cuenta'];
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Cuenta = $Id_Cuenta AND Configurado = 0", $Conexion);
	echo '<table id="Tabla_Librerias" style="text-align:center; vertical-align:middle;" cellspacing="0" border="0" class="table display"  >
	<thead> 
	<tr>
	<th style="text-align:center;">Library</th>
	<th style="text-align:center;">KeyWords</th>
	<th style="text-align:center;">Tissue</th>
	<th style="text-align:center;">Sequence platform</th>
	<th style="text-align:center;">Protocol</th>
	<th style="text-align:center;">Date</th>
	<th style="text-align:center;">Format</th>
	<th style="text-align:center;">Configurate</th>
	<th style="text-align:center;">Delete</th>
	</tr>';
	echo '</thead>';
	echo '<tbody>'; 
	while($row =mysql_fetch_assoc($Resultado))
	{
		echo '<tr >';
		echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Nombre" onclick="Actualizar(this)">'.$row['Nombre'].'</td>';
		echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Palabras_Clave" onclick="Actualizar(this)">'.$row['Palabras_Clave'].'</td>';
		echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Tejido" onclick="Actualizar(this)">'.$row['Tejido'].'</td>';
		echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Plataforma" onclick="Actualizar(this)">'.$row['Plataforma'].'</td>';
		echo '<td style="cursor:pointer" Id_Libreria = "'.$row['Id_Libreria'].'" Campo="Descripcion" onclick="Actualizar(this)">'.$row['Descripcion'].'</td>';
		echo '<td Id_Libreria = "'.$row['Id_Libreria'].'" >'.$row['Fecha'].'</td>';
		echo '<td Id_Libreria = "'.$row['Id_Libreria'].'" >'.$row['Formato'].'</td>';
		echo '<td ><input type="button" Id_Libreria="'.$row['Id_Libreria'].'" class="btn btn-primary" onclick="Configurar_Libreria(this)" value="Configurate"></td>';
		echo '<td ><input type="button" Id_Libreria="'.$row['Id_Libreria'].'" class="btn btn-danger" onclick="Borrar_Libreria_Inicio(this)" value="Delete"></td>';
		echo "</tr>";
	}
	echo '</tbody>'; 
	echo "</table>";
	Cerrar_Conexion($Conexion);
}
function Identificar_Formatos($Ruta_Relativa)
        {
			$Archivo="";
                foreach( glob($Ruta_Relativa."/{*}",GLOB_BRACE) as $array)
                {
                       $texto=Primeras_Lineas($array,$Ruta_Relativa); 
                                       if(preg_match("@^>@",$texto[0]))
                                {
                                                        if(preg_match("/^[atgcATGCuU]+(\s)*$/",$texto[1]))
                                                        {
                                                                rename($array,$Ruta_Relativa."/File_Fasta");
                                                                $Archivo = $Ruta_Relativa."/File_Fasta";
                                                        }
                               				else if(preg_match("/(^((-?[\d\.])+(\s)?)+)(\s)*$/",$texto[1]))
                                                        {
                                                         	rename($array,$Ruta_Relativa."/File_Qual");
                                                         	$Archivo = $Ruta_Relativa."/File_Qual";
                                        		}
                                        		else if( preg_match("/^[tT\.0123]+(\s)*$/",$texto[1]))
                                        		{
                                         			rename($array,$Ruta_Relativa."/File_CSFasta");
                                         			$Archivo = $Ruta_Relativa."/File_CSFasta";
                                        		}
                                 }
                        else if(preg_match("/^@/",$texto[0]))
                        {
                                 if((preg_match("/^[atgcnATGCuUN]+(\s)*$/",$texto[1])))
                                   {
                                         rename($array,$Ruta_Relativa."/File_FastaQ");
                                         $Archivo = $Ruta_Relativa."/File_FastaQ";
                        	   }
 				 if((preg_match("/^[tT\.0123]+(\s)*$/",$texto[1])))
                                  {
                                         rename($array,$Ruta_Relativa."/File_CSFastaQ");
                                         $Archivo = $Ruta_Relativa."/File_CSFastaQ";
                                  }
                        }
                        else if(preg_match("/^(.)+(\t)[ATGCUactgu]+(\t)(\d)+(\s)*$/",$texto[0]) )
                        {
                                rename($array,$Ruta_Relativa."/File_Tabular3");
                                $Archivo = $Ruta_Relativa."/File_Tabular3";
                        }
                        else if(preg_match("/^[acgtACTGuU]+(\t)(\d)+(\s)*$/",$texto[0]))
                        {
                                rename($array,$Ruta_Relativa."/File_Tabular2");
                                $Archivo = $Ruta_Relativa."/File_Tabular2";
                        }
                        else
                        {
                                 rename($array,$Ruta_Relativa."/File_Unknown");
                                 $Archivo = $Ruta_Relativa."/File_Unknown";
                        }

                }
                $Archivos=glob($Ruta_Relativa."/{*}",GLOB_BRACE);
                return $Archivo;
                               
        }
function Primeras_Lineas($array,$Ruta_Relativa)
{
	$texto="";
         if(!file_exists($array))return "File no found";
                                $file=fopen($array,"r") or exit("File Incorrect");
                                for($i=0;$i<4;$i++)
                                {
                                $txt=trim(fgets($file));
        
                                                if(!empty($txt))
                                                {
                                                          $txt=str_replace("\n","",$txt);
                                                          $txt=str_replace("\r","",$txt);
                
                                                 $texto[$i]=$txt;
                                                }
                                                
                                }
                                fclose($file);
                        
	return $texto;
}
function ls($Ruta_Relativa)
{
   if(is_dir($Ruta_Relativa))
   return glob($Ruta_Relativa."/{*}",GLOB_BRACE);
   else return false;
}
function Borrar_Archivo_Directorio()
{
	session_start();
	$Id_Libreria = $_SESSION['Id_Libreria'];
	$Conexion=Abrir_Conexion();
	mysql_query("DELETE FROM Libreria WHERE Id_Libreria = $Id_Libreria", $Conexion);
	Cerrar_Conexion($Conexion);
	if(isset($_POST['Archivo']))
	shell_exec("rm -fr ".$_POST['Archivo']);
	$Ruta_Relativa=dirname($_POST['Archivo']);
	echo json_encode(array("Directorio" => Directorio($Ruta_Relativa),"Valido" => Archivo_Valido($Ruta_Relativa)));

}
function Directorio($Ruta_Relativa)
{
$Directorio="<br> Please upload the complement file (Qual or CSFasta):";
//Muestra el nombre de los archivos subidos
$array=ls($Ruta_Relativa);
foreach($array as $line)
{
	if(basename($line) != "Temp")
	{
	$Directorio.= "<div id='".basename($line)."'>
	Format: ".basename($line)." Size: ".formatSizeUnits(filesize($line))." Last Modification: ".date("F d Y H:i:s.", filectime($line))."<div class='Close_General glyphicon glyphicon-remove-sign' onclick='Borrar_Archivo_Directorio(\"".$line."\");'></div></div>";
	}
}
return $Directorio;
}
function Tipo_Archivo($Ruta_Relativa)
{
   $Directorio=ls($Ruta_Relativa);
   foreach($Directorio as $Archivo)
   {
       if(basename($Archivo)=="File_Fasta")
          return "Fasta";
       else if(basename($Archivo)=="File_FastaQ")
	  return "FastaQ";
       else if(basename($Archivo)=="File_CSFastaQ")
  	  return "CSFastaQ";
       else if(basename($Archivo)=="File_Unknown")
	 return false;
       else if(basename($Archivo)=="File_Qual" || basename($Archivo)=="File_CSFasta")
         if( Check_Solid($Archivo,$Ruta_Relativa)) return "Qual and CSFasta";
      else return false;
   }

}
function Check_Solid($Archivo1,$Ruta_Relativa)
{
    $Directorio=ls($Ruta_Relativa);
    foreach($Directorio as $Archivo2)
    {
       if(basename($Archivo1)=="File_Qual" && basename($Archivo2)=="File_CSFasta")
          return true;
	   if(basename($Archivo1)=="File_CSFasta" && basename($Archivo2)=="File_Qual")
	      return true;
    }
   return false;
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














function Revisar_Libreria()
{
	session_start();
$Nombre=$_POST['Key'];

$conexion=Abrir_Conexion();
$q = mysql_query("select * from Libreria where Nombre= '$Nombre' and Id_Cuenta = ".$_SESSION['Id_Cuenta'].";",$conexion);
echo mysql_num_rows($q);
	
Cerrar_Conexion($conexion);
}
function Almacenar_Tuberia()
{
	session_start();
	$Id_HTOP=Registrar_Pipe();
	echo json_encode(array('Id_HTOP' => $Id_HTOP));
}

function Registrar_Pipe()
{
	$Conexion=Abrir_Conexion();
	$Id_Libreria = $_SESSION['Id_Libreria'];
	mysql_query("UPDATE Libreria SET Configurado = 1 WHERE Id_Libreria = $Id_Libreria", $Conexion);
	$Pipe=split(",",$_POST['Pipe']);
	$Nombre=split(",",$_POST['Nombre']);
    $Cont=0;
	$Numero_Pipes=count($Pipe);
	mysql_query("INSERT INTO HTOP VALUES(DEFAULT,-1,0,1,$Numero_Pipes,".$_SESSION['Id_Cuenta'].",$Id_Libreria ,0 ,0 ,0, 0 ,0)",$Conexion);
	$Id_HTOP=mysql_insert_id();
	foreach($Pipe as $Script)
	{
                mysql_query("INSERT INTO PIPE VALUES(NULL,'".$Nombre[$Cont]."','No set',$Id_HTOP,'$Script','')",$Conexion);
              
				//	mysql_query("INSERT INTO PIPE VALUE(NULL,'".$Nombre[$Cont]." Report','No set',$Id_HTOP,'Reporte.html')",$Conexion);
		      
	        $Cont++;
        }
        	Cerrar_Conexion($Conexion);

	return $Id_HTOP;
}
?>
