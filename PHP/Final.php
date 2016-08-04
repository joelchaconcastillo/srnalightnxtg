<html>
<title>Process Transcriptome</title>
<head>
	<!--
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="js/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="js/jquery.balloon.js"></script>
<script src="http://10.10.100.6:3306/socket.io/socket.io.js"></script>
<script src="js/socket.io-stream.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="js/jquery.balloon.js"></script>
<script src="js/sonic.js"></script>
<script src="js/Core.js"></script>
<script src="js/Controles.js"></script>
<script src="js/Send.js"></script>
<script src="js/jquery.validate.js" type="text/javascript"></script>
<script src="js/Validacion.js"></script>
-->
 <script src="js/jquery.validate.js" type="text/javascript"></script>
<script src="js/Validacion.js"></script>
<script src="js/Help.js"></script>

<style>
body
{
	text-align:center;	
}
#Content_Geometries fieldset
{
	display:inline-block;
	vertical-align:top;
}
#Content_Geometries label
{
	width:100px;
	text-align:right;
}
#Filtro_Content label
{
    width:300px;
	text-align:right;	
}
#Total_Meter{
	width:100%;
	height:5px;
	-moz-box-shadow: 0px 8px 40px #000000;
-webkit-box-shadow: 0px 8px 40px #000000;
box-shadow: 0px 8px 40px #000000;
	

}meter { -webkit-appearance: none; } //Crucial, this will disable the default styling in Webkit browsers

meter::-webkit-meter-bar {
    background: #FFF;
    border: 1px solid #CCC;
	
}
.Cerrar
{
	float:right;	
}
.Cerrar:hover
{
	cursor:pointer;
}
.Log
{
	background:#333;
	margin: 0px auto;
	color:#CCC;
	width:80%;
	overflow:auto;
	height:120px;
}
.Log_Opc
{
	display:none;
cursor:pointer;
color:rgb(168, 164, 164);
width: 170px;
text-align:center;
list-style: none;
padding: 3px;
	margin: 0px auto;
font: bold 14px verdana, sans-serif;
text-shadow: #333 3px 3px 5px;
background: -webkit-linear-gradient(top, #FFFFFF, #0B0949);
background: -moz-linear-gradient(top, #FFFFFF, #693);
-moz-border-radius: 10px;
-webkit-border-radius: 10px;
border-radius: 10px;
}
.Proceso
{
	width:190px;	
}
#Total_Progress
{
	
}
#Total_Progress li
{
	list-style:none;
	display:inline-block;
	text-shadow:5px 5px 35px #000000;
}
.RealTime
{
	z-index:9999; 
	position:relative;
	 color:green;
}
#Content_Geometries .Help
{
	float:left;
}
.Principal_Linker label
{
	text-align:right;
	width:200px;
}
</style>
<!--<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      google.load("visualization", "1", {packages:["corechart"]});
     // google.setOnLoadCallback(UrlFile);
      
    </script>-->
</head>
<?php
//Primero se obtienen los valores por medio de post y se convierten a un onjeto de tipo JSON
 $Path=$_POST['Path'];
 $Id_Libreria=$_POST['Id_Libreria'];
 $Tabla=$_POST['Tabla'];

 ?>
<script>
//Path="Users/prueba/1";
/*$("#Path").attr("id",Name);
	$("#"+Name).val(Path);*/
	$.post("Final_Server.php",{Origen:"Genomas"},function(data)
	{
		$("#<?php echo basename($Path)?>Genoma").html(data);
	});
</script>
<body >
<h3><span class="glyphicon glyphicon-tasks" style="padding-right:5px"></span>Experiment <b><?php echo basename($Path) ?></b></h3>
<div id="Content_Body_Final">
<!--//////////SUBIR ARCHIVO///////////////-->
<br>
<input type="text" id="Archivo" style="display:none">
<input type="text" id="Patron_Save" style="display:none">
<div id="Total_Progress" >
<ul>
<li><div id="Progreso_Subir" class="Proceso">Upload File</div>
<li><div id="Progreso_Adaptador" class="Proceso">Linker Configuration</div></li>
<li><div id="Progreso_Filtrar" class="Proceso">Filter Configuration</div></li>
<li><div id="Progreso_Mapear" class="Proceso">Bowtie Configuration</div></li>
<li><div id="Progreso_Almacenar" class="Proceso">Data Base Insert</div></li>
</ul>
<br>
<meter id="Total_Meter" min="0" max="100" value="0" ></meter>
</div>
<br><br>
<!--//Sección para subir archivos-->
<form id="Subir_Archivos">
<fieldset id="Uploadtittle">
	<legend>Upload File</legend>
<div id="Content_Subir">
<br>
<div class="glyphicon glyphicon-question-sign" id="Help_Flujo"></div><label>Filter File</label><input type="radio" value="Filtrar" name="Flujo" checked>
<label>Directly Mapping</label><input type="radio" value="Mapear" name="Flujo">
<br><br>
<div class="glyphicon glyphicon-question-sign" id="Help_PreAnalyze"></div><label>Pre-Analyze file</label><input type="checkbox" id="Analizar" name="Analizar" >
<br><br>
<input type="file" id="Subir_Archivo" style="margin:auto;" onChange="Send_File(this,'<?php echo $Path?>')">
<div id="fileName"></div>
<div id="fileSize"></div>
<div id="fileType"></div>
<br>
<progress id="Progreso" style="z-index:100000; position:relative"></progress>
<div id="Progreso_Numero" style="z-index:1000000; color:white; position:relative"></div>
<br>
<hr>
<label>Optionally you can indicate the URL of the file:</label>
<br><br>
<label>URL:</label><input type="text" name="URL" id="URL" > <input type="button" onClick="UrlFile('<?php echo $Path?>')" value="Go"><br>
<div id="Curl" style="z-index:99999; position:relative"></div>
<br>
<input type="button" value="I want to specity the format input" id="Patron_btn" onClick="Show_Rules('<?php echo $Path?>');">
<br>
<fieldset id="Rules" style="display:none;">
<legend>Rules to specify the format input:</legend>
<br>
<label>Pattern:</label><input type="text" id="Patron" name="Patron"><br><br>
<center>
<div id="Pattern_Table" >
<table border=1 style="color:white; background:black">
<tr>
	<th>Pattern</th><th>Mean</th>
</tr>
<tr>
	<td>%R</td><td>Expect read (longest sequence found over [a-zA-Z]*) - empty read allowed</td>
</tr>
<tr>
	<td>%I</td><td>Expect identifier (longest sequence of non-blank)</td>
</tr>
<tr>
	<td>%A</td><td>Expect overflow annotation field (initial blanks skipped, rest of line)</td>
</tr>
<tr>
	<td>%a</td><td>Use previous %F or %G or %H field as annotation</td>
</tr>
<tr>
	<td>%B</td><td>Expect annotation field (longest sequence of non-blank)</td>
</tr>
<tr>
	<td>%Q</td><td>Expect quality (longest sequence of non-blank)</td>
</tr>
<tr>
	<td>%X</td><td>Expect count (a nonnegative integer number)</td>
</tr>
<tr>
	<td>%F</td><td>Expect and discard field (longest sequence of non-tab)</td>
</tr>
<tr>
	<td>%G</td><td>Expect and discard field (longest sequence of non-blank)</td>
</tr>
<tr>
	<td>%#</td><td>Discard everything until end of line</td>
</tr>
<tr>
	<td>%b</td><td>Expect run of blanks (space or tab)</td>
</tr>
<tr>
	<td>%n</td><td>Expect end of line match</td>
</tr>
<tr>
	<td>%.</td><td>Expect and discard any character</td>
</tr>
<tr>
	<td>%s</td><td>Expect a space</td>
</tr>
<tr>
	<td>%t</td><td>Expect a tab</td>
</tr>
<tr>
	<td>%%</td><td>Expect a percent sign</td>
</tr>
</table>
</div>
<br><br>
<label>Examples:</label><br><br>
<table border ="1">
<tr><th>Format:</th><th>Pattern</th></tr>
<tr><td>FASTA:</td><td>>%I%#%R%n</td></tr>
<tr><td>FASTAQ:</td><td>@%I%A%n%R%n+%#%Q%n</td></tr>
</table>
</center>

</fieldset>
<br><br>
<fieldset>
<legend>Directory</legend>
<div id="Directorio"></div>
</fieldset>
<br><br>
<br>
</div>
</fieldset>
</form>
<!--Sección para mostrar la primer sección de gráficos-->
<fieldset id="Pre_Analized" style="display:none;">
<legend>Pre-Analized</legend>
<span style="cursor:pointer; position:relative; float:right;" class="glyphicon glyphicon-remove-circle" onClick="Cerrar('<?php echo $Path?>','Pre_Analized');" value="Close Graph"></span>
<br>
<h3>
<span onClick="Toggle_Div('<?php echo $Path?>','RealTime_Analisys')" class="glyphicon glyphicon-eye-close" style="position:relative; float:left; cursor:pointer;"></span>
</h3><br><br>
<div id="RealTime_Analisys"  class="RealTime"></div>
<div id="Analisis_Frecuencia" style="width: 600px; height: 500px; margin:auto; position:relative;"></div>
<div id="Analisis_Longitud" style="width: 600px; height: 500px; margin:auto; position:relative;"></div>
<div id="Analisis_Calidad" style="width: 1200px; height: 600px; margin:auto; position:relative;"></div>
<br>

</fieldset>
<!--///////////INGRESAR INFORMACIÓN DEL ADAPTADOR//////////////-->
<form id="Adaptador">
		<fieldset id="Trimm" style="display:none" >
		<legend>Linker Configuration</legend>
		<div id="Content_Adaptador" >
        	<div  style="position:absolute; left:0px; width:300px; height:400px; text-align:left; overflow-x:auto;" >
		<input type="button" id="btn_Calculate_Linker" onClick="Calcular_Adaptador('<?php echo $Path?>')" value="Calculate Linker (Only FastQ)"><br>
		<span id="Minion"></span>
		</div>
		<div class="Principal_Linker"><br>
		<label>Geometry:</label>
		<select id="Geometria" name="Geometria" onChange="Disabled_Geometry('<?php echo $Path?>',this.value)">
		<option value="" disabled="disabled" required selected="selected">Please select a Geometry</option>
		<option value="no-bc">no_barcode</option>
		<option value="3p-bc">3p_barcode</option>
		<option value="5p-bc">5p_barcode</option>
		</select><div class="glyphicon glyphicon-question-sign" style="padding:5px;" id="Help_Geometry"></div><br><br>
		Alignment options
		<br>
		Options to specify when part of an alignment triggers a match:
		<br>
		<label >Adapter 3':</label><input type="text" name="Adaptador_3" id="Adaptador_3"><div class="glyphicon glyphicon-question-sign" style="padding:5px;" id="Help_Adaptador_3"></div>
		<br>
		<label >Barcode:</label><input type="text" name="Barcode" id="Barcode"><div class="glyphicon glyphicon-question-sign" style="padding:5px;" id="Help_Barcode"></div>
		<br>
		<label >Tabu:</label><input type="text" name="Tabu" id="Tabu"><div class="glyphicon glyphicon-question-sign" style="padding:5px;" id="Help_Tabu"></div>
		<br>
		<label >Insert sequence 3':</label><input type="text" name="Insert_Adaptador_3" id="Insert_Adaptador_3" ><div class="glyphicon glyphicon-question-sign" style="padding:5px;" id="Help_Insert_Adaptador_3"></div>
		<br>
		<label >Insert sequence 5':</label><input type="text" name="Insert_Adaptador_5" id="Insert_Adaptador_5"><div class="glyphicon glyphicon-question-sign" style="padding:5px;" id="Help_Insert_Adaptador_5"></div>
		<br>
		<label>Remove repeated sequences after trimm</label><input type="checkbox" name="Remove" >
        </div>
		<br><br>
		<input type="button" id="Enviar_Content_Adaptador" onClick="btn_Content_Adaptador('<?php echo $Path?>')" value="Send"><br><br>
		<input type="button" value="Criteria for Geometry" id="btn_Geometry" onClick="Show_Geometries('<?php echo $Path?>')" >
		<br>
		<fieldset id="Content_Geometries" style="display:none; width:85%">
			
		<fieldset>
		<legend><div class="glyphicon glyphicon-question-sign" id="Help_specification" ></div>Alignment Tests</legend>
		<label>3p-global:</label><input type="text" name="T_3p_global"><div class="glyphicon glyphicon-question-sign" id="Help_3p_global"></div><br>
		<label>3p-prefix:</label><input type="text" name="T_3p_prefix"><div class="glyphicon glyphicon-question-sign" id="Help_3p_prefix"></div><br>
		<label>3p-barcode:</label><input type="text" name="T_3p_barcode"><div class="glyphicon glyphicon-question-sign" id="Help_3p_barcode"></div><br>
		<label>5p-barcode:</label><input type="text" name="T_5p_barcode"><div class="glyphicon glyphicon-question-sign" id="Help_5p_barcode"></div><br>
		<label>5p-sinsert:</label><input type="text" name="T_5p_sinsert"><div class="glyphicon glyphicon-question-sign" id="Help_5p_sinsert"></div><br>
		<label>Mr-tabu:</label><input type="text" name="Mr_tabu"><div class="glyphicon glyphicon-question-sign" id="Help_Mr_tabu"></div><br>
		<label>3p-head-to-tail:</label><input type="text" name="T_3p_head_to_tail"><div class="glyphicon glyphicon-question-sign" id="Help_3p_head_to_tail"></div><br>
		</fieldset>
		<div style="display:inline-block; width:500px">
            <fieldset>
            <legend>Quality:</legend>
            <label>qqq-check:</label><input type="text" name="qqq_check"><div class="glyphicon glyphicon-question-sign" id="Help_qqq_check"></div>
            </fieldset>
            <fieldset>
            <legend>N-Masked Bases:</legend>
            <label>nnn-check</label><input type="text" name="nnn_check"><div class="glyphicon glyphicon-question-sign" id="Help_nnn_check"></div>
            </fieldset>
            <fieldset>
            <legend>Low Complexity Sequence:</legend>
            <label>dust-suffix:</label><input type="text" name="dust_suffix"><div class="glyphicon glyphicon-question-sign" id="Help_dust_suffix"></div><br>
            <label>dust-suffix-late:</label><input type="text" name="dust_suffix_late"><div class="glyphicon glyphicon-question-sign" id="Help_dust_suffix_late"></div>
            </fieldset>
            <fieldset>
            <legend>Length-based Filtering:</legend>
            <label>clean-length</label><input type="text" name="clean_length"><div class="glyphicon glyphicon-question-sign" id="Help_clean_length"></div>
            </fieldset>
            <fieldset>
            <legend>Other options:</legend>
           
            <label>tri</label><input type="text" name="tri"><div class="glyphicon glyphicon-question-sign" id="Help_tri"></div><br>
           <label>tri_length</label><input type="text" name="tri_length"> <div class="glyphicon glyphicon-question-sign" id="Help_tri_length"></div><br>
           <label>polya</label><input type="text" name="polya"> <div class="glyphicon glyphicon-question-sign" id="Help_polya"></div><br>
            <label>sc-max</label><input type="text" name="sc_max"><div class="glyphicon glyphicon-question-sign" id="Help_sc_max"></div>
             <div style="display:block">
            <label>bcq-early</label><input type="checkbox" name="bcq_early"><div class="glyphicon glyphicon-question-sign" id="Help_bcq_early"></div>
        </div><br>
        <div style="display:block">
        	<label>bcq-late</label><input type="checkbox" name="bcq_late"><div class="glyphicon glyphicon-question-sign" id="Help_bcq_late"></div>
        </div><br>
        <div style="display:block">
       		<label>full-length</label><input type="checkbox" name="full_length"> <div class="glyphicon glyphicon-question-sign" id="Help_full_length"></div>
       </div><br>
		
        </fieldset>
		</div>
		</fieldset>
		<br>
		</div>
		</fieldset>
</form>
<br>
<form id="Form_Filtro">
<fieldset id="Filter" style="display:none" >
<legend>Filter Configuration</legend>
<h3>
<span onClick="Toggle_Div('<?php echo $Path?>','RealTime_Adapter')" class="glyphicon glyphicon-eye-close" style="position:relative; float:left; cursor:pointer;"></span>
</h3>
<br><br>
<div id="RealTime_Adapter" class="RealTime">
</div>
<br>
<div id="Filtro_Content">
<label>Accepted Tri-nucleotide score low than:</label><input type="text" name="Low" id="Low"><br>
<label>Min Size length:</label><input type="text" name="Min_Size" id="Min_Size"><br>
<label>Max Size length:</label><input type="text" name="Max_Size" id="Max_Size"><br>
<label>Remove "n" nucleotides of five section:</label><input type="text" name="Five" id="Five"><br>
<label>Remove "n" nucleotides of three section:</label><input type="text" name="Three" id="Three"><br>
<label>Remove repeated sequences after filter</label><input type="checkbox" name="Filter_Repeated" ><br>
<input type="button" onClick="btn_Enviar_Filtrado('<?php echo $Path?>')" value="Go">
</div>
<br>
<div id="Frecuencia_Input" style="width: 600px; height: 500px; display: inline-block;"></div>
<div id="Frecuencia_Output" style="width: 600px; height: 500px;display: inline-block;"></div>
<div id="Calidad_Input" style="width: 1200px; height: 600px;display: inline-block;"></div>
<div id="Longitud_Output" style="width: 1200px; height: 600px;display: inline-block;"></div>
<div id="Complexity_Adapter" style="width: 1200px; height: 600px; display: inline-block;"></div>
<!--<input type="button" id="PDF_Filtro" value="PDF">--><br>
<br>
</fieldset>
<fieldset id="Report_Filter" style="display:none">
<legend>Report After Filter</legend>
<h3>
<span onClick="Toggle_Div('<?php echo $Path?>','RealTime_Filter')" class="glyphicon glyphicon-eye-close" style="position:relative; float:left; cursor:pointer;"></span>
</h3>
<br><br>
<div id="RealTime_Filter"  class="RealTime">
</div>
<div id="Complexivity_Filter" style="width: 1200px; height: 600px; display: inline-block;"></div>
<div id="Longitud_Output_Filter" style="width: 1200px; height: 600px;display: inline-block;"></div>
<!--<input type="button" id="PDF_After_Filter" value="PDF">-->
</fieldset>
</form>
<form id="Form_Bowtie">
<fieldset id="Bowtie" style="display:none">
<legend>Bowtie Configuration</legend>
<div id="Select_Genome">
<label>Genome:</label>
<br>
<select name="Select_Genoma" id="<?php echo basename($Path)?>Genoma">
 <option value="" disabled="disabled" id="dropdown" required selected="selected">Please select a Genome</option>
</select>
</div>
<br>
<input type="button" value="Use console" id="btn_toggle_bowtie" onClick="Toggle_Bowtie('<?php echo $Path?>')">
<fieldset id="Interface">
<legend>Interface</legend>
<fieldset id="Alignment">
	<legend>Alignment</legend>
<input type="radio" name="Tipo_Alineacion"  value="-v" checked><label>Report alignment with mismatches (-v)</label>
<input type="radio" name="Tipo_Alineacion"  value="-n"><label>Maximum number of mismatches permitted in the seed (-n)</label>
<br>
<input type="text" value="2" name="Alineacion_n" id="n_text">
<br>
<label>The seed length (-l)</label>
<input type="text" name="Opcion_l" id="l_text"  id="Longitud_Semilla">
<br>
<label>Maximum permitted total of quality values at all mistmatches read positions (-e)</label><input type="text"  name="Opcion_e" id="e_text" name="e_text" >
<br>
</fieldset>
<fieldset>
<legend>Reporting</legend>
<br>
<div id="Content_Interfaz">
<input type="radio" value="-a" checked="checked" onChange="Check('k_text','<?php echo $Path?>')" name="Tipo_Reporte" /> <label>Report all valid alignments per read or pair (-a)</label>
<input type="radio" value="-k" id="K" onChange="Check('k_text','<?php echo $Path?>')"  name="Tipo_Reporte" />
<label>Valid alignments per read or paid (-k)</label>
<br>
<input type="text" name="Opcion_k"  id="k_text" disabled="false" size="1"/><br>
<label>Output no alignments if the report have more alignments than (-m) </label> <input type="text" name="Opcion_m" id="m_text" name="m_text" size="1"/><br>
<label>Report one at random if the report have more alignments than (-M) </label>  <input type="text" name="Opcion_M" id="ML_text" name="ML_text" size="1"/><br>
<input type="checkbox" value="--best" id="best_check" name="Check_best"/><label>Report the alignment in order "best-to-worse"(--best)</label><br>
<input type="checkbox" value="--strata" id="strata_check" name="Check_strata"><label>Report only those alignments that fall into the best stratum
(--strata )</label></fieldset>
<br><input type="button" value="Go" onClick="btn_Enviar_Content_Mapeado('<?php echo $Path; ?>','<?php echo $Tabla?>','<?php echo $Id_Libreria; ?>')">
</fieldset>
<fieldset id="Console" style="display:none" >
<legend>Console</legend>
<br> Remember doesn't write the path of the end.. <br>
Example: "	bowtie -a -n 2 --best"
<br>With commands:<br>
<textarea cols="80" rows="5" name="TerminalBowtie" id="TerminalBowtie">bowtie</textarea>
<br><input type="button" value="Go" onClick="btn_Enviar_Content_Mapeado_Terminal('<?php echo $Path; ?>','<?php echo $Tabla?>','<?php echo $Id_Libreria; ?>')">
</fieldset>


<div id="RealTime_Bowtie"  class="RealTime">
</div>

</fieldset>
</form>

</div>
</body>
</html>
