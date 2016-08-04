<?php
include("MySQL.php");
switch($_REQUEST['Origen'])
{
   case "Cargar_Datos":
   session_start();
   Cargar_Datos($_SESSION['Ruta']);
   break;
   case "Actualizar_Reporte":
   Actualizar_Reporte();
   break;
   case "Mover_Archivo":
   Mover_Archivo();
   break;
}
function Mover_Archivo()
{
	$Directorio =$_POST['Destino']."/Temp";
	if(!is_dir($Directorio))
	shell_exec("mkdir $Directorio");
	rename($_POST['Archivo'], $_POST['Destino']."/Temp/".basename($_POST['Archivo']) );
}
function Actualizar_Reporte()
{
	session_start();
	$Conexion=Abrir_Conexion();
	mysql_query("UPDATE PIPE set Reporte = 0 WHERE Id_HTOP = ".$_SESSION['Id_HTOP'],$Conexion);
	Cerrar_Conexion($Conexion);
}
function Cargar_Datos($Ruta_Relativa)
{
   shell_exec($Ruta_Relativa);
   $Directorio=Directorio($Ruta_Relativa);
   $Reporte=Reporte($Ruta_Relativa);
   $STDOUT=STDOUT($Ruta_Relativa);
   $Calidades1=ParseCalidades_Parte1($Ruta_Relativa."/Reporte_Estadisticas");
   $Calidades2=ParseCalidades_Parte2($Ruta_Relativa."/Reporte_Estadisticas");
   $Frecuencias=ParseFrecuencias($Ruta_Relativa."/Reporte_Estadisticas");

   echo json_encode(array("Directorio"=>$Directorio, "Reporte"=>$Reporte, "STDOUT"=>$STDOUT, "Calidad1"=>$Calidades1, "Calidad2"=>$Calidades2, "Frecuencias"=>$Frecuencias));
}
function Directorio($Ruta_Relativa)
{
   $Listado="";
   $Listado.='<table style="text-align:center;" cellpadding="0" cellspacing="0" border="0" class="table display">
				<tr><th style="text-align:center;">File</th><th style="text-align:center;">Size</th><th style="text-align:center;">Last modification</th><th></th><th></th></tr>';
   foreach( glob($Ruta_Relativa."/{*}",GLOB_BRACE) as $array)
   {
	   if(basename($array)=="JBrowse")continue;
	   if(basename($array)=="Temp")continue;
	   if(basename($array)=="Reporte_Estadisticas")continue;
	   if(basename($array)=="Reporte_STDOUT")continue;
       if(preg_match('/\.zip/',$array)) continue;
		 if(basename($array) == $_SESSION['Archivo'] || basename($array) == "BigWig")
        $Listado.="<tr><td>".basename($array)."</td><td>".formatbytes($array,"MB")."</td><td>".date("F d Y H:i:s.", filemtime($array))."</td><td><button class='btn btn-success' Archivo='$array' style='cursor:pointer;' onclick='Descargar_Archivo(this)'>Download</button></td><td></td></tr>";
		else
		$Listado.="<tr><td>".basename($array)."</td><td>".formatbytes($array,"MB")."</td><td>".date("F d Y H:i:s.", filemtime($array))."</td><td><button class='btn btn-success' Archivo='$array' style='cursor:pointer;' onclick='Descargar_Archivo(this)'>Download</button></td><td><button class='btn btn-primary' onclick=\"Mover_Archivo('$array','$Ruta_Relativa'), $(this).parent().parent().fadeOut();\">Move to tempory folder </button></td></tr>";
     
   }   
   $Listado.='</table>';
   return $Listado;
}
function Reporte($Ruta_Relativa)
{
   $Text="";
   $Archivo=$Ruta_Relativa."/Reporte_Estadisticas";
   if(file_exists($Archivo))
   {
      $Text=file_get_contents($Archivo);
      $Text=str_replace("\n","<br>",$Text);   
   }
   return $Text;
}
function STDOUT($Ruta_Relativa)
{
   $Text=file_get_contents($Ruta_Relativa."/Reporte_STDOUT");
   $Text=str_replace("\n","<br>",$Text);
   return $Text;
}
function ParseCalidades_Parte1($Calidades)
{
	if(!file_exists($Calidades)) return false;
$Text=file_get_contents($Calidades);

$Complete= '{"cols": [
{"id":"","label":"Topping","pattern":"","type":"string"},
{"id":"Total","label":"Min","pattern":"","type":"number"},
{"id":"Unique","label":"Max","pattern":"","type":"number"},
{"id":"Repeated","label":"Mean","pattern":"","type":"number"},
],"rows": [';

foreach(explode("\n",$Text) as $Row)
{
$Cell=explode("\t",$Row);
if($Cell[0]!="column" && $Cell[0]!="")
{
	$Complete.='{"c":[{"v":"'.$Cell[0].'","f":null},{"v":"'.$Cell[2].'","f":null},{"v":'.$Cell[3].',"f":null},{"v":'.$Cell[5].',"f":null}]},';
}

}
$Complete=substr($Complete,0,-1);
$Complete.=']}';
return $Complete;
}
function ParseCalidades_Parte2($Calidades)
{
$Text=file_get_contents($Calidades);

$Complete= '{"cols": [
{"id":"","label":"Topping","pattern":"","type":"string"},
{"id":"Repeated","label":"Q1","pattern":"","type":"number"},
{"id":"Repeated","label":"Median","pattern":"","type":"number"},
{"id":"Repeated","label":"Q3","pattern":"","type":"number"}
],"rows": [';

foreach(explode("\n",$Text) as $Row)
{
$Cell=explode("\t",$Row);
if($Cell[0]!="column" && $Cell[0]!="")
{
	$Complete.='{"c":[{"v":"'.$Cell[0].'","f":null},{"v":"'.$Cell[6].'","f":null},{"v":'.$Cell[7].',"f":null},{"v":'.$Cell[8].',"f":null}]},';
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
if($Cell[0]!="column" && $Cell[0]!="")
{
	$Total=$Cell[12]+$Cell[13]+$Cell[14]+$Cell[15]+$Cell[16];
	$Complete.='{"c":[{"v":"'.$Cell[0].'","f":null},{"v":'.$Cell[12]/$Total.',"f":null},{"v":'.$Cell[13]/$Total.',"f":null},{"v":'.$Cell[14]/$Total.',"f":null},{"v":'.$Cell[15]/$Total.',"f":null},{"v":'.$Cell[16]/$Total.',"f":null}]},';
	
}

}
$Complete=substr($Complete,0,-1);
$Complete.=']}';
return $Complete;
}
?>
