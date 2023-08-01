<?php
include("MySQL.php");
switch($_REQUEST['Origen'])
{
	
case "Organismos_Disponibles":
Organismos_Disponibles();
break;
case "Genomas_Disponibles":
Genomas_Disponibles();
break;
case "Librerias_Disponibles":
Librerias_Disponibles();
break;
case "Realizar_Consulta":
Realizar_Consulta();
break;
case "Realizar_Consulta_Secuencia_Especial":
Realizar_Consulta_Secuencia_Especial();
break;
case "Enviar_Filtro":
Enviar_Filtro();
break;	
case "Enviar_Filtro_Archivo":
Enviar_Filtro_Archivo();
break;	
case "Descargar_Filtro":
Descargar_Filtro();
break;
case "Descargar_Filtro_Archivo":
Descargar_Filtro_Archivo();
break;
case "Remove":
Remove();
break;
case "Consulta":
Consulta();
break;
case "LibraryForm":
Consulta_Libreria();
break;
case "Organismos":
Organismos();
break;
case "Genomas":
Genomas();
break;	
case "Libreria":
Libreria();
break;
case "Revisar_Cambios_Consultas":
Revisar_Cambios_Consultas();
break;
case "Limpiar_Visto":
Limpiar_Visto();
break;
case "Desactivar_Ventana_Abierta":
Desactivar_Ventana_Abierta();
break;
}
function Desactivar_Ventana_Abierta()
{
	$Conexion = Abrir_Conexion();
	$Id_Query = $_POST['Id_Query'];
	mysql_query("UPDATE Query SET Ventana_Abierta = 0 WHERE Id_Query = $Id_Query", $Conexion);
	Cerrar_Conexion($Conexion);
}
function Limpiar_Visto()
{
		session_start();
		$Conexion = Abrir_Conexion();
		$Id_Cuenta = $_SESSION['Id_Cuenta'];
		$Select = "UPDATE Query SET Visto = 0 ";
		$Select .="WHERE Id_Cuenta = $Id_Cuenta  ";
		mysql_query($Select, $Conexion);
		Cerrar_Conexion($Conexion);
		
}
function Revisar_Cambios_Consultas()
{
		session_start();
		$Conexion = Abrir_Conexion();
		$Id_Cuenta = $_SESSION['Id_Cuenta'];
		$Select ="SELECT COUNT(*) FROM Query Q ";
		$Select .=" WHERE Q.Id_Cuenta = $Id_Cuenta AND Visto = 1  ";
		$Resultado = @mysql_query($Select, $Conexion);
		$Numero_Elementos = mysql_fetch_array($Resultado);// @mysql_num_rows($Resultado);
		if($Numero_Elementos){
			echo json_encode(array("Numero_Consultas_Listos" => $Numero_Elementos[0]));
		}
		
}
function Organismos_Disponibles()
{
	$Conexion=Abrir_Conexion();
	$Resultado = mysql_query("select * from Organismo",$Conexion);

	echo '<option value=""  id="dropdown" selected="selected">None</option>';
	while($row =mysql_fetch_assoc($Resultado))
	{
	echo '<option value="'.$row['Id_Organismo'].'">'.$row['Organismo'].'</option>'; 
	}
	Cerrar_Conexion($Conexion);
}
function Genomas_Disponibles()
{
	session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Conexion=Abrir_Conexion();
	$Resultado = mysql_query("SELECT * FROM Genoma",$Conexion);
	echo '<option value="" id="dropdown" selected="selected">None</option>';
	while($row =mysql_fetch_assoc($Resultado))
	{
	echo '<option value="'.$row['Id_Genoma'].'">'.$row['Nombre'].'</option>'; 
	}
	Cerrar_Conexion($Conexion);
}
function Librerias_Disponibles()
{
	session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Conexion=Abrir_Conexion();
	$Resultado = mysql_query("SELECT * FROM Libreria WHERE Id_Cuenta = $Id_Cuenta",$Conexion);
	echo '<option value="" id="dropdown" selected="selected">None</option>';
	while($row =mysql_fetch_assoc($Resultado))
	{
		echo '<option value="'.$row['Id_Libreria'].'_'.$Id_Cuenta.'">'.$row['Nombre'].'</option>'; 
	}
	$Query = "select L.Nombre,L.Id_Libreria from Grupo_Libreria GL inner join Grupo G on G.Id_Grupo = GL.Grupo_Id_Grupo inner join Miembro_Grupo MG on MG.Id_Grupo = G.Id_Grupo inner join Cuenta C on C.Id_Cuenta = MG.Id_Cuenta inner join Libreria L on Id_Libreria = GL.Libreria_Id_Libreria where L.Id_Cuenta != $Id_Cuenta ;";
	$Resultado = mysql_query($Query, $Conexion);
	while($row =mysql_fetch_assoc($Resultado))
	{
		$row2 = mysql_fetch_assoc(mysql_query("SELECT * FROM Cuenta C INNER JOIN Libreria L on L.Id_Cuenta = C.Id_Cuenta WHERE L.Id_Libreria = ".$row['Id_Libreria'], $Conexion));
		echo '<option value="'.$row2['Id_Libreria'].'_'.$row2['Id_Cuenta'].'">'.$row['Nombre'].' ( from '.$row2['Cuenta'].' )</option>'; 
	}
	Cerrar_Conexion($Conexion);
}
function Realizar_Consulta()
{
	session_start();
	$Vistas = $_POST['Vistas'];
	$Operaciones = $_POST['Operaciones'];
	//Se limpia el objeto de las vistas ya que exísten elementos los cuales se encuentran vacíos
	//Paso de los datos por referencia
	Limpiar_Vistas($Vistas);
	//Verificar si se deben crear tablas temporales
	if(preg_match("/INTERSECTION/",implode($Operaciones)) || preg_match("/UNION/",implode($Operaciones)))
	$Query = Preparar_Tablas_Temporales($Vistas, $Operaciones);
	else
	$Query = Generar_Consulta($Vistas, $Operaciones);
	
	Consulta_Bash($Query);
		
}
function Realizar_Consulta_Secuencia_Especial()
{
		session_start();
	$Id_Cuenta = $_SESSION['Id_Cuenta'];
	$Secuencia = $_POST['Secuencia'];	
	$Missmatch = $_POST['Missmatch'];	
	$Tipo_Busqueda = $_POST['Tipo_Busqueda'];
	$Patron = $_POST['Pattern'];
	$Informacion_Libreria = split("_",$_POST['Informacion_Libreria']);
	$Id_Cuenta_Busqueda = $Informacion_Libreria[1];
	$Id_Libreria_Busqueda =  $Informacion_Libreria[0];
	$Query="CREATE TEMPORARY TABLE Secuencia$Id_Cuenta ENGINE = MYISAM SELECT * FROM ";
	
	//Consulta especial porque se realiza la búsqueda en una cuenta diferente
	$Query.="Molecula".$Id_Cuenta_Busqueda." ";
	$Query.=" WHERE Id_Libreria = $Id_Libreria_Busqueda; ";
	$Query.="CREATE FULLTEXT INDEX INDICE_$Id_Cuenta on Secuencia$Id_Cuenta(Secuencia); ";
	$Query.="SELECT M.Id_MoleculaN, M.Chr, M.Inicio, M.Fin, M.Sentido, M.Missmatches, M.Secuencia, M.Loci, M.Numero_Moleculas, M.Tipo, M.Id_Biologico FROM Secuencia$Id_Cuenta M ";
	if($Tipo_Busqueda == "Simple")
	{
		if(!empty($Secuencia))
		{
			$Query.="WHERE ";
			if($Missmatch==0)
			{
				$Query.="M.Secuencia LIKE '%$Secuencia%'";
			}
			else
			{
				for( $i=0; $i< strlen($Secuencia)-$Missmatch+1 ; $i++)
				{
					$Temporal = $Secuencia;
					
					for($j=0; $j<$Missmatch ; $j++)
					$Temporal[$i+$j]=".";
					$Query.="M.Secuencia REGEXP '$Temporal' OR ";		
					
				}
				$Query= substr($Query,0,-3); 		
			}
		}
	}
	else
	{
		if(!empty($Patron))
		$Query.="WHERE M.Secuencia REGEXP '$Patron' ";
	}

	Consulta_Bash($Query);
}
//Se separan las vistas por tablas, si existe una intersección o union se genera una tabla temporal
function Preparar_Tablas_Temporales($Vistas, $Operaciones)
{
	$Tabla = array();
	$Tablas = array();
	$Operacion = array();
		foreach($Vistas as $indice => $Vista)
		{
			//En el caso de la primer vista que no cuenta con una operación
			if(empty($Operaciones[$indice]))
			$Tabla[$indice] = $Vista;
			//En el caso de que exista una intersección o una unión
			else if($Operaciones[$indice] == "INTERSECTION" || $Operaciones[$indice] == "UNION")
			{
				$Tablas[] = $Tabla;
				unset($Tabla);
				$Tabla[$indice] = $Vista;
				$Operacion[] = $Operaciones[$indice];
			}
			//Los demás casos
			else
			$Tabla[$indice] = $Vista;
		}
		$Tablas[] = $Tabla;
		$Query2 = "";
	
		foreach($Tablas as $index => $Tabla)
		{
			$Query.="CREATE TEMPORARY TABLE Table$index ENGINE = MYISAM ".Generar_Consulta($Tabla, $Operaciones)." GROUP BY (Secuencia) ; ALTER TABLE Table$index ADD INDEX (Secuencia); ";
			if($Operacion[$index] == "UNION")
			{ 
				$Query2.="SELECT * FROM Table$index UNION ";
			}
			if($Operacion[$index] == "INTERSECTION")
			{
				if(substr($Query2,-6) == "UNION " || empty($Query2))
				$Query2.="SELECT T$index.* FROM Table$index T$index INNER JOIN ";
				else if(substr($Query2,-5) == "JOIN ")
				$Query2.="Table$index T$index USING(Secuencia) INNER JOIN ";
			}
			if(empty($Operacion[$index]))
			{
				if(substr($Query2,-6) == "UNION ")
				$Query2.="SELECT * FROM Table$index";
				if(substr($Query2,-5) == "JOIN ")
				$Query2.="Table$index T$index USING(Secuencia)";
			}
			
			
		}
		$Query.="; ";
		return $Query.$Query2;
}
function Verificar_Librerias_Externas($Vistas, $Operaciones)
{
	$Librerias_Grupos = array();
	$Cont = 0;
	foreach($Vistas as $indice => $Vista)
	{
		$Librerias_Grupos[$Cont]['Id_Cuenta'] = $_SESSION['Id_Cuenta'];
		foreach($Vista as $i => $Campo)
		{
			if($Campo['name'] == "Id_Libreria" && preg_match("/_/", $Campo['value']))
			{
				
				$Info = explode("_",$Campo['value']);
				$Librerias_Grupos[$Cont]['Id_Libreria'] = $Info[0];
				$Librerias_Grupos[$Cont]['Id_Cuenta'] = $Info[1];
			}
		}
		$Cont++;
		
	}
	return $Librerias_Grupos;
}
function Generar_Consulta($Vistas, $Operaciones)
{
	$Librerias_Grupos = Verificar_Librerias_Externas($Vistas, $Operaciones);
	$Consulta = "";
	//foreach($Librerias_Grupos as $Molecula)
	for($i=0;$i<count($Librerias_Grupos); $i++)
	{
		$Consulta .= Generar_Subconsulta($Vistas, $Operaciones, $Librerias_Grupos[$i]['Id_Cuenta'] )." UNION ";
	}
	$Consulta = substr($Consulta,0,-6);
	 return $Consulta;
}
function Generar_Subconsulta($Vistas, $Operaciones, $Id_Cuenta)
{
		//if($Id_Cuenta)$Id_Cuenta = $_SESSION['Id_Cuenta'];
		$Elemento = array();
		$Select = "SELECT M.Id_MoleculaN, M.Chr, M.Inicio, M.Fin, M.Sentido, M.Missmatches, M.Secuencia, M.Loci, M.Numero_Moleculas, M.Tipo, M.Id_Biologico  FROM Molecula$Id_Cuenta M ";
		$Where ="WHERE ";
		$Join ="";
		foreach($Vistas as $indice => $Vista)
		{
			foreach($Vista as $i => $Campo)
			{
				if($Campo['name'] == "Id_Organismo")
					$Join = " INNER JOIN Genoma G on M.Id_Genoma = G.Id_Genoma INNER JOIN Organismo O on O.Id_Organismo = G.Id_Organismo ";			
				if(isset($Elemento[$Campo['name']]))
					$Elemento[$Campo['name']] .=  $Operaciones[$indice] . " ". Revisar_Inner_Join($Campo) .Revisar_Tipo_Campo($Campo)." ";
				else
					$Elemento[$Campo['name']] = Revisar_Inner_Join($Campo). Revisar_Tipo_Campo($Campo)." ";
			}
		}
		foreach($Elemento as $Campo)
			$Where.=$Campo." AND ";
			//Eliminar el último "AND"
			$Where = substr($Where,0, -4);
		return $Select.$Join.$Where;
}
function Revisar_Inner_Join($Campo)
{
	if($Campo['name'] == "Id_Organismo")
		return "O.".$Campo['name'];
	else
		return "M.".$Campo['name'];
}
function Revisar_Tipo_Campo($Campo)
{
	switch($Campo['name'])
	{
		case "Id_Organismo":
		return " = ".$Campo['value'];
		case "Id_Genoma":
		return " = ".$Campo['value'];
		case "Id_Libreria":
		$Elementos = explode("_" , $Campo['value']);
		return " = ".$Elementos[0];
		case "Tipo":
		return " = '".$Campo['value']."'";
		case "Secuencia":
		return " = '".$Campo['value']."'";
		case "Numero_Moleculas":
		return " ".$Campo['value'];
		case "Sentido":
		return " = '".$Campo['value']."'";
		case "Chr":
		return " = '".$Campo['value']."'";
		case "Inicio":
		return " >= ".$Campo['value'];
		case "Fin":
		return " <= ".$Campo['value'];
		
	}
}
//Parámetro por referencia
function Limpiar_Vistas(&$Vistas)
{
	foreach($Vistas as $indice => $Vista)
	{
		foreach($Vista as $i => $Campo)
			{
				if($Campo['value'] == "")
				{
					unset($Vistas[$indice][$i]);
				}
			}
	}
}
function Consulta()
{
	if(count($_POST['SetHash'])>1)
	{
		$Query="";
		//Se preparan las tablas temporales
			foreach($_POST['SetHash'] as $Element=>$Val)
			{
				$Query.="create temporary table $Element engine=myisam  ";
				 if(!empty($_POST['Tally']))
			  $Query.=MakeQuery($Val)." group by (Secuencia); ";
			 else $Query.=MakeQuery($Val)."; ";
				
				$Query.="alter table $Element add index (Secuencia); ";
			}  
	    //Se comparan las tablas teporales según el criterio seleccionado 
	    $Select="";
		$Inner="";
			switch($_POST['Criterio'])
			{
				case "Intersection":
					$Select="select ";
						$Cont=1;
							foreach($_POST['SetHash'] as $Element=>$Val)
						{
							$Select.=Select($Element);
							
							if($Cont==1)
							{
							$Inner.="$Element";
							} else $Inner.=" inner join $Element using(Secuencia)";
							$Cont++;
						}
					$Select="$Select Secuencia from $Inner";
					
				break;
				case "Union":
					foreach($_POST['SetHash'] as $Element=>$Val)
					{
						if(!empty($_POST['Tally']))
						$Select.="select * from $Element union ";
						else
						$Select.="select * from $Element union all ";
					}
					if(!empty($_POST['Tally']))
					$Select=substr($Select,0,-6);
					else			
					$Select=substr($Select,0,-10);				
				break;
				case "Differences":
					$Select="select ";
					$tmp="";
						$Cont=1;
							foreach($_POST['SetHash'] as $Element=>$Val)
						{
							$Select.=Select($Element);
							
							if($Cont==1)
							{
								$tmp=$Element;
						    	$Inner.="$Element";
							} else 
							{
						     	$Inner.=" inner join $Element on($tmp.Secuencia=$Element.Secuencia)";
								$tmp=$Element;
							}
							$Cont++;
						}
					$Select="$Select Set4.Secuencia,Set5.Secuencia from $Inner";

				break;
				
			}
			$Total=$Query.$Select;
			 system( "echo \"$Total\" >Consultas/uno");
			// if(!empty($_POST['Tally']))
			  //Consulta_Bash($Total,true);
			 //else
			  Consulta_Bash($Total);
			 
	}
	else
	{
		foreach($_POST['SetHash'] as $Element=>$Val)
			{
				if(!empty($_POST['Tally']))
				Consulta_Bash(MakeQuery($Val)." group by (Secuencia)");
				else
				Consulta_Bash(MakeQuery($Val));
				shell_exec($Element);
			}
	}
	//shell_exec($_POST['SetHash']['Set4']['BioGen'][0]);
	//Consulta_Bash(MakeQuery($_POST['SetHash']['Set1']));
}

















function Enviar_Filtro_Archivo()
{
	$Capacity=10000;

		$Filtro="cat ./../Consultas/".$_POST['Archivo']." ";
		if($_POST['Longitud']<=$Capacity)
		{
			(empty($_POST['Numero_Linea']))?$Numero_Linea=2: $Numero_Linea=$_POST['Numero_Linea']+1;
			(empty($_POST['Longitud']))?$Longitud=100: $Longitud=$_POST['Longitud'];
			$Stream=$Numero_Linea+$Longitud-1;
			$Stream.="p";
			$Filtro.=" | sed -n '".$Numero_Linea.",$Stream' ";
			if(!empty($_POST['Patron']))
			$Filtro.="| grep ".$_POST['Patron'];
		}
		
	$Tabla=shell_exec($Filtro);
	
	
	
	$Formato="<table id='Tabla_Texto' style='text-align:center; vertical-align:middle;' cellspacing='0' border='0' class='table display' >
	<thead>
		<th>Id_MoleculaN</th>
		<th>Chr</th>
		<th>Start</th>
		<th>End</th>
		<th>Strand</th>
		<th>Missmatches</th>
		<th>Sequence</th>
		<th>Loci</th>
		<th>Number Molecule</th>
		<th>Type</th>
		<th>Id Biology</th>
	</thead>
	<tbody>";

	foreach(preg_split('/[\n]/', $Tabla) as $Fila)
	{
		if(empty($Fila))continue;
		$Formato.="<tr>";
		foreach(preg_split("/[\t]/", $Fila) as $Campo)
		{
				$Formato.=" <td>".$Campo."</td>";
		}
		$Formato.="</tr>";
	}
	$Formato.="</tbody> </table>";
	echo $Formato;
	
}
function Descargar_Filtro()
{
	$Id=uniqid();
	$Archivo='../Consultas/'.$Id.'.zip';
	$Temporal='../Consultas/'.$Id;
	$Filtro=getFiltro();
	$Filtro.=" > $Temporal  ; zip -j $Archivo $Temporal; rm $Temporal ";
	shell_exec($Filtro);
	Temporizador($Archivo);
	echo $Archivo;
	
}
function Descargar_Filtro_Archivo()
{
	$Capacity=10000;

		$Filtro="cat ./../Consultas/".$_POST['Archivo']." ";
		if($_POST['Longitud']<=$Capacity)
		{
			if(!empty($_POST['Patron']))
			$Filtro.="| grep ".$_POST['Patron'];
			(empty($_POST['Numero_Linea']))?$Numero_Linea=2: $Numero_Linea=$_POST['Numero_Linea']+1;
			(empty($_POST['Longitud']))?$Longitud=100: $Longitud=$_POST['Longitud'];
			$Stream=$Numero_Linea+$Longitud-1;
			$Stream.="p";
			$Filtro.=" | sed -n '".$Numero_Linea.",$Stream' ";
			
		}
	$Id=uniqid();
	$Archivo='../Consultas/'.$Id.'.zip';
	$Temporal='../Consultas/'.$Id;
	$Filtro.=" > $Temporal  ; zip -j $Archivo $Temporal; rm $Temporal ";
	shell_exec($Filtro);
	
	
	Temporizador($Archivo);
	echo $Archivo;
	
}
function getFiltro()
{
	$Conexion=Abrir_Conexion();	
	$Capacity=10000;

		$Resultado=mysql_query("SELECT * FROM Libreria WHERE Id_Libreria = ".$_POST['Id_Libreria'],$Conexion);
		$row=mysql_fetch_assoc($Resultado);
		Cerrar_Conexion($Conexion);

		$Filtro=' ./../Scripts/Select_Buffer.sh " SELECT Id_MoleculaN, Chr, Inicio, Fin, Sentido, Missmatches, Secuencia, Loci, Numero_Moleculas, Tipo, Id_Biologico FROM Molecula'.$row['Id_Cuenta'].' WHERE Id_Libreria = '.$_POST['Id_Libreria'].'" ';


		if($_POST['Longitud']<=$Capacity)
		{
			if(!empty($_POST['Patron']))
			$Filtro.="| grep ".$_POST['Patron'];
			(empty($_POST['Numero_Linea']))?$Numero_Linea=2: $Numero_Linea=$_POST['Numero_Linea']+1;
			(empty($_POST['Longitud']))?$Longitud=100: $Longitud=$_POST['Longitud'];
			$Stream=$Numero_Linea+$Longitud-1;
			$Stream.="p";
			$Filtro.=" | sed -n '".$Numero_Linea.",$Stream' ";
			
		}
		return $Filtro;
}
function Enviar_Filtro()
{

	$Tabla=shell_exec(getFiltro());
	
	
	
	$Formato="<table id='Tabla_Texto' style='text-align:center; vertical-align:middle;' cellpadding='0' cellspacing='0' border='0' class='table display' >
	<thead>
		<th>Id Database</th>
		<th>Chr</th>
		<th>Start</th>
		<th>End</th>
		<th>Strand</th>
		<th>Missmatches</th>
		<th>Sequence</th>
		<th>Loci</th>
		<th>Number Molecule</th>
		<th>Type</th>
		<th>Id Biology</th>
	</thead>
	<tbody>";
	
	foreach(preg_split('/[\n]/', $Tabla) as $Fila)
	{
		if(empty($Fila))continue;
		$Formato.="<tr>";
		foreach(preg_split("/[\t]/", $Fila) as $Campo)
		{
				$Formato.=" <td>".$Campo."</td>";
		}
		$Formato.="</tr>";
	}
	$Formato.="</tbody> </table>";
	echo $Formato;
	
}
function Libreria()
{
	session_start();
	$conexion=Abrir_Conexion();
	$q = mysql_query("select * from Libreria where Id_Cuenta= ".$_SESSION['Id_Cuenta'] ,$conexion);
//echo '<option value="" disabled="disabled" id="dropdown" required selected="selected">Please select a Library</option>';
echo '<option value="" selected style="color:red;">None</option>';

while($row =mysql_fetch_assoc($q))
  {
	  echo '<option value="'.$row['Ruta'].'"  id="dropdown" >'.$row['Nombre'].'</option>';

  }

	Cerrar_Conexion($conexion);
}
function Genomas()
{
	$conexion=Abrir_Conexion();
$array=glob("./../Indexes/{*}",GLOB_BRACE);
//echo '<option value="" disabled="disabled" id="dropdown" required selected="selected">Please select a Genome</option>';
echo '<option value="" selected style="color:red;">None</option>';
			foreach($array as $line)
			{
				$Split=explode("_",basename($line));
        		$Version=end($Split);
				$Genoma=substr(basename($line),0,-1*(strlen($Version)+1));
				$q = mysql_query("select * from Organismo O inner join Genoma G on G.Id_Organismo = O.Id_Organismo where G.Nombre= '".$Genoma."' and Version='".$Version."';" ,$conexion);
				$row =mysql_fetch_assoc($q);
				echo '<option value="'.$line.'"  id="dropdown" >'.$row['Organismo']." (".basename($line).")".'</option>';
                        
			}	
}
function Remove()
{
$name=$_POST['Path'];
$name=str_replace("%2F","/",$name);
((!empty($name))&& preg_match("/Consultas/",$name))?shell_exec("rm -fr $name"):true;
unlink($_POST['Archivo']."Result");
unlink($_POST['Archivo']);
}
function Temporizador($Ruta)
{
	shell_exec("(sleep 10h; rm -fr $Ruta) > /dev/null 2>&1 & echo $!");
}
function Organismos()
{
$conexion=Abrir_Conexion();
$q = mysql_query("select distinct Organismo from Organismo",$conexion);

echo '<option value="" disabled="disabled" id="dropdown" selected="selected">Please select a Organism</option>';
while($row =mysql_fetch_assoc($q))
  {
echo '<option id="Organismo" value="'.$row['Organismo'].'">'.$row['Organismo'].'</option>'; 
  }
	Cerrar_Conexion($conexion);
}
function Consulta_Bash($Query)
{
	$id=uniqid();
	//En caso de que se interrumpa algún proceso
	$R1='./..Consultas/'.$id.'.txt';
	$R2='./../Consultas/'.$id.'.fa';
	$R3='./../Consultas/'.$id.'.zip';
	
	if((!empty($R1)) && (!empty($R2)) && (!empty($R3)))
	{
	 Temporizador($R1);
	 Temporizador($R2);
	 Temporizador($R3);
	}
	
	$Id_Query = Registro_PID_MySQL();
	  system( "echo \"".$Query."\" > ./../Consultas/uno");
   $Query="UPDATE Query SET PID = (SELECT CONNECTION_ID() AS pid), Archivo = '$id.txt' WHERE Id_Query = $Id_Query; ".$Query;
   $Query.="; UPDATE Query SET Fecha_Termino = NOW(), Visto=1, Ventana_Abierta=1 WHERE Id_Query = $Id_Query; ";
   shell_exec('./../Scripts/Select.sh "'.$Query.'" ./../Consultas/'.$id.'.txt >> /dev/null &');
 
   
     //system( "echo \"".$row['Info']."\" > ./../Consultas/uno");
	/*shell_exec('./../bin/BDtoFasta ./../Consultas/'.$id.'.txt ./../Consultas/'.$id.'.fa');
		shell_exec('zip  ./../Consultas/'.$id.' ./../Consultas/'.$id.'.fa');
	$name=urlencode('./../Consultas/'.$id.'.zip');
	$url= 'Descargar.php?id='.urlencode('Consultas/'.$id.'.zip').'&name='.$name;
	$size=filesize('./../Consultas/'.$id.'.zip');
	$sizefa=filesize('./../Consultas/'.$id.'.fa');
	$Num_Lines=shell_exec('wc -l ./../Consultas/'.$id.'.fa');
	$Reads="The elements of this view only soport 10000 lines and can't download only copy ";
	$Obj= array('Url'=>$url,'Size'=>formatSizeUnits($size),'Sizefa'=>$sizefa,'Name'=>$name, 'NumReads'=>($Num_Lines/2),'Reads'=>$Reads,'Fasta'=>'Consultas/'.$id.'.fa');
	
	echo json_encode($Obj);
	unlink('./../Consultas/'.$id.'.txt');*/
	
}

function Registro_PID_MySQL()
{
	session_start();
	$Conexion=Abrir_Conexion();
	 mysql_query("INSERT INTO Query VALUES (NULL, '".$_POST['txt_Nombre']."', '".date('Y-m-d H:i:s')."', NULL, '".$_POST['txt_Descripcion']."', 0, ".$_SESSION['Id_Cuenta'].",NULL, NULL, NULL )",$Conexion);
	 $Ultimo_Id=mysql_insert_id();
	Cerrar_Conexion($Conexion);
	return $Ultimo_Id;
	
}
function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}

function Consulta_Libreria()
{
	$conexion=Abrir_Conexion();
	$q=mysql_query("Select Tipo_Libreria from Libreria where Id_Libreria =".$_POST['opc'],$conexion);
	
	$Molecula=mysql_fetch_assoc($q);
	
	Cerrar_Conexion($conexion);
	$Query="";
	
	$Tam=$Molecula['Tipo_Libreria'];
	 if(!empty($_POST['opc'])) 
	 {
		$Query="select T.Id_MoleculaN, T.Id_Biologico, O.Organismo, T.Version, T.Inicio, T.Fin, T.Sentido, T.Secuencia from $Tam T inner join Libreria L on T.Id_Libreria=L.Id_Libreria inner join Organismo O on O.Id_Organismo=L.Id_Organismo where T.Id_Libreria =".$_POST['opc'];
	 }
	
	 Consulta_Bash($Query);
		
}
function MakeQuery($Set)
{
	session_start();
	$Id=$_SESSION['Id_Cuenta'];
	//Anotacion A
	//Molecula M
	//SubTipo S
	//SubDominio Su
	$Molecula=$_POST['Tabla'];
	$Where="";
	$Query="";
	$Select="Select ";
	//Los campos que se deben obtener de la consulta
	if(!empty($_POST['Id_MoleculaN_Field'])){
		
		( $_POST['Id_MoleculaN_Field']=="true")?$Select.="M.Id_MoleculaN, ":true;
		}
	if(!empty($_POST['Id_Biologico_Field']) ){
		( $_POST['Id_Biologico_Field']=="true")?$Select.="M.Id_Biologico, ":true;
	}
	if(!empty($_POST['Genoma_Field'])){
		($_POST['Genoma_Field']=="true")?$Select.="G.Nombre, ":true;
	}
	if(!empty($_POST['Inicio_Field'])){
		($_POST['Inicio_Field']=="true")?$Select.="M.Inicio, ":true;
	}
	if(!empty($_POST['Fin_Field'])){
		
		($_POST['Fin_Field']=="true")?$Select.="M.Fin, ":true;
	}
	if(!empty($_POST['Chr_Field'])){
		($_POST['Chr_Field']=="true")?$Select.="M.Chr, ":true;
	}
	if(!empty($_POST['Sentido_Field'])){
		($_POST['Sentido_Field']=="true")?$Select.="M.Sentido, ":true;
	}
	if(!empty($_POST['Mismatches_Field'])){
		($_POST['Mismatches_Field']=="true")?$Select.="M.Missmatches, ":true;
	}
	if(!empty($_POST['Numero_Moleculas'])){
		($_POST['Numero_Moleculas']=="true")?$Select.="M.Numero_Moleculas, ":true;
	}
	$Select.=" M.Secuencia ";
	

	session_start();
	//$Id=
	//M.Id_MoleculaN, M.Id_Biologico, M.Secuencia
	$Inner=$Select."from Molecula$Id M inner join Libreria L on L.Id_Libreria = M.Id_Libreria inner join Genoma G on G.Id_Genoma = M.Id_Genoma";
	$Where=" where L.Id_Cuenta = ".$_SESSION['Id_Cuenta'];
	
	if(count($Set['Libreria'])>0)
	{
		$Where.=" and ";
		for($i=0;$i<count($Set['Libreria']) ;$i++)
		{
		$Where.="L.Nombre = '".$Set['Libreria'][$i]."' or ";
		}
		$Where=substr($Where,0,-3);
	}
	if(count($Set['Secuencia'])>0)
	{
		$Where.=" and ";
		for($i=0;$i<count($Set['Secuencia']) ;$i++)
		{
		$Where.="M.Secuencia = '".$Set['Secuencia'][$i]."' or ";
		}
		$Where=substr($Where,0,-3);
	}	
	if(count($Set['Id_Biologico'])>0)
	{
		$Where.=" and ";
		for($i=0;$i<count($Set['Id_Biologico']) ;$i++)
		{
		$Where.="M.Id_Biologico = '".$Set['Id_Biologico'][$i]."' or ";
		}
		$Where=substr($Where,0,-3);
	}
	if(count($Set['Numero_Moleculas'])>0)
	{
		$Where.=" and ";
		for($i=0;$i<count($Set['Numero_Moleculas']) ;$i++)
		{
		$Where.="M.Numero_Moleculas = '".$Set['Numero_Moleculas'][$i]."' or ";
		}
		$Where=substr($Where,0,-3);
	}
	if(count($Set['Chr'])>0)
	{
		
		$Where.=" and ";
		for($i=0;$i<count($Set['Chr']) ;$i++)
		{
		$Where.="M.Chr = '".$Set['Chr'][$i]."' or ";
		}
		$Where=substr($Where,0,-3);
	
	}
	if(count($Set['Inicio'])>0)
	{
		$Where.=" and ";
		for($i=0;$i<count($Set['Inicio']) ;$i++)
		{
			if($Set['Fin'][$i]>=0)
			{
		$Where.="M.Inicio >= ".$Set['Inicio'][$i]." and M.Fin <= ".$Set['Fin'][$i]." or ";
			}
		}
		$Where=substr($Where,0,-3);
	}
	 
	if(count($Set['Sentido'])>0)
	{
		$Where.=" and ";
		for($i=0;$i<count($Set['Sentido']) ;$i++)
		{
		$Where.="M.Sentido = '".$Set['Sentido'][$i]."' or ";
		}
		$Where=substr($Where,0,-3);
	}
	
	//Elementos que necesitan otras tablas
	
	if(count($Set['Genoma'])>0)
	{
		$Where.=" and ";
		for($i=0;$i<count($Set['Genoma']) ;$i++)
		{
			$Split=explode("_",$Set['Genoma'][$i]);
			$Version=end($Split);
			$Genoma=substr($Set['Genoma'][$i],0,-1*(strlen($Version)+1));
			$Where.="G.Nombre = '".$Genoma."' and G.Version= '".$Version."' or ";
		}
		$Where=substr($Where,0,-3);
	}
	if(count($Set['BioGen'])>0)
	{
		$Where.=" and ";
		for($i=0;$i<count($Set['BioGen']) ;$i++)
		{
		$Where.="M.Tipo = '".$Set['BioGen'][$i]."' or ";
		}	

		$Where=substr($Where,0,-3);
	}
	if(count($Set['Nombre_Comun'])>0)
	{
		(preg_match("/Anotacion/",$Inner))?true:$Inner.=" inner join Anotacion A on A.Id_Biologico = M.Id_Biologico";
		
		$Where.=" and ";
		for($i=0;$i<count($Set['Nombre_Comun']) ;$i++)
		{
		$Where.="A.Nombre_Comun = '".$Set['Nombre_Comun'][$i]."' or ";
		}
		$Where=substr($Where,0,-3);
	}
	//Grupo de trabajo
	$Query=$Inner.$Where;//" where ".preg_replace("/ and/","",$Where,1);
	
	//preg_replace("/.* where/","",$Where);
//$Query=$Inner.$Where;
	 //$consulta="select * from Anotacion where Bio_Gen = '".$_POST['txtBioGen']."',""
	 //Realizar la consulta por medio de shell es más rápido¡¡
	 //El primer parámetro es la BD el segundo parámetro es la consulta y el tercer donde se va a generar el arcchivo
	//echo $Query;
	 system( "echo \"$Query\" > ./../Consultas/uno");
   return $Query;

/*$Obj= array('url'=>'none','Size'=>formatSizeUnits(1000),'Sizefa'=>1000,'Name'=>$name, 'NumReads'=>($Num_Lines/2),'Reads'=>$Reads);
	
	echo json_encode($Obj);
	*/ 
}
function Select($Table)
{
	$Select="$Table.Id_Biologico, $Table.Nombre, $Table.Inicio, $Table.Fin, $Table.Chr, $Table.Sentido, $Table.Missmatches, Table.Numero_Moleculas ";
	//Los campos que se deben obtener de la consulta
	/*if(!empty($_POST['Id_Biologico_Field']) ){
		( $_POST['Id_Biologico_Field']=="true")?$Select.="$Table.Id_Biologico, ":true;
	}
	if(!empty($_POST['Genoma_Field'])){
		($_POST['Genoma_Field']=="true")?$Select.="$Table.Nombre, ":true;
	}
	if(!empty($_POST['Inicio_Field'])){
		($_POST['Inicio_Field']=="true")?$Select.="$Table.Inicio, ":true;
	}
	if(!empty($_POST['Fin_Field'])){
		
		($_POST['Fin_Field']=="true")?$Select.="$Table.Fin, ":true;
	}
	if(!empty($_POST['Chr_Field'])){
		($_POST['Chr_Field']=="true")?$Select.="$Table.Chr, ":true;
	}
	if(!empty($_POST['Sentido_Field'])){
		($_POST['Sentido_Field']=="true")?$Select.="$Table.Sentido, ":true;
	}
	if(!empty($_POST['Mismatches_Field'])){
		($_POST['Mismatches_Field']=="true")?$Select.="$Table.Missmatches, ":true;
	}
	if(!empty($_POST['Numero_Moleculas'])){
		($_POST['Numero_Moleculas']=="true")?$Select.="$Table.Numero_Moleculas, ":true;
	}*/
	return $Select;
}



?>
