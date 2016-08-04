<?php 
include("MySQL.php");
switch($_REQUEST['Origen'])
{
   case "Cargar_Procesos":
   Cargar_Procesos();
   break;
   case "Baja_PIPE":
   Baja_PIPE();
   break;
   case "Sesion_Datos":
   Sesion_Datos($_POST['Id_HTOP']);
   break;
   case "Proceso_Siguiente":
   Pop_Proceso($_POST['Id_HTOP']);
   break;
}
function Ejecucion_Proceso($Id_HTOP, $PID)
{
   $Conexion=Abrir_Conexion();
   mysql_query("update HTOP set Activo = 1, PID = $PID where Id_HTOP = $Id_HTOP",$Conexion);
   Cerrar_Conexion($Conexion);
}
//Funciones que son parte del switch..
function Sesion_Datos($Id_HTOP)
{
   session_start();
   $Conexion=Abrir_Conexion();
   $Resultado=mysql_query("select * from HTOP H inner join Libreria L on L.Id_Libreria=H.Id_Libreria where H.Id_HTOP = ".$Id_HTOP,$Conexion);
   $row=mysql_fetch_assoc($Resultado);
   $_SESSION['Id_HTOP']=$row['Id_HTOP'];
   $_SESSION['Ruta']=$row['Ruta']; 
   $_SESSION['Archivo']=Archivo($row['Ruta']);
   Cerrar_Conexion($Conexion);
}
function Archivo($Ruta_Relativa)
{
     $Directorio=ls($Ruta_Relativa);
   foreach($Directorio as $Archivo)
   {
       if(basename($Archivo)=="File_Fasta")
          return "File_Fasta";
       else if(basename($Archivo)=="File_FastaQ")
          return "File_FastaQ";
       else if(basename($Archivo)=="File_CSFastaQ")
          return "File_CSFastaQ";
       else if(basename($Archivo)=="File_Unknown")
         return "File_Unknown";
       else if(basename($Archivo)=="File_Qual" || basename($Archivo)=="File_CSFasta")
         return Check_Solid($Archivo,$Ruta_Relativa);
      else return false;
   }
}
function Check_Solid($Archivo1,$Ruta_Relativa)
{
    $Directorio=ls($Ruta_Relativa);
    foreach($Directorio as $Archivo2)
    {
       if(basename($Archivo1) == "File_Qual" && basename($Archivo2) == "File_CSFasta")
          return "File_CSFastaQ+";
	   if(basename($Archivo1) == "File_CSFasta" && basename($Archivo2) == "File_Qual")
          return "File_CSFastaQ+";
    }
   return false;
}
function ls($Ruta_Relativa)
{
   if(is_dir($Ruta_Relativa))
   return glob($Ruta_Relativa."/{*}",GLOB_BRACE);
   else return false;
}
function Cargar_Procesos()
{
   session_start();
   $Conexion=Abrir_Conexion();
   $Id_Cuenta=$_SESSION['Id_Cuenta'];
   $Resultado=mysql_query("select * from HTOP H inner join Libreria L on L.Id_Libreria = H.Id_Libreria where L.Id_Cuenta = $Id_Cuenta",$Conexion);
   while( $row=mysql_fetch_assoc($Resultado))
   {
      /*echo '<script>Balloon_Help("Open_Process'.$row['Id_HTOP'].'","Select to open process");</script>';*/
      if($row['Activo']==1)
      echo "<div class='Subcontent_Menu_Derecho' style='background:red;'>";
      else
      {
        $Primer_Elemento=Primer_Elemento($row['Id_HTOP']);
         echo "<div class='Subcontent_Menu_Derecho' style='background:green;'>";
         echo "<div class='glyphicon glyphicon-open' id='Open_Process".$Primer_Elemento['Id_PIPE']."' onclick=\"Abrir_Pipe('".$Primer_Elemento['Comando']."','".$row['Id_HTOP']."');\" style='float:left; cursor:pointer;'></div>";
      }
      echo "<div class='Close_General glyphicon glyphicon-remove-sign' onclick=Baja_HTOP('".$row['Id_HTOP']."','".$row['Ruta']."','".$row['Id_Libreria']."')></div>";
      echo "<label>Name: ".$row['Nombre']."</label><br>";
      echo "<label>Step: ".$row['Numero_Pipe_Actual']." of ".$row['Numero_Pipes']."</label>";
      echo "</div>";
   }
   Cerrar_Conexion($Conexion);
}
function Baja_PIPE()
{
   $Conexion=Abrir_Conexion();
   mysql_query("delete from PIPE where Id_HTOP =".$_POST['Id_HTOP'],$Conexion);
   mysql_query("delete from HTOP where Id_HTOP =".$_POST['Id_HTOP'],$Conexion);
   mysql_query("delete from Libreria where Id_Libreria =".$_POST['Id_Libreria'],$Conexion);
   Cerrar_Conexion($Conexion);
   rmdir($_POST['Ruta']);
}
function Primer_Elemento($Id_HTOP)
{
   $Conexion=Abrir_Conexion();
   $Resultado= mysql_query("select * from PIPE where Id_HTOP = ".$Id_HTOP." order by Id_PIPE limit 1");
   $row=mysql_fetch_assoc($Resultado);
   Cerrar_Conexion($Conexion);
   return $row;
}
function Pop_Proceso($Id_HTOP)
{
   
   $File_Borrar = Primer_Elemento($Id_HTOP);
   $Conexion = Abrir_Conexion(); 
   mysql_query("delete from PIPE where Id_PIPE = ".$File_Borrar['Id_PIPE'],$Conexion);
   Cerrar_Conexion($Conexion);   
   $Ultimo_Elemento = Primer_Elemento($Id_HTOP);
   Actualizar_HTOP($Id_HTOP);
   echo json_encode($Ultimo_Elemento);
}
function Actualizar_HTOP($Id_HTOP)
{
   $Conexion=Abrir_Conexion();
   $Resultado=mysql_query("select * from HTOP where Id_HTOP = ".$Id_HTOP,$Conexion);
   $row=mysql_fetch_assoc($Resultado);
   $Numero_Pipe=$row['Numero_Pipe_Actual'];
   $Numero_Pipe++;
   mysql_query("update HTOP set Numero_Pipe_Actual = ".$Numero_Pipe,$Conexion);
   Cerrar_Conexion($Conexion);
}
function Borrar_HTOP($Id_HTOP)
{
   $Conexion=Abrir_Conexion();
   mysql_query("delet from HTOP where Id_HTOP = $Id_HTOP",$Conexion);
   Cerrar_Conexion($Conexion);

}	
?>
