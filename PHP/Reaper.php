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
}
function Ordenar_Campos()
{
   session_start();
   Limpiar_Directorio($_SESSION['Ruta'], $_SESSION['Archivo']);
    //Ficheros de entreda y salida
   $Ruta_Archivo=$_SESSION['Ruta']."/".$_SESSION['Archivo'];
   $Comando=Linker_Configuration($Ruta_Archivo, $_SESSION['Ruta']);
  
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 1);
    $Comando='  mv '.$Ruta_Archivo.'.lane.clean '.$Ruta_Archivo.' ';
   Agregar_Comando($Comando, $_SESSION['Id_HTOP'], 0);
   Generar_Pipe($_SESSION['Id_HTOP'], $_SESSION['Ruta'], $Ruta_Archivo);

      
}
function Linker_Configuration($Ruta_Archivo, $Ruta)
{
	$Comando='./../bin/reaper -i '.$Ruta_Archivo.' -basename '.$Ruta_Archivo.' ';
	
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
	    $meta="barcode\t3p-ad\ttabu\t3p-si\t5p-si\n";
		$Comando.='-geom '.$_POST['Geometria'].' -meta '.$Ruta.'/meta --nozip ';
		$meta.=(!empty($_POST['Barcode']))?$_POST['Barcode']."\t":"-\t";
		$meta.=(!empty($_POST['Adaptador_3']))?$_POST['Adaptador_3']."\t":"-\t";
		$meta.=(!empty($_POST['Tabu']))?$_POST['Tabu']."\t":"-\t";
		$meta.=(!empty($_POST['Insert_Adaptador_3']))?$_POST['Insert_Adaptador_3']."\t":"-\t";
		$meta.=(!empty($_POST['Insert_Adaptador_5']))?$_POST['Insert_Adaptador_5']:"-";	
		file_put_contents($Ruta.'/meta', $meta);
	        return $Comando;	
		
}
?>
