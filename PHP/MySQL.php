<?php
function mysql_query($Query, $Conexion){
	if(!$Query)return "";
	return mysqli_query($Conexion, $Query);
}
function mysql_fetch_assoc($input){
	if(!$input)return "";
   return mysqli_fetch_assoc($input);
}
function mysql_fetch_array($input){
	if(!$input)return "";
   return mysqli_fetch_array($input);
}
Abrir_Conexion();
function Abrir_Conexion()
{
#	$conexion = mysql_connect("","joel","chacon") or die('No pudo conectarse: ' . mysql_error());	
	$conexion = new mysqli('', 'joel', 'chacon', 'mydb');
#       $mysqli->set_charset("utf8");
#	echo($conexion);
#	if (!$conexion) {
#           die('Conexion error: ' . mysql_error());
#        }
	#       mysql_select_db('mydb', $conexion) or die('Could not select database.');
return $conexion;
}
function Cerrar_Conexion($conexion)
{
	mysqli_close($conexion);
}
function formatbytes($file, $type)
{
   switch($type){
      case "KB":
         $filesize = filesize($file) * .0009765625; // bytes to KB
      break;
      case "MB":
         $filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
      break;
      case "GB":
         $filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
      break;
   }
   if($filesize <= 0){
      return $filesize = 'unknown file size';}
   else{return round($filesize, 2).' '.$type;}
}

?>
