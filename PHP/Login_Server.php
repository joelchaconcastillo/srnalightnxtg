<?php
//include("MySQL.php");
include("New_Server.php");
switch($_REQUEST['Origen'])
{
case "Informacion_Usuario":
Informacion_Usuario();
break;
case "Listar_Usuarios":
Listar_Usuarios();
break;
case "Agregar_Usuario":
Agregar_Usuario();
break;
case "Correo_Recuperacion":
Correo_Recuperacion();
break;
case "Verificar_Usuario":
Verificar_Usuario();
break;
case "Registrar_Invitacion":
Registrar_Invitacion();
break;
case "Menu_Permisos":
Menu_Permisos();
break;
case "Logout":
session_start();
session_destroy();
break;
}
function Informacion_Usuario()
{
	session_start();
	$Id_Cuenta=$_SESSION['Id_Cuenta'];
	$Conexion=Abrir_Conexion();
	$Resultado=mysql_query("SELECT * FROM Cuenta WHERE Id_Cuenta = $Id_Cuenta", $Conexion);
	$row=mysql_fetch_assoc($Resultado);
	Cerrar_Conexion($Conexion);
	echo json_encode($row);
}
function Menu_Permisos()
{
	session_start();
	$Sesion_Activa="";
	$Tipo_Cuenta = $_SESSION['Permission'];
	if(isset($_SESSION['Cuenta']))
	$Sesion_Activa="true";
	echo json_encode(array('Script'=>$_SESSION['Script'], 'Activa' => $Sesion_Activa, 'Tipo_Cuenta' => $Tipo_Cuenta));
}
function Registrar_Invitacion()
{
	
	$Conexion=Abrir_Conexion();
   $values="null,"."'".$_POST['User']."','".$_POST['Pass']."','User','".$_POST['Name']."','".$_POST['LastName']."','".$_POST['Email']."',1";
	 mysql_query("insert into Cuenta values($values)",$Conexion);
     mysql_query("insert into Miembros_Grupo values(".$_POST['Id_Grupo'].",".mysql_insert_id().");",$Conexion);
	JBrowse_data($Conexion);
	Cerrar_Conexion($Conexion);
}
function Verificar_Usuario()
{ 
	$Usuario=$_POST['Usuario'];	
	$Pass= $_POST['Pass'];
	$Mensaje="";
	$Conexion=Abrir_Conexion();
	$Script="#";
	$Aceptado=0;
#	$Resultado=mysql_query("SELECT COUNT(*) FROM Cuenta where Cuenta = '$Usuario' AND Pass = '$Pass'",$Conexion);
	$Resultado=mysqli_query($Conexion, "SELECT COUNT(*) FROM Cuenta where Cuenta = '$Usuario' AND Pass = '$Pass'");

	#$row=mysql_fetch_array($Resultado);
	$row=mysqli_fetch_array($Resultado);
		if($row[0]==1)
		{
			$row=Verificacion_Correo($Usuario, $Pass, $Conexion);
			if($row['Confirmacion'])
			{	
				session_start();
				$_SESSION['Cuenta']=$row['Cuenta'];
				$_SESSION['Permission']=$row['Tipo_Cuenta'];
				$_SESSION['Id_Cuenta']=$row['Id_Cuenta'];
				$_SESSION['Email']=$row['Email'];
				$Script="./../index.html";
				if($_SESSION['Permission']=="User")
				$_SESSION['Script']="./HTML/Menu_Superior.html";
				else
				$_SESSION['Script']="./HTML/Menu_Superior_Administrador.html";
				$Aceptado=1;
			}
			else
			{
				$Mensaje="You need to confirm your email or try \"Remember Pass\"";	
			}
		}
		else
		{
			$Mensaje="User or Pass incorrect please check your information";
		}
		
	Cerrar_Conexion($Conexion);
	echo json_encode(array('Mensaje' => $Mensaje, 'Script' => $Script, 'Aceptado' => $Aceptado));
}
function Verificacion_Correo($Usuario, $Pass, $Conexion)
{
	#$Resultado=mysql_query("SELECT * FROM Cuenta where Cuenta = '$Usuario' AND Pass = '$Pass'",$Conexion);
	$Resultado=mysqli_query($Conexion, "SELECT * FROM Cuenta where Cuenta = '$Usuario' AND Pass = '$Pass'");
	$row=mysqli_fetch_assoc($Resultado);
	return $row;
}
function Listar_Usuarios()
{
		$conexion=Abrir_Conexion();
		$q = mysql_query("select Cuenta, Email from Cuenta where Tipo_Cuenta='User';",$conexion);
		$Resultado=array("");
		while($row =mysql_fetch_assoc($q))
		{
			$Resultado['Cuenta'][]=$row['Cuenta'];
			$Resultado['Email'][]=$row['Email'];
}
echo json_encode($Resultado);	
Cerrar_Conexion($conexion);
}
function Correo_Recuperacion()
{
		$Conexion=Abrir_Conexion();
        $Resultado = mysqli_query($Conexion, "SELECT * FROM Cuenta WHERE Email = '".$_POST['Email']."'");
	    $row =mysqli_fetch_assoc($Resultado);
        $usuario=$row['Cuenta'];
        $pass=$row['Pass'];
        JBrowse_data($Conexion,$usuario);
        $permiso="User";
		$Trash=basename($_POST['URL']);
        $URL=substr($_POST['URL'],0,-1*(strlen($Trash)+1));
		$cabeceras = 'From: TransciptomeGene@chacon.com' . "\r\n" ;
        $mensaje = 'Please check the next link to recover your pass: '.$URL.'/Recover.php?Id='.base64_encode($_POST['Email'])."&User=".base64_encode($usuario)."&Pass=".base64_encode($pass);
        mail($_POST['Email'],'Recover Pass',$mensaje,$cabeceras);
		Cerrar_Conexion($conexion);

}
function Agregar_Usuario()
{
	
	$Conexion=Abrir_Conexion();
	$Usuario=$_POST['User'];
	$pass=$_POST['Pass'];
    $Trash=basename($_POST['URL']);
    $URL=substr($_POST['URL'],0,-1*(strlen($Trash)+1));
	$cabeceras = 'From: TransciptomeGene@chacon.com' . "\r\n" ;
	$mensaje = 'Please check the next link to activate your account: '.$URL.'/Register.php?Id='.base64_encode($_POST['Email'])."&User=".base64_encode($Usuario)."&Pass=".base64_encode($pass);
    $values="null,"."'$Usuario','$pass','User','".$_POST['Name']."','".$_POST['LastName']."','".$_POST['Email']."',0";
    mysql_query("insert into Cuenta values($values)",$Conexion);
   
	JBrowse_data($Conexion);
	mail($_POST['Email'],'Activate account',$mensaje,$cabeceras);
	Cerrar_Conexion($Conexion);
}


?>
