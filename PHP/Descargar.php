<?php 
$id=urldecode($_GET["id"]);
$name=urldecode($_GET["name"]);
$name=str_replace("%2F","/",$name);
$enlace =  $id;
header("Content-Disposition: attachment; filename=".$id."\n\n");
header("Content-Type: application/octet-stream");
header("Content-Length: ".filesize($enlace));
readfile($enlace);
((!empty($name))&& preg_match("/Consultas/",$name))?shell_exec("rm -fr $name"):true;
?>