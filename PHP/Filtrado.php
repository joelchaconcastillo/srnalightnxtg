<?php
session_start();
$RutaRelativa=$_SESSION['path'];
$encode=$_SESSION['encode'];
$LQ=$_POST['LQ'];
if(!empty($_POST['max']))
{$Max=$_POST['max'];}
if(!empty($_POST['min']))
{$Min=$_POST['min'];}
$Redundance=$_POST['RemoveRep'];
$BasesI=$_POST['BforRead'];
$BasesA=$_POST['Bambi'];
$Range=$_POST['Range'];
$Minvalue=$_POST['LV'];

if($Redundance!= "TRUE"){$Redundance="FALSE";}
if($Range != "FALSE"){$Range="TRUE";}
if(empty($Max) && empty($Min)){$Max=0; $Min=0;}
if($encode=="33")
{
shell_exec("Rscript Scripts/AfterFilter.R $RutaRelativa FastqQuality $Min $Max $BasesI $BasesA $Redundance $Range $LQ $Minvalue ".$_SESSION['Formato']." 2> ".$_SESSION['path']."/Progress.txt >> /dev/null &");
}
if($encode=="64")
{
shell_exec("Rscript Scripts/AfterFilter.R $RutaRelativa SFastqQuality $Min $Max $BasesI $BasesA $Redundance $Range $LQ $Minvalue ".$_SESSION['Formato']." 2> ".$_SESSION['path']."/Progress.txt >> /dev/null &");
}
if($_SESSION['Formato']=="Solid")
{
//shell_exec("perl Scripts/csfastqhybrid-to-csfastq.pl -i $RutaRelativa/PureLecture.fq -o $RutaRelativa/PurelectureSolid.fq 2> ".$_SESSION['path']."/Progress.txt");// >> /dev/null &");	
 $_SESSION['Archivo']="-C ".$RutaRelativa."/PurelectureSolid.fq";
 $_SESSION['TInput']="_c";
}
if($_SESSION['Formato']=="Illumina")
{
	 $_SESSION['Archivo']="-q ".$RutaRelativa."/PureLecture.fq";
}
/*echo "<script>parent.MapearVisibility();</script>";*/
//shell_exec("rm -fr ".$_SESSION['path']);
?>