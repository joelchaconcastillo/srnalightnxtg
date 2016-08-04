<html>
<title>Process Transcriptome</title>
<head>
	
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="js/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="js/jquery.balloon.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>-->
<script src="js/jquery.balloon.js"></script>
<script src="js/sonic.js"></script>
<script src="js/Core.js"></script>
<script src="js/Controles.js"></script>
<script src="js/Send.js"></script>
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
	width:80px;
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
</style>
</head>
<script>
//Path="Users/prueba/1";
/*$("#Path").attr("id",Name);
	$("#"+Name).val(Path);*/
	
$(document).ready(function(e) {
$.post("Load_JBrowse_Server.php",{Origen:"Genomas"},function(data)
	{
		$("#Genoma").html(data);
	});
	
	$("input[name=Proceso]:radio").change(
	function()
	{
		if($(this).val()=="All")
		{
			$("#Trimm").slideDown();
			$("#Filter").slideDown();
		}
		else
		{
			$("#Trimm").slideUp();
			$("#Filter").slideUp();
		}
	});
});
	
</script>
<body  >
<div id="Automatic">
<?php
//Primero se obtienen los valores por medio de post y se convierten a un onjeto de tipo JSON
 $Path=$_POST['Path'];
 $Id_Libreria=$_POST['Id_Libreria'];
 $Tabla=$_POST['Tabla'];
 ?>
<!-----///////////INGRESAR INFORMACIÓN DEL ADAPTADOR//////////////-->
<form id="Form_Automatic" >
	<input type="text" name="Path" style="display:none" value="<?php echo $Path ?>">
	<input type="text" name ="Id_Libreria" style="display:none" value="<?php echo $Id_Libreria ?>">
	<input type="text" name ="Tabla" style="display:none" value="<?php echo $Tabla ?>">
	<input type="text" name="Origen" style="display:none" value="Upload">
	<br>
		<input type="radio" name="Proceso" value="All" checked><label>Stages linker, filter and bowtie</label>
		<input type="radio" name="Proceso" value="Bowtie"><label>Only Bowtie</label><br><br><br>
		<fieldset id="Trimm"  class="Window">
		<legend>Linker Configuration</legend>
		<div id="Content_Adaptador" >
		<div class="Help" id="Help_Geometry">?</div><label>Geometry:</label>
		<select id="Geometria" name="Geometria" onChange="Disabled_Geometry('Automatic')">
		<option value="" disabled="disabled" required selected="selected">Please select a Geometry</option>
		<option value="no-bc">no_barcode</option>
		<option value="3p-bc">3p_barcode</option>
		<option value="5p-bc">5p_barcode</option>
		</select><br><br>
		Alignment options
		<br>
		Options to specify when part of an alignment triggers a match:
		<br>
		<div class="Help" id="Help_Adaptador_3">?</div><label >Adapter 3':</label><input type="text" name="Adaptador_3" id="Adaptador_3">
		<br>
		<div class="Help" id="Help_Barcode">?</div><label >Barcode:</label><input type="text" name="Barcode" id="Barcode">
		<br>
		<div class="Help" id="Help_Tabu">?</div><label >Tabu:</label><input type="text" name="Tabu" id="Tabu">
		<br>
		<div class="Help" id="Help_Insert_Adaptador_3">?</div><label >Insert sequence 3':</label><input type="text" name="Insert_Adaptador_3" id="Insert_Adaptador_3" >
		<br>
		<div class="Help" id="Help_Insert_Adaptador_5">?</div><label >Insert sequence 5':</label><input type="text" name="Insert_Adaptador_5" id="Insert_Adaptador_5">
		<br><br>
		<input type="button" value="Criteria for Geometry" id="btn_Geometry" onClick="Show_Geometries('Automatic')" >
		<br>
		<fieldset id="Content_Geometries" style="display:none; width:85%">
			
		<fieldset>
		<legend><div class="Help" id="Help_specification" >?</div>Alignment Tests:</legend>
		<div class="Help" id="Help_3p_global">?</div><label>3p-global:</label><input type="text" name="T_3p_global"><br>
		<div class="Help" id="Help_3p_prefix">?</div><label>3p-prefix:</label><input type="text" name="T_3p_prefix"><br>
		<div class="Help" id="Help_3p_barcode">?</div><label>3p-barcode:</label><input type="text" name="T_3p_barcode"><br>
		<div class="Help" id="Help_5p_barcode">?</div><label>5p-barcode:</label><input type="text" name="T_5p_barcode"><br>
		<div class="Help" id="Help_5p_sinsert">?</div><label>5p-sinsert:</label><input type="text" name="T_5p_sinsert"><br>
		<div class="Help" id="Help_Mr_tabu">?</div><label>Mr-tabu:</label><input type="text" name="Mr_tabu"><br>
		<div class="Help" id="Help_3p_head_to_tail">?</div><label>3p-head-to-tail:</label><input type="text" name="T_3p_head_to_tail"><br>
        Options l/e/g/o
		</fieldset>
		<div style="display:inline-block; width:500px">
		<fieldset>
		<legend>Quality:</legend>
		<div class="Help" id="Help_qqq_check">?</div><label>qqq-check:</label><input type="text" name="qqq_check">
		</fieldset>
		<fieldset>
		<legend>N-Masked Bases:</legend>
		<div class="Help" id="Help_nnn_check">?</div><label>nnn-check</label><input type="text" name="nnn_check">
		</fieldset>
		<fieldset>
		<legend>Low Complexity Sequence:</legend>
		<div class="Help" id="Help_dust_suffix">?</div><label>dust-suffix:</label><input type="text" name="dust_suffix"><br>
		<div class="Help" id="Help_dust_suffix_late">?</div><label>dust-suffix-late:</label><input type="text" name="dust_suffix_late">
		</fieldset>
		<fieldset>
		<legend>Length-based Filtering:</legend>
		<div class="Help" id="Help_clean_length">?</div><label>clean-length</label><input type="text" name="clean_length">
		</fieldset>
        <fieldset>
		<legend>Other options:</legend>
       
		<div class="Help" id="Help_tri">?</div><label>tri</label><br><input type="text" name="tri"><br>
        <div class="Help" id="Help_tri_length">?</div><label>tri_length</label><input type="text" name="tri_length"><br>
        <div class="Help" id="Help_polya">?</div><label>polya</label><input type="text" name="polya"><br>
        <div class="Help" id="Help_sc_max">?</div><label>sc-max</label><input type="text" name="sc_max">
         <div style="display:block">
        <div class="Help" id="Help_bcq_early">?</div><label>bcq-early</label><input type="checkbox" name="bcq_early"></div><br>
        <div style="display:block">
        <div class="Help" id="Help_bcq_late">?</div><label>bcq-late</label><input type="checkbox" name="bcq_late"></div><br>
        <div style="display:block">
       <div class="Help" id="Help_full_length">?</div> <label>full-length</label><input type="checkbox" name="full_length"></div><br>
		
        </fieldset>
		</div>
		</fieldset>
		<br>
		</div>
		</fieldset>
<fieldset id="Filter"  class="Window" >
<legend>Filter Configuration</legend>
<div id="RealTime_Adapter" class="RealTime">
</div>
<br>
<label>Accepted Tri-nucleotide score low than:</label><input type="text" name="Low" id="Low"><br>
<label>Min Size length:</label><input type="text" name="Min_Size" id="Min_Size"><br>
<label>Max Size length:</label><input type="text" name="Max_Size" id="Max_Size"><br>
<label>Remove "n" nucleotides of five section:</label><input type="text" name="Five" id="Five"><br>
<label>Remove "n" nucleotides of three section:</label><input type="text" name="Three" id="Three"><br>
<label>Remove repeated sequences after filter</label><input type="checkbox" name="Filter_Repeated" ><br>

<br>
</fieldset>

<fieldset id="Bowtie" class="Window">
<legend>Bowtie Configuration</legend>
<input type="button" value="Use console" id="btn_toggle_bowtie" onClick="Toggle_Bowtie('Automatic')">
<fieldset id="Interface">
<legend>Interface</legend>
<div id="Select_Genome">
<label>Genome:</label>
<br>
<select name="Select_Genoma" id="Genoma">
 <option value="" disabled="disabled" id="dropdown" required selected="selected">Please select a Genome</option>
</select>
</div>
<fieldset>
	<legend>Alignment</legend>

<input type="radio" name="Tipo_Alineacion"  value="-v" checked><label>Report alignment with mismatches</label>
<br>
<input type="radio" name="Tipo_Alineacion"  value="-n"><label>Maximum number of mismatches permitted in the seed</label><input type="text" value="2" name="Alineacion_n" id="n_text">
<br>
<label>-l</label>
<input type="checkbox"  onChange="Check('l_text','Automatic')"  id="l" value="-l">
<label>The seed length</label>
<input type="text" name="Opcion_l" id="l_text" disabled id="Longitud_Semilla">
<br>
<label>-e</label><input type="checkbox" id="e"  onChange="Check('e_text','Automatic')"  value="-e"/>Maximum permitted total of quality values at all mistmatches read positions<input type="text" disabled="false" name="Opcion_e" id="e_text" name="e_text" >
<br>
</fieldset>
<fieldset>
<legend>Content_Reporting</legend>
<label>Reporting</label>
<br>
<div id="Content_Interfaz">
-a <input type="radio" value="-a" checked="checked" onChange="Check('k_text','Automatic')" name="Tipo_Reporte" /> Report all valid alignments per read or pair<br>
-k <input type="radio" value="-k" id="K" onChange="Check('k_text','Automatic')"  name="Tipo_Reporte" />
Valid alignments per read or paid 
   		<input type="text" name="Opcion_k"  id="k_text" disabled="false" size="1"/><br>
-m <input type="checkbox" value="-m" onChange="Check('m_text','Automatic')"  />Output no alignments if the report have more alignments than:  <input type="text" name="Opcion_m" id="m_text" disabled="false" name="m_text" size="1"/><br>
-M <input type="checkbox" value="-M" onChange="Check('ML_text','Automatic')"  />Report one at random if the report have more alignments than:  <input type="text" name="Opcion_M" id="ML_text" disabled="false" name="ML_text" size="1"/><br>
-best <input type="checkbox" value="--best" id="best_check" checked="checked" name="Check_best"  />Report the alignment in order "best-to-worse"<br>
-strata <input type="checkbox" value="--strata" id="strata_check" name="Check_strata"  />Report only those alignments that fall into the best stratum
</fieldset>
</fieldset>
<fieldset id="Console" style="display:none" >
<legend>Console</legend>
<br> Remember doesn't write the path of the end.. <br>
Example: "	bowtie -a -n 2 --best"
<br>With commands:<br>
<textarea cols="80" rows="5" name="TerminalBowtie" id="TerminalBowtie">bowtie</textarea>
</fieldset>
</fieldset>
</fieldset>
<fieldset id="Uploadtittle">
	<legend>Upload File</legend>
<div id="Content_Subir">
<br>
<input type="file" id="Subir_Archivo" onChange="Send_File_Automatic(this,'Automatic')">
<div id="fileName"></div>
<div id="fileSize"></div>
<div id="fileType"></div>
<br>
<progress id="Progreso" style="z-index:100000; position:relative"></progress>
<div id="Progreso_Numero" style="z-index:1000000; color:black; position:relative"></div>
<br>
<br>
<input type="button" value="I want to specity the format input" id="Patron_btn" onClick="Show_Rules('Automatic');">
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
</div>
</body>
</html>
