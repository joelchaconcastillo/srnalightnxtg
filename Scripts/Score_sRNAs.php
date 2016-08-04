<?php
$Archivo_Anotacion = $argv[1];
$Id_Libreria = $argv[2];
$Id_Genoma = $argv[3];
Calcular_RPM($Archivo_Anotacion);
//Se calculan los "Reads Per Million" de cada elemento correspondiente al archivo de anotación
function Calcular_RPM($Archivo_Anotacion)
{
		Calcular_FTPM($Archivo_Anotacion);
		//Se multiplica cada FTPM por cada score	
		
}
//Se genera el valor de normalización de transcriptos por millón
function Calcular_FTPM($Archivo_Anotacion)
{
	global $Id_Libreria, $Id_Genoma;
	$Subdata=array();
	$Columnas=array();
	//Asignar a cada nombre de secuencia su read y su score
	//Este paso ya está previamente calculado con tally ya que funciona de 
	//forma más eficiente
	$File_Scores = fopen($Archivo_Anotacion."_Reads_Scores", "r");
	while(!feof($File_Scores))
	{
		$Columnas=explode("\t", trim(fgets($File_Scores)));
		//Se cargan los valores los scores correspondientes a cada read en un hash
		//Formato $Columnas: GAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAG	9866
		$Subdata[$Columnas[0]]=$Columnas[1];
	}
	
	$File_Anotacion = fopen($Archivo_Anotacion, "r");
	
	//Eliminar elementos repetidos tomando como unidad el nombre de secuencia y el score
	//Formato $Columnas: GAII06_0003:3:7:3631:18435#0 GAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAGAG
	$Subdata_Unico=Array();
	$ftpm=0;
	while(!feof($File_Anotacion))
	{
		$Columnas=explode("\t", trim(fgets($File_Anotacion)));
		$Info=explode(" ", $Columnas[3]);
		$Subdata_Unico[$Info[0]."Read".$Subdata[$Info[1]]]=$Subdata[$Info[1]];
	}	
	//Se suman el score de la tabla con elementos únicos para obtener el valor de normalización RPM
	foreach($Subdata_Unico as $Score)
	{
		$ftpm+=$Score;
	}
	//Se divide un millon entre el valor de normalización FTPM
	$FTMP= 1000000/$ftpm;
	print "El valor de normalización es \n$FTMP\n";
	
	//Regresar el puntero de archivo al inicio
	rewind($File_Anotacion);
	//Generar el archivo para la base de datos
	$File_BD = fopen($Archivo_Anotacion."_DATABASE", "w");
	
	$Wiggle_hash=array();
	while(!feof($File_Anotacion))
	{
		$Columnas=explode("\t", trim(fgets($File_Anotacion)));
		$Info=explode(" ", $Columnas[3]);
		$Line="";
		$Line="NULL\t";//Id_Molecula
		$Line.=$Columnas[0]."\t";//Chr
		$Line.=$Columnas[1]."\t";//Inicio
		$Line.=$Columnas[2]."\t";//Fin
		$Line.=$Columnas[5]."\t";//Sentido
		$Line.=$Columnas[4]."\t";//Numero de missmatches
		$Line.=$Info[1]."\t";//Secuencia
		$Line.="LOCI\t";//Loci
		$Line.=$Subdata[$Info[1]]."\t";//Numero_Moleculas
		$Line.=$Columnas[8]."\t";//Tipo
		$Line.=$Columnas[14]."\t";//Id_Biologico
		$Line.=$Id_Genoma."\t";//Id_Genoma	
		$Line.=$Id_Libreria."\n";//Id_Libreria
		fwrite($File_BD, $Line);
		//Generar los wiggle-files en el orden: Secuencia, Chr, Inicio, Sentido, RPM
		$Wiggle=$Info[1]."\t";
		$Wiggle.=$Columnas[0]."\t";
		$Wiggle.=$Columnas[1]."\t";
		$Wiggle.=$Columnas[5]."\t";
		$Wiggle.=$Subdata[$Info[1]]."\n";
		$Wiggle_hash[$Columnas[0]][]=$Wiggle;
		
	}
	
	foreach($Wiggle_hash as $Key =>$Chr)
	{
		$File_Wiggle = fopen($Archivo_Anotacion."$Key.prewigglefile", "w");
		foreach($Chr as $Line)
		fwrite($File_Wiggle, $Line);
		fclose($File_Scores);
	}
	
	fclose($File_Anotacion);
	fclose($File_BD);
}

?>
