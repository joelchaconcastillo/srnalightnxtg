
<?php 	

switch($_REQUEST['Origen'])
{
	case "Upload":
	Upload();
	break;
	case "GetURL":
	GetURL();	
	break;
	case "Preparar":
	Preparar();
	break;
	case "Minion":
	Minion();
	break;
	case "Filtrado":
	Filtrar();
	break;
	case "Leer_Analisis":
	Analisis_JSon();
	break;
	case "Report_Adapter":
	Report_Adapter();
	break;
	case "Report_Filter":
	Report_Filter();
	break;
	case "Borrar":
	Borrar_Archivo();
	break;
	
}	
function Borrar_Archivo()
{
 unlink($_POST['Archivo']);	
 $array=glob($_POST['Path']."/{*}",GLOB_BRACE);
 echo json_encode(array("Directorio" => Directorio($_POST['Path'])) );
}
//Archivo desde la URL (Nodejs-->PHP)
function GetURL()
{
			$Name=basename($_POST['URL']);
			$RutaRelativa=$_POST['Path'];
			$Path=$_POST['Path']."/$Name";
			echo json_encode(CheckFiles($RutaRelativa,$Path,$_POST['Patron'],$_POST['Analizar']));

		}
function Upload()
{
		$Path=NULL;$RutaRelativa=NULL;$Extension=NULL;
				$RutaRelativa=$_POST['Path'];
				if(!is_dir($RutaRelativa)) mkdir($RutaRelativa,0777);
				$Path=$RutaRelativa."/".$_FILES["fileUpload"]["name"];
				move_uploaded_file($_FILES["fileUpload"]["tmp_name"],$Path);
				echo json_encode(CheckFiles($RutaRelativa,$Path,$_POST['Patron'],$_POST['Analizar']));
		}
//Esta función revisa los archivos disponibles y retorna un array con la
//la configuración de los mismos
function CheckFiles($RutaRelativa,$Path,$Patron,$Analizar)
{
			
			Descomprimir($Path,$RutaRelativa);
			$Archivo=Identificar_Formatos($RutaRelativa);
			$Directorio=Directorio($RutaRelativa);
			if(empty($Patron))
				{
					//Si el archivo no se conoce ofrecer la posibilidad de indicar el patrón del mismo
					if(basename($Archivo) == "File_Unknown")
					{
					$Archivo=$RutaRelativa."/File_Unknown";
					}
					else
					{
						//Se revisa si estan los archivos "CSFasta" y "Qual"
							$Element=array("File_CSFasta" => 0, "File_Qual" =>0);
						foreach( glob($RutaRelativa."/{*}",GLOB_BRACE) as $array)
								{
									$Element[basename($array)]++;
								}
						if($Element["File_CSFasta"]>0 && $Element["File_Qual"]>0)
						$Archivo="File_CSFastaQ+";	
					}						
				}
				$Head=shell_exec("head ".$Archivo);
							return array("Archivo"=>$Archivo,"Path"=>$RutaRelativa,"Patron"=>$Patron,"Directorio"=>$Directorio,"Head"=>$Head,"Analizar"=>$Analizar);	
		
		}
//Esta función genera las coordenadas en formato JSON despues del trimm del adaptador
function Report_Adapter()
{
	       $RutaRelativa=$_POST['Path'];
			
				$Frecuencia_Input=Report_Reaper($RutaRelativa,"Frecuencia_Input");
				$Frecuencia_Output=Report_Reaper($RutaRelativa,"Frecuencia_Output");
				$Calidad_Input=Report_Reaper($RutaRelativa,"Calidad_Input");
				if(!empty($_POST["Unico"]))
				$Longitud_Output=Report_Reaper($RutaRelativa,"Longitud_Unique");
				else
				$Longitud_Output=Report_Reaper($RutaRelativa,"Longitud_Output");
				
				$Complexity_Ouput=Report_Reaper($RutaRelativa,"Complexity");
      	echo json_encode(array(Frecuencia_Input=>$Frecuencia_Input,Frecuencia_Output=>$Frecuencia_Output,Calidad_Input=>$Calidad_Input,Longitud_Output=>$Longitud_Output,Complexity_Ouput=>$Complexity_Ouput));
}
//Genera las coordenadas en formato JSON despues del filtro
function Report_Filter()
{
	       $RutaRelativa=$_POST['Path'];
			
				if(!empty($_POST["Unico"]))
				$Longitud_Output=Report_Reaper($RutaRelativa,"Longitud_Unique_Filter");
				else
				$Longitud_Output=Report_Reaper($RutaRelativa,"Longitud_Output_Filter");
				
				$Complexity_Ouput=Report_Reaper($RutaRelativa,"Longitud_Output_Filter");
      	echo json_encode(array(Longitud_Output=>$Longitud_Output,Complexity_Ouput=>$Complexity_Ouput));
}
//Realiza el calculo del tamaño de adaptador
function Minion()
{
	echo shell_exec("./bin/minion search-adapter -i ".$_POST['Archivo']);
}
function Analisis_JSon()
{
$Calidades=NULL;
$Frecuencias=NULL;
$Longitudes=NULL;
$RutaRelativa=$_POST['Path'];
$File_Out=$_POST['File_Out'];
if(isset($_POST['Analizar']))
{
$Frecuencias=Report_Reaper($RutaRelativa,"Frecuencias");
$Calidades=Report_Reaper($RutaRelativa,"Calidades");
$Longitudes=Report_Reaper($RutaRelativa,"Logitudes");
}
echo json_encode(array("Directorio"=>"Directorio","Calidad"=>$Calidades,"Frecuencia"=>$Frecuencias,"Longitud"=>$Longitudes,"Archivo"=>$File_Out,"Analizar"=>$_POST['Analizar'],"Patron"=>$_POST['Patron']));

}
function Report_Reaper($RutaRelativa,$Tipo)
{

//Una geometría diferente genera un sufijo diferente
if($_POST['Geometria']=="no-bc")
$Sufijo_Trimm="/reaper/Trimm.lane";
else
$Sufijo_Trimm="/reaper/Trimm.-";

switch($Tipo)
{
case "Frecuencias":
$File="$RutaRelativa/Analyze/Report.lane.report.input.nt";
return ParseFrecuencias($File);
break;
case "Calidades":
$File="$RutaRelativa/Analyze/Report.lane.report.input.q";
return ParseCalidades($File);
break;
case "Logitudes":
$File="$RutaRelativa/Analyze/Report.lane.report.clean.len";
return ParseLogitudesReads($File);
break;
case "Frecuencia_Input":
$File="$RutaRelativa$Sufijo_Trimm.report.input.nt";
return ParseFrecuencias($File);
break;
case "Frecuencia_Output":
$File="$RutaRelativa$Sufijo_Trimm.report.clean.nt";
return ParseFrecuencias($File);
break;
case "Calidad_Input":
$File="$RutaRelativa$Sufijo_Trimm.report.input.q";
return ParseCalidades($File);
break;
case "Longitud_Output":
$File="$RutaRelativa$Sufijo_Trimm.report.clean.len";
return ParseLogitudesReads($File);
break;
case "Complexity":
$File="$RutaRelativa/reaper/Trimm.lane.report.complexity";
return ParseComplexity($File);
case "Longitud_Unique":
$FileUnique="$RutaRelativa/reaper/Trimm.lane.report.unique";
$FileRepeated="$RutaRelativa$Sufijo_Trimm.report.clean.len";
return ParseLongitudes($FileUnique,$FileRepeated,"");
break;
case "Longitud_Unique_Filter":
$FileUnique="$RutaRelativa/filter/Trimm.lane.report.unique";
$FileRepeated="$RutaRelativa/reaper/Trimm.lane.report.unique";
return ParseLongitudes($FileUnique,$FileRepeated,"Unique_Filter");
break;
case "Longitud_Output_Filter":
$File="$RutaRelativa/filter/Trimm.lane.report.complexity";
return ParseComplexity($File);
break;
}

}	
function ParseComplexity($File_Complexity)
{
if(file_exists($File_Complexity))
{
 $FILE=fopen($File_Complexity,"r");

			$vec="";
			$vec2="";
			$Total=0;

		   while(!feof($FILE))
			{
			
				  $str1=rtrim(fgets($FILE));
			
				   $str=explode("\t",$str1);
				if($str[0]!="Complexity" && $str[0]!="" && $str[0]!="Max")
				{

				   $vec[$str[0]][$str[1]]=$str[2]; 
				   $vec2[$str[1]][$str[0]]=$str[2];
				}


			}
	fclose($FILE);
				ksort($vec);
				foreach($vec as $key=>$i)
				{
ksort($vec[$key]);
				
				}
				


$Complete= '{"cols": [ {"id":"","label":"Topping","pattern":"","type":"string"},';
ksort($vec2);
foreach($vec2 as $key=>$i)
				{
					
			   $Complete.='{"id":"Total","label":"Complexity: '.$key.'-'.($key+9).'","pattern":"","type":"number"},';
				
				}
$Complete=substr($Complete,0,-1);
$Complete.='],"rows": [';



foreach($vec as $key=>$i)
		{
		$Complete.='{"c":[{"v":"'.$key.'","f":null},';
		foreach($i as $key2=>$i2)
		{

			$Complete.='{"v":"'.$i2.'","f":null},';
		}
		$Complete=substr($Complete,0,-1);
			$Complete.=']},';

		}

$Complete=substr($Complete,0,-1);

$Complete.=']}';

return $Complete;
}

}
function ParseLongitudes($FileUnique,$FileRepeated,$Step)
{
$FILE=fopen($FileUnique,"r");
	$vec="";
	$Total=0;
   while(!feof($FILE))
	{
		  $str1=rtrim(fgets($FILE));
		   $str= preg_split( "/(\t|\s)/", $str1 );//explode(" ",$str1);
		   $vec[$str[0]]=$str[1];
	}
		ksort($vec);
fclose($FILE);

$max=0;
$FILE=fopen($FileRepeated,"r");
$Repetido="";
 while(!feof($FILE))
	{
		  $str1=rtrim(fgets($FILE));
		    $str= preg_split( "/(\t|\s)/", $str1 );//$str=explode(" ",$str1);
		   if($str[0]!="length" && $str[0]!="")
		{
		   $Repetido[$str[0]]=$str[1];
		   if($str[0]>$max) $max=$str[0];
		}
	}
fclose($FILE);

if($Step=="Unique_Filter")
{
$Label_Before="Total Reads Before Filter";
$Label_After="Total Reads After Filter";
}
else
{
$Label_Before="Total Reads";
$Label_After="Uniqued Reads";

}
$Complete= '{"cols": [
{"id":"","label":"Topping","pattern":"","type":"string"},
{"id":"Total","label":"'.$Label_Before.'","pattern":"","type":"number"},
{"id":"Total","label":"'.$Label_After.'","pattern":"","type":"number"}
],"rows": [';


for($i=0;$i<=$max;$i++)
{
	
	$Complete.='{"c":[{"v":"'.$i.'","f":null},{"v":"'.((isset($Repetido[$i]))?$Repetido[$i]:"0").'","f":null},{"v":"'.((isset($vec[$i]))?$vec[$i]:"0").'","f":null}]},';
	shell_exec($Repetido[$i]);
}

$Complete=substr($Complete,0,-1);
$Complete.=']}';
return $Complete;
}

function ParseLogitudesReads($File)
{
$Text=file_get_contents($File);

$Complete= '{"cols": [
{"id":"","label":"Topping","pattern":"","type":"string"},
{"id":"Total","label":"Length Total Reads","pattern":"","type":"number"}
],"rows": [';

foreach(explode("\n",$Text) as $Row)
{
$Cell=explode("\t",$Row);
if($Cell[0]!="length" && $Cell[0]!="")
{
	$Complete.='{"c":[{"v":"'.$Cell[0].'","f":null},{"v":"'.$Cell[1].'","f":null}]},';
	
}

}
$Complete=substr($Complete,0,-1);
$Complete.=']}';
return $Complete;
}
function ParseCalidades($Calidades)
{
$Text=file_get_contents($Calidades);

$Complete= '{"cols": [
{"id":"","label":"Topping","pattern":"","type":"string"},
{"id":"Total","label":"0% Quantile","pattern":"","type":"number"},
{"id":"Unique","label":"10% Quantile","pattern":"","type":"number"},
{"id":"Repeated","label":"25% Quantile","pattern":"","type":"number"},
{"id":"Repeated","label":"50% Quantile","pattern":"","type":"number"},
{"id":"Repeated","label":"75% Quantile","pattern":"","type":"number"},
{"id":"Repeated","label":"90% Quantile","pattern":"","type":"number"},
{"id":"Repeated","label":"100% Quantile","pattern":"","type":"number"}
],"rows": [';

foreach(explode("\n",$Text) as $Row)
{
$Cell=explode("\t",$Row);
if($Cell[0]!="pos" && $Cell[0]!="")
{
	$Complete.='{"c":[{"v":"'.$Cell[0].'","f":null},{"v":"'.$Cell[1].'","f":null},{"v":'.$Cell[2].',"f":null},{"v":'.$Cell[3].',"f":null},{"v":'.$Cell[4].',"f":null},{"v":'.$Cell[5].',"f":null},{"v":'.$Cell[6].',"f":null},{"v":'.$Cell[7].',"f":null}]},';
	
}

}
$Complete=substr($Complete,0,-1);
$Complete.=']}';
return $Complete;
}
function ParseFrecuencias($Analizar)
{

$Total=1;

$Text=file_get_contents($Analizar);//."/Report.lane.report.input.nt");

$Complete= '{"cols": [
{"id":"","label":"Topping","pattern":"","type":"string"},
{"id":"A","label":"A","pattern":"","type":"number"},
{"id":"T","label":"T","pattern":"","type":"number"},
{"id":"G","label":"G","pattern":"","type":"number"},
{"id":"C","label":"C","pattern":"","type":"number"},
{"id":"N","label":"N","pattern":"","type":"number"}
],"rows": [';

foreach(explode("\n",$Text) as $Row)
{
$Cell=explode("\t",$Row);
if($Cell[0]!="pos" && $Cell[0]!="")
{
	$Total=$Cell[1]+$Cell[2]+$Cell[3]+$Cell[4]+$Cell[5];
	$Complete.='{"c":[{"v":"'.$Cell[0].'","f":null},{"v":'.$Cell[1]/$Total.',"f":null},{"v":'.$Cell[2]/$Total.',"f":null},{"v":'.$Cell[3]/$Total.',"f":null},{"v":'.$Cell[4]/$Total.',"f":null},{"v":'.$Cell[5]/$Total.',"f":null}]},';
	
}

}
$Complete=substr($Complete,0,-1);
$Complete.=']}';
return $Complete;
}	
function Directorio($RutaRelativa)
{
$Directorio="<br> Please upload the complement file (Qual or CSFasta):";
//Muestra el nombre de los archivos subidos

$array=glob($RutaRelativa."/{*}",GLOB_BRACE);
foreach($array as $line)
$Directorio.= "<div id='".basename($line)."'>
Format: ".basename($line)." Size: ".formatSizeUnits(filesize($line))." Last Modification: ".date("F d Y H:i:s.", filectime($line))."<div class='Cerrar' onclick='Borrar_Archivo(\"".$RutaRelativa."\",\"".$line."\");'>x</div></div>";
return $Directorio;
}
function Descomprimir($Path,$RutaRelativa)
{
$Path_parts=pathinfo(basename($Path));
if(strtoupper($Path_parts['extension'])=="ZIP")
{
shell_exec("unzip $Path -d ".$RutaRelativa);
shell_exec("rm $Path");
$Files =  glob($RutaRelativa."/{*.zip}",GLOB_BRACE);
strlen($Files[0])>0?Descomprimir($Files[0],$RutaRelativa):true;
}
else if(strtoupper($Path_parts['extension'])=="GZ")
{
shell_exec("gunzip $Path -d ".$RutaRelativa);
shell_exec("rm $Path");
$Files =  glob($RutaRelativa."/{*.gz}",GLOB_BRACE);
strlen($Files[0])>0?Descomprimir($Files[0],$RutaRelativa):true;
}
else if(strtoupper($Path_parts['extension'])=="TGZ")
{
shell_exec("tar xvf $Path -C ".$RutaRelativa);
shell_exec("rm $Path");
$Files =  glob($RutaRelativa."/{*.gz}",GLOB_BRACE);
strlen($Files[0])>0?Descomprimir($Files[0],$RutaRelativa):true;
}


}			
    #########Identificar el formato en base a su gramática#######
	//Parámetros (Ruta completa del archivo, Ruta Relativa)
	//Esta función retorna el tipo de archivo en base a las primeras lineas
	//también renombra el archivo con su "tipo"
	function Identificar_Formatos($RutaRelativa)
	{
		foreach( glob($RutaRelativa."/{*}",GLOB_BRACE) as $array)
		{
			
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
					if(preg_match("@^>@",$texto[0]))
				{
							if(preg_match("/^[atgcATGCuU]+(\s)*$/",$texto[1]))
							{
									rename($array,$RutaRelativa."/File_Fasta");
							}
							
				else if(preg_match("/(^((-?[\d\.])+(\s)?)+)(\s)*$/",$texto[1]))
					{		
						rename($array,$RutaRelativa."/File_Qual");
					}
					else if( preg_match("/^[tT\.0123]+(\s)*$/",$texto[1]))
					{	
					 rename($array,$RutaRelativa."/File_CSFasta");
					}
			}
			else if(preg_match("/^@/",$texto[0]))
			{
			if((preg_match("/^[atgcnATGCuUN]+(\s)*$/",$texto[1])))
			{
					 rename($array,$RutaRelativa."/File_FastaQ");
			}			
				if((preg_match("/^[tT\.0123]+(\s)*$/",$texto[1])))
				{
					 rename($array,$RutaRelativa."/File_CSFastaQ");
				}
			}
				 else if(preg_match("/^(.)+(\t)[ATGCUactgu]+(\t)(\d)+(\s)*$/",$texto[0]) )
				 {
					 rename($array,$RutaRelativa."/File_Tabular3");
				 }
				  else if(preg_match("/^[acgtACTGuU]+(\t)(\d)+(\s)*$/",$texto[0]))
				 {
					 rename($array,$RutaRelativa."/File_Tabular2");
				 }	
				 else
				 {
					  rename($array,$RutaRelativa."/File_Unknown");
				 }
				
		}
		$Archivos=glob($RutaRelativa."/{*}",GLOB_BRACE);
		if(count($Archivos)==1)
		{
			return $Archivos[0];
		}
		else
		{
		    return false;
		}
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
function Esconder()
{
	/*echo '<script>Esconder_Upload("'.basename($_POST['Path']).'");</script>';
*/}
?>
