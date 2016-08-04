$(document).ready(function(e) {
//Sección del script kraken.php
////Formulario en el que se suben archivos
 Balloon_Help("Help_Flujo","<div >These options indicate the flow of the processing if you want process the files select 'Filter File' but if you want use directly bowtie select 'Directly Mapping.' </div>","left");
 Balloon_Help("Help_PreAnalyze","<div > This option will show you the graphs Frecuency, Quality and Length of your data examples:</div><br> <img height='200' width='250' src=\"Images/Frecuencias.png\"><img  height='200' width='250' src=\"Images/Calidades.png\"><br></div>","bottom");
////Formulario del Trimm

 Balloon_Help("Help_Geometry","<div >Three geometries are supported. A geometry is a description of what a read looks like, i.e. the read design. The supported geometries are described in greater detail below.<br><br> In the read representation below, a part enclosed in () indicates that the part must be present in the read, a part enclosed in [] is optional. A part enclosed in <> should not be present. This is unique to the 5' adapter part of a read, as the sequencing primer anneals to the 5' adapter, and hence the latter should not be seen in the raw read. Currently we have no means to strip a read of a such a match and proceed. The occurrence of this phenomenon and the quality of the remaining read should be quantified and any prospected gain evaluated before such a scheme is implemented.</div>","bottom");//<img  height='200' width='600' src=\"Images/Geometria.png\">
 Balloon_Help("Help_Adaptador_3","<div >It is the 3 prime adapter.</div><br>","left");
 Balloon_Help("Help_Barcode","<div >For the 3p-bc geometry, reaper constructs the concatenation of the sequences in columns 3p-si, barcode, and 3p-ad (note: 3p-si may be empty).<br>For the 5p-bc geometry, a matching barcode at the moment is required to be present at the beginning of the read. Mismatches may occur and the extent and types of allowable mismatches are under control of the user.<br>Examples: ACTA OR ACTA,CTAA</div>","left");
 Balloon_Help("Help_Tabu","<div >For all geometries the tabu column is treated the same. It may be empty, or it may contain one stretch of sequence, or a list of comma-separated sequences. If a sequence is specified, it will be aligned to the read. If a match is found that exceeds the requirements as specified by the program option -mr-tabu ( Defined into \"Criteria for Geometry\" Below ), the read is discarded. There is currently no provision to simply remove the matching part. We currently suggest to supply the 5' adapter sequence, if known, as a tabu sequence.</div>","right");
 Balloon_Help("Help_Insert_Adaptador_3","<div >The 3p-si column is only relevant for the 3p-bc geometry. It is part of the concatenated sequence constructed and aligned.</div>","left");
 Balloon_Help("Help_Insert_Adaptador_5","<div >For the 5p-si column, if present, an alignment is attempted against the leading part of the read. This alignment should pass the -5p-sinsert alignment criteria in order for the read to pass.</div>","left");
 //Criterios de las geometrias
 
  Balloon_Help("Help_specification","<div >The following options all take a similar form of argument. Where an X is written below, reaper ignores the corresponding parameter due to the availability of a canonical value (e.g. length of barcode). It should ordinarily not be necessary to specify the offset parameter (below given as o).<br>-3p-global l/e/g/o<br>-3p-prefix l/e/g/o<br>-3p-barcode X/e/g<br>-5p-barcode X/e/g/b<br>-5p-sinsert X/e/g/o<br>-mr-tabu l/e/g/o<br>The argument to each is of the form l/e/g/o (example: 14/2/0/0), as explained below. Generally speaking, the first, second, third and fourth positions respectively denote parameters related to length, edit distance, gap size, and offset. Exceptions exist, such as the fourth position in the -5p-barcode argument.<br>It is possible to leave out any trailing part, so l/e or 12/2 is a valid form as well. The meaning of the separate parts are as follows. The context is that of the best local alignment found for a read and an adapter sequence, to decide whether that alignment represents a true match. It is judged to represent a true match if the alignment has a subpart satisfying the following criteria.<br> l:<br>The subpart stretches over at least l bases.<br>e:<br>There are no more than e edits in the subpart.<br>g:<br>The total gap length in the subpart does not exceed g.<br>o:<br>The match occurs within an offset of o bases. The exact meaning depends on the part being matched. A zero value implies on offset requirement at all.<br><br>The fourth b field in the -5p-barcode option is a bit field. Bit 1 implies that a zip alignment is attempted first between barcode and read. A zip alignment is one where only mismatches are allowed and the beginning of the barcode is aligned precisely to the beginning of the read. This can be useful for short barcodes, where matching barcodes at an offset in the read may too easily lead to false matches. Bit 2 implies that an alignment is attempted where the latter is allowed. In this case the number of offset positions are counted as edits and contribute towards the total number of edits (that will be compared with the e part). It is possible to combine the two approaches. By default the fourth field is set to 2. For short barcodes it can be advisable to set it either to 1 or 3.<br>For adapter matching and tabu matching the offset position, if utilised, indicates how far from the start of the adapter the match is allowed to occur.<br>For -5p-sinsert the offset position indicates how far into the read the sequence insert is allowed to extend beyond the length of the sequence insert itself. For very short inserts it is useful to set this to zero or a very small value.</div>","right");

 ////Pruebas de alineación
 Balloon_Help("Help_3p_global","<div >This option specifies stringency criteria for the best alignment found between the 3' adapter and the read, anywhere. If the alignment passes the criteria, the read is considered to have adapter sequence at that position.<br>l/e[/g[/o]]  (default 14/2/1/0)</div>","right");
 Balloon_Help("Help_3p_prefix","<div >This option specifies stringency criteria for an alignment that matches the start of the 3' adapter with the end of the read. Additionally, the alignment may occur elsewhere in the read if followed by low complexity sequence. If the alignment passes the criteria, the read is considered to have adapter sequence at that position.<br>l/e[/g[/o]]  (default 8/2/0/2)</div>","right");
 Balloon_Help("Help_3p_barcode","<div >This specifies stringency criteria for the barcode section of the best alignment between the read and the concatenation 3p-si+barcode+3p-ad in the geometry 3p-bc. All barcodes are tried and the best scoring barcode is accepted. This is different from the 5' barcode case described below. The barcode is currently hardcoded to start either at the first or second base of the adapter.<br>X/e/g  (default 0/6/1/0)</div>","right");
 Balloon_Help("Help_5p_barcode","<div >This specifies stringency criteria (see below) for the alignment between a 5' barcode and the read. All barcodes are tried and the read is accepted only if a single barcode passes these criteria.<br>X/e/g/b  (default 0/0/0/2)</div>","right");
 Balloon_Help("Help_5p_sinsert","<div >This specifies stringency criteria for a match between the read and a sequence insert that should be present at the beginning of the read (after stripping of the 5' barcode if present).<br>X/e/g/o  (default 0/2/1/10)</div>","right");
 Balloon_Help("Help_Mr_tabu","<div >If a match is found that exceeds the requirements as specified by the program option -mr-tabu, the read is discarded<br>l/e[/g[/o]]  (default 16/2/1/0)</div>","right");
 Balloon_Help("Help_3p_head_to_tail","<div >This option specifies the minimum length at which a perfect match at the end of the read to the beginning of the 3' adapter should be stripped.<br>Minimal trailing perfect match length (default 0)</div>","right");
 ////Quality
 Balloon_Help("Help_qqq_check","<div >Low quality sequence can be detected using the median quality value in a sliding window. <br>(val)/(winlen) cut sequence when median quality drops below (val)<br>(val)/(winlen)/(winofs) as above, cut at <winofs> (default 0)<br>(val)/(winlen)/(winofs)/(readofs) as above, start at (readofs)<br>For -qqq-check the cutoff relates to the raw ASCII values found in the file and expressly not to the transformed P-values they represent.<br>(default 0/0)</div>","right");
 ////N-Masked Bases
 Balloon_Help("Help_nnn_check","<div > (count)/(outof)  <br>Disregard read onwards from seeing (count) N's in (outof) bases.<br> (default 0/0)</div>","right");
 ////Low Complexity Sequence
 Balloon_Help("Help_dust_suffix","<div > (threshold) dust theshold for read suffix.<br><br>The trimming test succeeds and this suffix is used if the score exceeds or is equal to the threshold specified.<br> (default 0, suggested 20)</div>","left");
 Balloon_Help("Help_dust_suffix_late","<div >A variant of this, still part of the first mechanism, is to apply this test after initial trimming by other means, such as trimming by adapter, quality, or occurrence of N-masked bases.<br>(default 0, suggested 20)</div>","left");
 ////Length-based Filtering
 Balloon_Help("Help_clean_length","<div >(int) minimum allowed clean length.<br>(default 0)</div>","right");
 ////Other options
 Balloon_Help("Help_tri","<div >(threshold)  filter out reads with tri-nt score > threshold, a reasonable (threshold) is 35</div>","right");
 Balloon_Help("Help_tri_length","<div >Cut reads back to length (int)</div>","right");
 Balloon_Help("Help_polya","<div >Remove trailing A's if length exceeds (int)</div>","right");
 Balloon_Help("Help_sc_max","<div >Threshold for complexity of suffix following prefix match.<br> (default 0.25)</div>","right");
 Balloon_Help("Help_bcq_early","<div > B is a special Illumina quality score indicating the base at that position should not be trusted. Runs of trailing Bs can be detected by specifying either --bcq-early or --bcq-late perform early 'B' quality filtering (when reading sequences).</div>","right");
 Balloon_Help("Help_bcq_late","<div > Perform late 'B' quality filtering (before outputting sequences)</div>","right");
 Balloon_Help("Help_full_length","<div > Only allow reads not shortened in any filter step.</div>","right");
////////////////////////7
///Consultas/////
/////////////////////7777
Balloon_Help("Help_Gramatica_Conjuntos","You can make the grammar with:<br>I : Intersecion<br>U : Union<br>D : Differences","left");



});
function Balloon_Help(Id,Mensaje,Position,config)
{
	if(!config)
	{
		//$("#"+Id).attr("data-content", Mensaje);
		$("#"+Id).popover({
		//	placement: Position,
			 trigger: "hover",
			  html : true, 
        content: function() {
					return Mensaje
			}
			 });
	}
	else
	{
		/*$("#"+Id).balloon(
		{
			contents:Mensaje,
			position:Position,
			css:{
			backgroundColor:"black",
			border: "solid 1px black",
			color:"white",
			textAlign:"center"
			}
			
		});*/
	}
	
}
