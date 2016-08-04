	<?php
include("Procesos_Derecho_Server.php");
switch($_REQUEST['Origen'])
{
case "Ejecutar":
Ordenar_Campos();
break;
case "Genomas":
Genomas_Disponibles();
break;
}
function Ordenar_Campos()
{
   session_start();
   $Ruta=$_SESSION['Ruta'];
   
	Limpiar_Directorio($Ruta, $_SESSION['Archivo']);
	$Ruta_Archivo=$Ruta."/".$_SESSION['Archivo'];
	//Se filtra una longitud máxima de 18
	//Filtrar_Longitud_Reads($Ruta_Archivo);
	Bowtie($Ruta_Archivo, $Ruta);
	Almacenar_BD($Ruta);
	Preparar_JBrowse($Ruta);
	Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);
}
function Filtrar_Longitud_Reads($Ruta_Archivo)
{
	$Comando="";
		switch(basename($Ruta_Archivo))
		{
		  case "File_Fasta":
			$Comando="./../bin/tally -i ".$Ruta_Archivo." ";
			$Comando.="--fasta-in --fasta-out --no-tally ";
		  break;
		  case "File_FastaQ":
			$Comando="./../bin/tally -i ".$Ruta_Archivo." ";
			$Comando.="--no-tally --with-quality";
		  break;
		}
		$Comando.="-o ".$Ruta_Archivo."tmp -l 18 --nozip ";
		 Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
		 $Comando="mv ".$Ruta_Archivo."tmp $Ruta_Archivo";
		 Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
}
function Almacenar_BD($Ruta)
{
	$Comando="";
	$Id_Cuenta=$_SESSION['Id_Cuenta'];
	$Ruta_SAM="$Ruta/File.sam";
	$Ruta_SAM_Salida="$Ruta/File_CLEAN.sam";
	$Id_Libreria=$_SESSION['Id_Libreria'];
	$Id_Genoma=$_POST['Select_Genoma'];
	$Ruta_GFF="./../Indexes/$Id_Genoma/Anotacion/GFF.gff";	
	//Cálculos para generar archivo de almacenamiento a la base de datos
	//y se crean la información lara los tracks de JBrowse

	$Comando="./../Scripts/Mapping.sh $Ruta_SAM $Ruta_SAM_Salida $Ruta_GFF $Id_Libreria $Id_Genoma $Ruta ";
	
	 Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
	//Almacenar la informarción de los cálculos
	$Comando="./../Scripts/Insert.sh ".$Ruta_SAM_Salida."_DATABASE Molecula$Id_Cuenta ";
	 Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
	
}
function Preparar_JBrowse($Ruta)
{
   	$Comando="";
   	$Id_Genoma=$_POST['Select_Genoma'];
   	$Ruta_Genoma="Indexes/$Id_Genoma";
   	$Id_Libreria=$_SESSION['Id_Libreria'];
   	$Id_Cuenta="Usuario".$_SESSION['Id_Cuenta'];
   	$Nombre_Libreria=$_SESSION['Nombre_Libreria'];
   	$Comando="./../Scripts/automatic_processing_wigglefiles.sh $Ruta $Ruta_Genoma ";
   	 Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
   	$Comando="./../Scripts/Import_JBrowse.sh $Ruta ./../Indexes/$Id_Genoma/JBrowse $Id_Libreria ";
   	 Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
   	$Comando="./../Scripts/TrackBigWig $Ruta $Ruta_Genoma $Id_Libreria $Id_Cuenta $Nombre_Libreria ";
	Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
 }
function Bowtie($Ruta_Archivo, $Ruta)
{
		$Genoma = $_POST['Select_Genoma'];
	    $Comando="bowtie ";
	    ($_POST['Tipo_Reporte']=="-k")?$Comando.="-k ".$_POST['Opcion_k']:$Comando.=$_POST['Tipo_Reporte']." ";
		if(!empty($_POST['Opcion_m']))$Comando.="-m ".$_POST['Opcion_m']." ";
		if(!empty($_POST['Opcion_M']))$Comando.="-M ".$_POST['Opcion_M']." ";
		if(!empty($_POST['Check_best']))$Comando.=$_POST['Check_best']." ";
		if(!empty($_POST['Check_strata']))$Comando.=$_POST['Check_strata']." ";
		$Comando.=$_POST['Tipo_Alineacion']." ";
		if(!empty($_POST['Alineacion_n']))$Comando.=$_POST['Alineacion_n']." ";
		if(!empty($_POST['Opcion_l']))$Comando.='-l '.$_POST['Opcion_l']." ";
		if(!empty($_POST['Opcion_e']))$Comando.='-e '.$_POST['Opcion_e']." ";
	
		$Genoma=basename($_POST['Select_Genoma']);
		
		switch(basename($Ruta_Archivo))
		{
		  case "File_Fasta":
			$Comando.="./../Indexes/".$_POST['Select_Genoma']."/Illumina/".$Genoma." -f ".$Ruta_Archivo." ";
		  break;
		  case "File_FastaQ":
		  $Comando.="./../Indexes/".$_POST['Select_Genoma']."/Illumina/".$Genoma." -q ".$Ruta_Archivo." ";
		  break;
		  case "File_CSFastaQ":
			$Comando.="./../Indexes/".$_POST['Select_Genoma']."/Solid/".$Genoma."_C -C ".$Ruta_Archivo." ";
		  break;
		}
		$Comando.="-S ".$Ruta."/File.sam -p 1 --un ".$Ruta."/No_Alignment ";
		
	    Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 1);
	
}
function Genomas_Disponibles()
{
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Genoma G inner join Organismo O on G.Id_Organismo = O.Id_Organismo", $Conexion);
    echo '<option value="" disabled="disabled" id="dropdown"	 required selected="selected">Please select a Genome</option>';
			while($row = mysql_fetch_assoc($Resultado))
			{
				echo '<option value="'.$row['Id_Genoma'].'"  id="dropdown" >'.$row['Organismo']." (".$row['Nombre']."_".$row['Version'].")".'</option>';            
			}
 Cerrar_Conexion($Conexion);
}
?>
