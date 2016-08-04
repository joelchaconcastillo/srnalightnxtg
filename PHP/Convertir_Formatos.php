<?php 
class Convertir_Formato
{
	 public $PathTemp;
	function Convertir_Formato()
	{
	}
		function Tofasta(&$array,$Temporal)
				{
					$tam=count($array);
					for($i=0;$i<count($array);$i++)
					{
						echo "<script>parent.CargaConvirtiendo('$i','$tam')</script>";
						if(preg_match("/^(.)+(\t)[ATGCUactgu]+(\t)(\d)+(\s)*$/",$array[$i]))
						{
							 
							   $dividido=split("\t",$array[$i]);
							   $array[$i]=">".$dividido[0]."|".$dividido[2];
								array_splice($array,$i+1,0,$dividido[1]); 
						}
					}
					$this->Tofastaq($array,$Temporal);
				}
		function Tofastaq(&$array,$Temporal)
				{
					$tam=count($array);
					for($i=0;$i<count($array);$i++)
							{
								echo "<script>parent.CargaConvirtiendo('$i','$tam')</script>";
								 if(preg_match("/^[atgcATGCuU]+(\s)*$/",$array[$i]))
								 {
									 
									 $char2="";
									for($u=0;$u<strlen($array[$i]);$u++)
									{ $char2.="40 ";}
									array_splice($array,$i+1,0,"+");
									array_splice($array,$i+2,0,$char2); 
									$char2="";
								 	}
										
							}
							
							$this->SavefastaQ($array,$Temporal);
					 
				}
		function TocsfastaQ($Csfasta,$Qual,$Temporal)
					{
						
						$Path=$Temporal.'/CSFASTAQ.txt';
	
	$Fcsf=fopen($Path,'w+');
	$CSFASTA=fopen($Csfasta,"r+") or exit("No se puede leer archivo");
	$QUAL=fopen($Qual,"r+") or exit("No se puede leer archivo");
	
		while(!feof($QUAL))
		{
			
				$txtcsfa=trim(fgets($CSFASTA));
				$txtqual=trim(fgets($QUAL));
				if(!empty($txtcsfa) && !empty($txtqual))
				{	
				
				  $txtcsfa=str_replace("\n","",$txtcsfa);
				  $txtcsfa=str_replace("\r","",$txtcsfa);
				  $txtqual=str_replace("\n","",$txtqual);
				  $txtqual=str_replace("\r","",$txtqual);
				  if(preg_match("/^>(.)+(\s)*$/",$txtcsfa))
								{
									if($txtcsfa != $txtqual){ echo "Las cabeceras son diferentes";}
									fwrite($Fcsf,str_replace(">","@",$txtcsfa."\n"));	
								}
				else if(preg_match("/^[tT\.0123]+(\s)*$/",$txtcsfa) || preg_match("/(^((-?[\d\.])+(\s)?)+)(\s)*$/",$txtqual))
				 {
					 fwrite($Fcsf,$txtcsfa."\n");	
					 fwrite($Fcsf,"+"."\n");
					 fwrite($Fcsf,$txtqual."\n");	
									
								
					 
				 }
				 else {fclose($Fcsf);fclose($CSFATA);fclose($QUAL); return false;}
				}
				
		}
		fclose($Fcsf);fclose($CSFATA);fclose($QUAL);
		session_destroy();
		return true;
	
						
						/*
						$array=array();
						$tam=count($array);
						if(count($t1)==count($t2))
						{
							for($i=0;$i<count($t1);$i++)
							{
								 echo "<script>parent.CargaConvirtiendo('$i','$tam')</script>";
								if(preg_match("/^>(.)+(\s)*$/",$t1[$i]))
								{
									  array_push($array,str_replace(">","@",$t1[$i]));	
								}
							 if(preg_match("/^[tT\.0123]+(\s)*$/",$t1[$i]) || preg_match("/(^((-?[\d\.])+(\s)?)+)(\s)*$/",$t2[$i]))
								{
									
										array_push($array,$t1[$i]);
										array_push($array,"+");
										array_push($array,$t2[$i]);
								}
								
							}
							$this->SavecsfastaQ($array,$Temporal);
							
							
						}*/
					}
		function SavefastaQ($cadena,$Temporal)
						{
						
						$Temporal.="/";
						$Path=$Temporal.'FASTAQ.txt';
						$fp=fopen($Path,'w+');	 
						foreach($cadena as $linea)
							{
						fwrite ($fp, $linea."\n");
							}
						
						fclose($fp);
					echo "<script>parent.redireccionar();</script>";
						
						}
		
		function SavecsfastaQ($cadena,$Temporal)
			{
				session_destroy();
				/*$Temporal.="/";
				$Path=$Temporal.'CSFASTAQ.txt';
				$fp=fopen($Path,'w+');
				
			foreach($cadena as $linea)
				{
			fwrite ($fp, $linea."\n");
				}
			
				fclose($fp);
				
				echo "<script>parent.redireccionar();</script>";
			*/}
}
?>
