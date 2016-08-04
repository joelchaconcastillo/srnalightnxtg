<?php
include("MySQL.php");
switch($_REQUEST["Origen"])
{
   case "Subir_Archivo":
   Subir_Archivo();
   break;
   case "Borrar_Archivo":
   Borrar_Archivo_Directorio();
   break;
   case "Directorio":
   Revisar_Directorio();
   break;
   case "Abrir_Folder_Temporal":
   Consultar_Directorio_Temporal();
   break;
   case "Listar_Librerias":
   Listar_Librerias();
   break;
   case "Utilizar_Temporal_Procesar_Archivo":
   Utilizar_Temporal_Procesar_Archivo();
   break;
}
function Utilizar_Temporal_Procesar_Archivo()
{
	session_start();
	$Ruta_Relativa=$_SESSION['Ruta'];
	$Archivo=$_SESSION['Ruta']."/".basename($_POST["Archivo"]);
	Generar_Directorio($Ruta_Relativa);
	copy($_POST["Archivo"], $Archivo);
    Descomprimir($Archivo,$Ruta_Relativa);
    Identificar_Formatos($Ruta_Relativa);
   echo json_encode(array("Directorio" => Directorio($Ruta_Relativa),"Valido"=>Archivo_Valido($Ruta_Relativa),"Base_Archivo"=>basename($Archivo), "Ruta_Relativa" => $Ruta_Relativa));

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
	$Ruta_Directorio_Temporal_Usuario='./../Users/'. $_SESSION['Cuenta'].'/';
	$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Libreria = ".$_POST['Id_Libreria'],$Conexion);
  	$row=mysql_fetch_assoc($Resultado);
  		echo '<table style="text-align:center;" cellpadding="0" cellspacing="0" border="0" class="table display">
		<tr>
		<th>File</th>
		<th></th>
		</tr>';
		echo '<tbody>'; 
		
		 foreach( glob($Ruta_Directorio_Temporal_Usuario.basename($row['Ruta'])."/Temp/{*}",GLOB_BRACE) as $Archivo)
			   {
				    if(pathinfo($Archivo, PATHINFO_EXTENSION) == "zip") continue;
				    echo '<tr>'; 
				    echo '<td >' . basename($Archivo). '</td>';
				    echo '<td ><button class="btn btn-success" onclick="Agregar_Archivo_Temporal(\''.$Archivo.'\')" >Select</button></td>';
					echo '</tr>';  
			   }
		echo '</tbody>'; 
		echo "</table>";
	
	 
	Cerrar_Conexion($Conexion);
}
function Borrar_Archivo_Directorio()
{
   unlink($_POST['Archivo']);
   $Ruta_Relativa=Ruta_Relativa_Archivo($_POST['Archivo']);
   echo json_encode(array("Directorio" => Directorio($Ruta_Relativa),"Valido" => Archivo_Valido($Ruta_Relativa)));

}
function Ruta_Relativa_Archivo($Archivo)
{

   $Longitud=strlen(basename($Archivo));
   $Ruta_Relativa=substr($Archivo,0,strlen($Archivo)-$Longitud);
   return $Ruta_Relativa;
}
function Revisar_Directorio()
{
   session_start();
   echo Directorio($_SESSION['Ruta']);
}
function Subir_Archivo()
{
   session_start();
   $Ruta_Relativa=$_SESSION['Ruta'];
   $Archivo=Preparar_Archivo($Ruta_Relativa);
   Descomprimir($Archivo,$Ruta_Relativa);
   Identificar_Formatos($Ruta_Relativa);
   
   echo json_encode(array("Directorio" => Directorio($Ruta_Relativa),"Valido"=>Archivo_Valido($Ruta_Relativa),"Base_Archivo"=>basename($Archivo), "Ruta_Relativa" => $Ruta_Relativa));
}
function Preparar_Archivo($Ruta_Relativa)
{
   Generar_Directorio($Ruta_Relativa);
   $Archivo=$Ruta_Relativa."/".$_FILES["fileUpload"]["name"];
   move_uploaded_file($_FILES["fileUpload"]["tmp_name"],$Archivo);
   return $Archivo;
}
function Generar_Directorio($Ruta_Relativa)
{
	if(!is_dir($Ruta_Relativa))
   mkdir($Ruta_Relativa,0777);
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
 function Identificar_Formatos($Ruta_Relativa)
        {
                foreach( glob($Ruta_Relativa."/{*}",GLOB_BRACE) as $array)
                {
                       $texto=Primeras_Lineas($array,$Ruta_Relativa); 
                                       if(preg_match("@^>@",$texto[0]))
                                {
                                                        if(preg_match("/^[atgcATGCuU]+(\s)*$/",$texto[1]))
                                                        {
                                                                rename($array,$Ruta_Relativa."/File_Fasta");
                                                        }
                               				else if(preg_match("/(^((-?[\d\.])+(\s)?)+)(\s)*$/",$texto[1]))
                                                        {
                                                         	rename($array,$Ruta_Relativa."/File_Qual");
                                        		}
                                        		else if( preg_match("/^[tT\.0123]+(\s)*$/",$texto[1]))
                                        		{
                                         			rename($array,$Ruta_Relativa."/File_CSFasta");
                                        		}
                                 }
                        else if(preg_match("/^@/",$texto[0]))
                        {
                                 if((preg_match("/^[atgcnATGCuUN]+(\s)*$/",$texto[1])))
                                   {
                                         rename($array,$Ruta_Relativa."/File_FastaQ");
                        	   }
 				 if((preg_match("/^[tT\.0123]+(\s)*$/",$texto[1])))
                                  {
                                         rename($array,$Ruta_Relativa."/File_CSFastaQ");
                                  }
                        }
                        else if(preg_match("/^(.)+(\t)[ATGCUactgu]+(\t)(\d)+(\s)*$/",$texto[0]) )
                        {
                                rename($array,$Ruta_Relativa."/File_Tabular3");
                        }
                        else if(preg_match("/^[acgtACTGuU]+(\t)(\d)+(\s)*$/",$texto[0]))
                        {
                                rename($array,$Ruta_Relativa."/File_Tabular2");
                        }
                        else
                        {
							if(basename($array)!="Temp")
                                 rename($array,$Ruta_Relativa."/File_Unknown");
                        }

                }
                $Archivos=glob($Ruta_Relativa."/{*}",GLOB_BRACE);
                               
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
	Format: ".basename($line)." Size: ".formatSizeUnits(filesize($line))." Last Modification: ".date("F d Y H:i:s.", filectime($line))."<div class='Close_General glyphicon glyphicon-remove-sign' onclick='Borrar_Archivo(\"".$line."\");'></div></div>";
	}
}
return $Directorio;
}
function Archivo_Valido($Ruta_Relativa)
{
   $Directorio=ls($Ruta_Relativa);
   foreach($Directorio as $Archivo)
   {
       if(basename($Archivo)=="File_Fasta")
          return true;
       else if(basename($Archivo)=="File_FastaQ")
	  return true;
       else if(basename($Archivo)=="File_CSFastaQ")
  	  return true;
       else if(basename($Archivo)=="File_Unknown")
	 return false;
       else if(basename($Archivo)=="File_Qual" || basename($Archivo)=="File_CSFasta")
         return Check_Solid($Archivo,$Ruta_Relativa);
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
//Esta función convierte el formato "bytes" a una métrica adecuada al tamaño
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
