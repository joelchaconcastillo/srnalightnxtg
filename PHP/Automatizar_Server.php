<?php
date_default_timezone_set("America/Mexico_City");
include("MySQL.php");
include("Identificar.php");
switch($_REQUEST['Origen'])
{
case "Upload":
Start();
break;
}
function Start()
{
				$Path=NULL;$RutaRelativa=NULL;$Extension=NULL;
				$RutaRelativa=$_POST['Path'];
				if(!is_dir($RutaRelativa)) mkdir($RutaRelativa,0777);
				$Path=$RutaRelativa."/".$_FILES["fileUpload"]["name"];
				move_uploaded_file($_FILES["fileUpload"]["tmp_name"],$Path);
				$Datos=CheckFiles($RutaRelativa,$Path,$_POST['Patron'],$_POST['Analizar']);		
				$Datos['RutaRelativa']=$RutaRelativa;
				if($_POST['Proceso']=="All")
				{
				$Datos=Linker_Configuration($Datos);
			    $Datos=Filter_Configuration($Datos);
			    }
			    $Datos=Bowtie_Configuration($Datos);
				$Datos=After_Bowtie($Datos);
				shell_exec("( ".$Datos['Comando']." ) > /dev/null &");
				
				//chmod($Datos['RutaRelativa']."/reaper", 0777); 
				
}	
function After_Bowtie($Datos)
{				
		$Genoma=basename($_POST['Select_Genoma']);
		$Version=array_pop(split('_',$Genoma));
		$Genoma=substr($Genoma,0,-1*(strlen($Version)+1));
		$conexion = Abrir_Conexion(); 
		$q = mysql_query("SELECT * FROM Genoma WHERE Nombre = '$Genoma' AND Version= '$Version' ",$conexion);
		$row =mysql_fetch_assoc($q);

		Cerrar_Conexion($conexion);
				
				$Comando='chmod -R 0777 '.$Datos['Path']." ;";
				$Comando.="./Scripts/Limpiar.sh ".$Datos['Path']."/File.sam ".$Datos['Path']."/File_clean.sam ; ";
			/////Generando el reporte entre la librería y el genoma
				$Comando.="./Scripts/Mapping.sh ".$Datos['Path']."/File_clean.sam ".$Datos['Path']."/Mapping ".$Datos['Genoma']."/Anotacion/Biomart ".$_POST['Id_Libreria']." ".$row['Id_Genoma']." ".$Datos['Path']." ; ";
		///////Insertando valores en la base de datos
				$Comando.="./Scripts/Insert.sh ".$Datos['Path']."/Mapping_BD ".$_POST['Tabla']." ; ";
		/////////////////////////JBrowse////////////////////
				$Comando.="./Scripts/Import_JBrowse.sh ".$Datos['Path']." ".$Datos['Genoma']."/JBrowse ".$_POST['Id_Libreria'].";";		
				$Comando.="chmod -R 777 ".$Datos['Path']." ;";
				$Comando.="./Scripts/automatic_processing_wigglefiles.sh ".$Datos['Path']." ".$Datos['Genoma']."/Anotacion ".$Datos['Path']."/BigWig ".$Datos['Genoma']."/JBrowse/".$_POST['Id_Libreria']."trackList.json ;"; 
				$Datos['Comando'].=$Comando;
				$Datos['Comando'].='chmod -R 0777 '.$Datos['Path']." ;";
				$Datos['Comando'].='php Email.php '.Email().' '.$Datos['Path'];
				file_put_contents($_POST['Path'].'/One', $Datos['Comando']);
				return $Datos;
	
}
function Email()
{
	session_start();
	$conexion = Abrir_Conexion(); 
	$q = mysql_query("SELECT * FROM Cuentas WHERE Id_Cuentas = ".$_SESSION['Id_Cuenta'],$conexion);
	$row =mysql_fetch_assoc($q);

	Cerrar_Conexion($conexion);
	return $row['Email']; 
}
function Linker_Configuration($Datos)
{
	$meta="barcode\t3p-ad\ttabu\t3p-si\t5p-si\n";
	$Datos['Patron']=Tipo_Patron($Datos);
	$Datos=Hibrido($Datos);
	mkdir($Datos['RutaRelativa']."/reaper",0777);
	mkdir($Datos['RutaRelativa']."/filter",0777);
	$Comando='./bin/reaper -i '.$Datos['Archivo'].' -basename '.$Datos['Path'].'/reaper/Trimm ';
	//Configuración de las geometrías..
	
		////Aligment Tests
		if(!empty($_POST['T_3p_global']))$Comando.='-3p-global '.$_POST['T_3p_global'].' ';
		if(!empty($_POST['T_3p_prefix']))$Comando.='-3p-prefix '.$_POST['T_3p_prefix'].' ';
		if(!empty($_POST['T_3p_head_to_tail']))$Comando.='-3p-head-to-tail '.$_POST['T_3p_head_to_tail'].' ';
		if(!empty($_POST['T_3p_barcode']))$Comando.='-3p-barcode '.$_POST['T_3p_barcode'].' ';
		if(!empty($_POST['T_5p_barcode']))$Comando.='-5p-barcode '.$_POST['T_5p_barcode'].' ';
		if(!empty($_POST['T_5p_sinsert']))$Comando.='-5p-sinsert '.$_POST['T_5p_sinsert'].' ';
		if(!empty($_POST['Mr_tabu']))$Comando.='-mr-tabu '.$_POST['Mr_tabu'].' ';
		//
		////Quality
		if(!empty($_POST['qqq_check']))$Comando.='-qqq-check '.$_POST['qqq_check'].' ';
		////N-Masked-Bases
		if(!empty($_POST['nnn_check']))$Comando.='-nnn-check '.$_POST['nnn_check'].' ';
		////Length based Filtering
		if(!empty($_POST['clean_length']))$Comando.='-clean-length '.$_POST['clean_length'].' ';
		////Low complexity
		if(!empty($_POST['dust_suffix']))$Comando.='-dust-suffix '.$_POST['dust_suffix'].' ';
		if(!empty($_POST['dust_suffix_late']))$Comando.='-dust-suffix-late '.$_POST['dust_suffix_late'].' ';
		////Other options
		if(!empty($_POST['tri']))$Comando.='-tri '.$_POST['tri'].' ';
		if(!empty($_POST['tri_length']))$Comando.='-trim-length '.$_POST['tri_length'].' ';
		if(!empty($_POST['polya']))$Comando.='-polya '.$_POST['polya'].' ';
		if(!empty($_POST['sc_max']))$Comando.='-sc-max '.$_POST['sc_max'].' ';
		if(!empty($_POST['bcq_early']))$Comando.='--bcq-early ';
		if(!empty($_POST['bcq_late']))$Comando.='--bcq-late ';
		if(!empty($_POST['full_length']))$Comando.='--full-length ';
		
		//Metafile geometry
		if(preg_match("/Q/",$Datos['Patron']))
		$Comando.='-geom '.$_POST['Geometria'].' -meta '.$_POST['Path'].'/meta -format-clean "@%X_t%T_w%L%n%C%n+%n%Q%n" --nozip -record-format "'.$Datos['Patron'].'"';
		else
		$Comando.='-geom '.$_POST['Geometria'].' -meta '.$_POST['Path'].'/meta -format-clean ">%X_t%T_w%L%n%C%n" --nozip -record-format "'.$Datos['Patron'].'"';
		$meta.=(!empty($_POST['Barcode']))?$_POST['Barcode']."\t":"-\t";
		$meta.=(!empty($_POST['Adaptador_3']))?$_POST['Adaptador_3']."\t":"-\t";
		$meta.=(!empty($_POST['Tabu']))?$_POST['Tabu']."\t":"-\t";
		$meta.=(!empty($_POST['Insert_Adaptador_3']))?$_POST['Insert_Adaptador_3']."\t":"-\t";
		$meta.=(!empty($_POST['Insert_Adaptador_5']))?$_POST['Insert_Adaptador_5']:"-";	
		file_put_contents($_POST['Path'].'/meta', $meta);
		
		$Datos['Comando']=$Comando.";chmod -R 777 ".$Datos['Path']." ; mv ".$Datos['Path']."/reaper/Trimm.lane.clean ".$Datos['Archivo']." ; "; 
        
		return $Datos;
		
		
}
function Filter_Configuration($Datos)
{
	$Comando='./bin/tally -i '.$Datos['Archivo'].' ';
	(!empty($_POST['Low']))?$Comando.='-tri '.$_POST['Low'].' ':true;
	(!empty($_POST['Min_Size']))?$Comando.='-l '.$_POST['Min_Size'].' ':true;
	(!empty($_POST['Max_Size']))?$Comando.='-u '.$_POST['Max_Size'].' ':true;
	if(preg_match("/Q/",$Datos['Patron']))
	$Comando.='-o '.$_POST['Path'].'/tempFile.clean --nozip -format "@%I_t%T_w%L_x%C%n%R%n+%n%Q%n" ';		
	else
	$Comando.='-o '.$_POST['Path'].'/tempFile.clean --nozip --fasta-in -format ">%I_t%T_w%L_x%C%n%R%n" ';	
    if(empty($_POST['Filter_Repeated']))
	$Comando.='--no-tally ';
	
	$Comando.='; mv '.$_POST['Path'].'/tempFile.clean '.$Datos['Archivo'].'; ';
	
	if(empty($_POST['Five']) || empty($_POST['Three']))
	{
		$Comando.='perl Scripts/Filtro.pl --Ruta '.$_POST['Path'].' --FileIn '.$Datos['Archivo'].' --Dir filter ';
		if(!preg_match("/Q/",$Datos['Patron']))
		$Comando.='--Fasta yes ';
		if(!empty($_POST['Five']))$Comando.='--5p '.$_POST['Five'].' ';
		if(!empty($_POST['Three']))$Comando.='--3p '.$_POST['Three'].' ';
		$Comando.='--FileOut '.$_POST['Path'].'/tempFile.clean ';
		$Comando.='; mv '.$_POST['Path'].'/tempFile.clean '.$Datos['Archivo'];
	}
	
	$Datos['Comando'].=$Comando."; ";
	return $Datos;
	
}
function Bowtie_Configuration($Datos)
{
	 $Comando="bowtie ";
	 $Comando2="";
	    ($_POST['Tipo_Reporte']=="-k")?$Comando.='-k '.$_POST['Opcion_k']:$Comando.=$_POST['Tipo_Reporte'].' ';
		if(!empty($_POST['Opcion_m']))$Comando.='-m '.$_POST['Opcion_m'].' ';
		if(!empty($_POST['Opcion_M']))$Comando.='-M '.$_POST['Opcion_M'].' ';
		if(!empty($_POST['Check_best']))$Comando.=$_POST['Check_best'].' ';
		if(!empty($_POST['Check_strata']))$Comando.=$_POST['Check_strata'].' ';
		$Comando.=$_POST['Tipo_Alineacion'].' ';
		if(!empty($_POST['Alineacion_n']))$Comando.=$_POST['Alineacion_n'].' ';
		if(!empty($_POST['Opcion_l']))$Comando.='-l '.$_POST['Opcion_l'].' ';
		if(!empty($_POST['Opcion_e']))$Comando.='-e '.$_POST['Opcion_e'].' ';
	
		$Datos['Genoma']=$_POST['Select_Genoma'];
		$Genoma=basename($_POST['Select_Genoma']);
		$Elements=array_pop(split('_',$Genoma));
		$Genoma=substr($Genoma,0,-1*(strlen($Elements)+1));
		switch(basename($Datos['Archivo']))
		{
		  case "File_Fasta":
			$Comando.=$_POST['Select_Genoma']."/Illumina/".$Genoma.' -f '.$Datos['Archivo'].' ';
		  break;
		  case "File_Tabular2":
			$Comando2="./Scripts/Convert_Format -t ".$_Datos['Path']."/File_Tabular2 -o ".$_Datos['Path']."/File_Fasta;";
			$Comando.=$_POST['Select_Genoma']."/Illumina/".$Genoma.' -f '.$Datos['Path'].'/File_Fasta ';
		  break;
		  case "File_Tabular3":
			$Comando2="./Scripts/Convert_Format -t ".$_Datos['Path']."/File_Tabular3 -o ".$_Datos['Path']."/File_Fasta;";
			$Comando.=$_POST['Select_Genoma']."/Illumina/".$Genoma.' -f '.$Datos['Path'].'/File_Fasta ';
		  break;
		  case "File_FastaQ":
		  $Comando.=$_POST['Select_Genoma']."/Illumina/".$Genoma.' -q '.$Datos['Path'].'/File_FastaQ ';
		  break;
		  case "File_FastaQH":
		  	Script.push("perl");
			$Comando2=" perl Scripts/csfastqhybrid-to-csfastq.pl -i ".$Datos['Archivo']." -o ".$Datos['Path']."/File_CSFastaQTemp;";
			$Comando2.="./Scripts/Sustituir.sh ".$Datos['Path']."File_CSFastaQTemp ".$Datos['Path']."File_CSFastaQ;";
			$Comando2.="rm ".$Datos['Path']."/File_CSFastaQTemp ".$Datos['Archivo'].";";
		    $Comando.=$_POST['Select_Genoma']."/Solid/".$Genoma.' -C '.$Datos['Path'].'/File_CSFastaQ ';
		  break;
		  case "File_CSFastaQ":
			$Comando.=$_POST['Select_Genoma']."/Solid/".$Genoma.' -C '.$Datos['Archivo'].' ';
		  break;
		  case "File_Unknown":
		if(preg_match("/Q/",$Datos['Patron']))
			{	
				$Comando2="./bin/reaper -geom no-bc -i ".$Datos['Archivo']." -record-format '".$Datos['Patron']."' -3pa '' -basename ".$Datos['Path']."/File_FastaQ --nozip;";
				$Comando.=$_POST['Select_Genoma']."/Illumina/".$Genoma.' -q '.$Datos['Path'].'/File_FastaQ.lane.clean ';
			}
			else
			{
				$Comando2="./bin/reaper -geom no-bc -i ".$Datos['Archivo']." -record-format '".$Datos['Patron']."' -3pa '' -basename ".$Datos['Path']."/File_FastaQ --nozip -format-clean '>%I%#%n%R%n' ;";
				$Comando.=$_POST['Select_Genoma']."/Illumina/".$Genoma.' -f '.$Datos['Path'].'/File_Fasta ';
				}
		  break;
		}
		$Comando.='-S '.$Datos['Path'].'/File.sam -p 1;';
		$Datos['Comando'].=$Comando2.$Comando;
		return $Datos;
}
function Tipo_Patron($Datos)
{
			
				
		switch(basename($Datos['Archivo']))
	 {
		 case "File_CSFastaQ+":
		 return "@%I%A%n%R%n+%#%Q%n";
		 
		 case "File_CSFastaQ":
		 return "@%I%A%n%R%n+%#%Q%n";
		 
		 case "File_FastaQ":
		 return "@%I%A%n%R%n+%#%Q%n";
		 
		 case "File_Fasta" :
		 return ">%I%#%R%n";
		 
		 case "File_Tabular2":
		 return "%R%t%X%n";
		 
		 case "File_Tabular3":
		 return "%I%t%R%t%X%n"; 
	 }
}
function Hibrido($Datos)
{
		 $Comando="";
	 //Juntar formatos Qual CSFasta...
		 if(basename($Datos['Archivo'])=="File_CSFastaQ+")
		 {
			 			 $Comando='./Scripts/Convert_Format -c '.$Datos['Path'].'/File_CSFasta -q '.$Datos['Path'].'/File_Qual -o '.$Datos['Path'].'/File_CSFastaQ; ';
			 $Comando.='rm '.$Datos['Path'].'/File_CSFasta; ';
			 $Comando.='rm '.$Datos['Path'].'/File_Qual; ';  
			 $Datos['Archivo']=$Datos['Path']."/File_CSFastaQ";
		 }
	  //Convertir a archivo hibrido...
	   if(basename($Datos['Archivo'])=="File_CSFastaQ")
	  {	
	 				$Comando.='perl Scripts/csfastq-to-csfastqhybrid.pl -i '.$Datos['Path'].'/File_CSFastaQ -o '.$Datos['Path'].'/File_FastaQH;';
					$Datos['Archivo']=$Datos['Path'].'File_FastaQH; ';
					
			if(isset($_POST['Barcode']))$_POST['Barcode']=LinkerSolid(strtoupper($_POST['Barcode']));
			if(isset($_POST['Adaptador_3']))$_POST['Adaptador_3']=LinkerSolid(strtoupper($_POST['Adaptador_3']));
			if(isset($_POST['Tabu']))$_POST['Tabu']=LinkerSolid(strtoupper($_POST['Tabu']));
			if(isset($_POST['Insert_Adaptador_3']))$_POST['Insert_Adaptador_3']=LinkerSolid(strtoupper($_POST['Insert_Adaptador_3']));
			if(isset($_POST['Insert_Adaptador_5']))$_POST['Insert_Adaptador_5']=LinkerSolid(strtoupper($_POST['Insert_Adaptador_5']));
	  }
 return $Datos;	
}
function LinkerSolid($linker)
{
	$Table["AA"]="A";
	$Table["CC"]="A";
	$Table["GG"]="A";
	$Table["TT"]="A";
	$Table["AC"]="T";
	$Table["CA"]="T";
	$Table["GT"]="T";
	$Table["TG"]="T";
	$Table["AG"]="G";
	$Table["CT"]="G";
	$Table["GA"]="G";
	$Table["TC"]="G";
	$Table["AT"]="C";
	$Table["CG"]="C";
	$Table["GC"]="C";
	$Table["TA"]="C";
	$cad="";
	for($i=0;$i<strlen($linker)-1;$i++ )
	{
			$cad+=$Table[$linker[$i]+$linker[$i+1]];
			
	}
	
	return $cad;
	
}
?>
