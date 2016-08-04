
<?php  
		session_start();
		Identificar_Formato();
		function Identificar_Formato()
	{
		
		$array=glob($_SESSION['path']."/{*}",GLOB_BRACE);
		switch($_SESSION['Tipo'])
		{
		case("FASTA"):	
								Filtrado($_SESSION['path']."/beforeFasta","F");	
				
		break;
		case("CSFASTA+QUAL"):
						FiltradoSOLID("CSFASTA+QUAL");
		break;
		case("QUAL+CSFASTA"):
			
						FiltradoSOLID("CSFASTA+QUAL");
		break;
		case("FASTAQ"):
		
					Filtrado($_SESSION['path']."/beforeFastaQ","FQ2H");	
		break;
		case("CSFASTAQ"):
		
FiltradoSOLID("CSFASTAQ");
					
		break;
		
		default:
		$Mensaje("El archivo no que se ha insertado es desconocido o esta defectuoso");
	Mensaje("switch incorrect");
		
		}
			   
	
	}		
	function Filtrado($File,$Type)
	{
$_SESSION['encode']=$_POST['classic'];
$Linker= $_POST['Linker'];
$Matches=$_POST['Matches'];	
		$_SESSION['Formato']="Illumina";
		if($_POST['opcMatch']=="Numeric")
			{	
			  $Matches= $Matches/strlen($Linker);
			}
			else if($_POST['opcMatch']=="Percent")
			{
				$Matches/=100;
			}
		shell_exec("perl Scripts/Convertir.pl -i $File -o ".$_SESSION['path']." -t $Type -e ".$_POST['classic']." -l ".$_POST['Linker']." -m $Matches ");	
			
	}
	function FiltradoSOLID($Type)
	{
$encode=$_POST['classic'];
$_SESSION['encode']=$encode;
$Linker= $_POST['Linker'];
$Matches=$_POST['Matches'];
$_SESSION['Formato']="Solid";
$Linker=LinkerSolid(strtoupper($Linker));
		
			if($_POST['opcMatch']=="Numeric")
			{	
			  $Matches= $Matches/strlen($Linker);
			}
			else if($_POST['opcMatch']=="Percent")
			{
				$Matches/=100;
			}
			if($Type=="CSFASTA+QUAL")
			{
			shell_exec("perl Scripts/csfastaq.pl -c ".$_SESSION['path']."/CSFASTA -q ".$_SESSION['path']."/QUAL -o ".$_SESSION['path']."/CSFASTAQ.txt -r ".$_SESSION['path']." -l $Linker -m $Matches -e $encode");
			}
			else if($Type=="CSFASTAQ")
			{
shell_exec("perl Scripts/csfastaq2.pl -f ".$_SESSION['path']."/beforeCSFastaQ -r ".$_SESSION['path']." -l $Linker -m $Matches -e $encode");
				
			}
	}
	
	
	 
	 
	
	
	function Mensaje($msj)
	{
		echo "<script languaje='javascript'>alert('".$msj."')</script>";
	}
function LinkerSolid($linker)
{

	$cad="";
	for($i=0;$i<strlen($linker)-1;$i++ )
	{
			$cad.=table($linker[$i],$linker[$i+1]);
			
	}
	
	return $cad;
	
}
function table($c1,$c2)
{
if($c1=="A" && $c2=="A"){return "A";}
if($c1=="C" && $c2=="C"){return "A";}
if($c1=="G" && $c2=="G"){return "A";}
if($c1=="T" && $c2=="T"){return "A";}
if($c1=="A" && $c2=="C"){return "T";}
if($c1=="C" && $c2=="A"){return "T";}
if($c1=="G" && $c2=="T"){return "T";}
if($c1=="T" && $c2=="G"){return "T";}
if($c1=="A" && $c2=="G"){return "G";}
if($c1=="C" && $c2=="T"){return "G";}
if($c1=="G" && $c2=="A"){return "G";}
if($c1=="T" && $c2=="C"){return "G";}
if($c1=="A" && $c2=="T"){return "C";}
if($c1=="C" && $c2=="G"){return "C";}
if($c1=="G" && $c2=="C"){return "C";}
if($c1=="T" && $c2=="A"){return "C";}
	
}
	

?>
