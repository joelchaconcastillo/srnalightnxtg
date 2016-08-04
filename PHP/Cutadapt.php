<?php
include("Procesos_Derecho_Server.php");
switch($_REQUEST['Origen'])
{
   case "Ejecutar":
   Ordenar_Campos();
   break;
   case "Guardar":
   Ordenar_Campos();
   break;
   case "Minion":
   Minion();
   break;
}
function Minion()
{
	session_start();
	$Ruta_Archivo=$_SESSION['Ruta']."/".$_SESSION['Archivo'];
	$Out = shell_exec("./../bin/minion search-adapter -i $Ruta_Archivo");
	echo '<table class="table table-condensed">
	<tr>
		<th>Criterion</th>
		<th>Sequence-density</th>
		<th>Sequence-density-rank</th>
		<th>Fanout-score</th>
		<th>Fanout-score-rank</th>
		<th>Prefix-density</th>
		<th>Prefix-fanout</th>
		<th>Sequence</th>
		
	</tr>
	
	<tr>';
	foreach(split("\n",$Out) as $Celda)
	{
		if($Celda == "")continue;
		$Valor=split("=",$Celda);
		echo "<td>".$Valor[1]."</td>";
		if(preg_match("/sequence=/",$Celda))
		{
			//echo '<td><input type="button" class="btn btn-primary" onclick="$(" value="Select adapter"></td>';
			echo '</tr><tr>';
		}
	}
	echo '</tr></tr></table>';
}
function Ordenar_Campos()
{
   session_start();
   $Comando="";
   Limpiar_Directorio($_SESSION['Ruta'], $_SESSION['Archivo']);
     //Ficheros de entreda y salida
   $Ruta_Archivo=$_SESSION['Ruta']."/".$_SESSION['Archivo'];
   $Comando=Cutadapt($Ruta_Archivo);
   //Procesamiento con Cutadapt
   $Comando.=" ".Formato_Cutadapt($_SESSION['Archivo'], $_SESSION['Ruta'])." -o ".$Ruta_Archivo."tmp ";
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], "1");
   $Comando="mv ".$Ruta_Archivo."tmp ".$Ruta_Archivo;
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
   Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);  
   
}
function Formato_Cutadapt($Archivo, $Ruta)
{
      if($Archivo == "File_Fasta")
         return "$Ruta/$Archivo -f fasta ";
      else if($Archivo == "File_FastaQ")
         return "$Ruta/$Archivo -f fastq ";
      else if($Archivo == "File_CSFastaQ")
         return "$Ruta/$Archivo -f fastq -c ";
      else if($Archivo == "File_CSFastaQ+")
	  {
		 rename("$Ruta/File_CSFasta","$Ruta/File_CSFasta.csfasta");
		 rename("$Ruta/File_Qual","$Ruta/File_Qual.qual");
	     return "$Ruta/File_CSFasta.csfasta $Ruta/File_Qual.qual -c ";
	  }
}
function Cutadapt()
{
	$Comando="";
	 $Comando.=" ./../bin_cutadapt/cutadapt ";
   //Opciones que influyen en como se busca el adaptador
   if($_POST['Option_a'])$Comando.="-a ".$_POST['Option_a']." "; 
   if($_POST['Option_b'])$Comando.="-b ".$_POST['Option_b']." ";
   if($_POST['Option_g'])$Comando.="-g ".$_POST['Option_g']." "; 
   if($_POST['Option_ERROR_RATE'])$Comando.="-e ".$_POST['Option_ERROR_RATE']." "; 
   if($_POST['Option_n'])$Comando.="-n ".$_POST['Option_n']." "; 
   if($_POST['Option_O'])$Comando.="-O ".$_POST['Option_O']." "; 
   if($_POST['Option_Match_Read_Wildcards'])$Comando.="--match-read-wildcards "; 
   if($_POST['Option_NO_Match_Read_Wildcards'])$Comando.="-N ";
   //Opciones para procesar las lecturas procesadas
   if($_POST['Option_Discard_Trimmed'])$Comando.="--discard-trimmed ";
   if($_POST['Option_Discard_Untrimmed'])$Comando.="--discard-untrimmed ";
   if($_POST['Minimum_Length'])$Comando.="-m ".$_POST['Minimum_Length']." ";
   if($_POST['Maximum_Length'])$Comando.="-M ".$_POST['Maximum_Length']." ";
   //Opciones adicionales para modificar las lecturas
   if($_POST['Option_q'])$Comando.="-q ".$_POST['Option_q']." ";
   if($_POST['Option_quality_base'])$Comando.="--quality-base ".$_POST['Option_quality_base']." ";
   if($_POST['Option_double_encode'])$Comando.="-d ";
   if($_POST['Option_Wildcard_File'])$Comando.="--wildcard-file ".$_SESSION['Ruta']."/".$_POST['Option_Wildcard_File']." ";
   if($_POST['Option_Too_Short_Output'])$Comando.="--too-short-output ".$_SESSION['Ruta']."/".$_POST['Option_Too_Short_Output']." ";
   if($_POST['Option_Too_Long_Output'])$Comando.="--too-long-output ".$_SESSION['Ruta']."/".$_POST['Option_Too_Long_Output']." ";
   if($_POST['Option_Untrimmed_Output'])$Comando.="--untrimmed-output ".$_SESSION['Ruta']."/".$_POST['Option_Untrimmed_Output']." ";
   return $Comando;
}
?>
