<!DOCTYPE html>
<!-- saved from url=(0017)http://osgjs.org/ -->
<html lang="en">
   <head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <!-- <link rel="shortcut icon" href="http://osgjs.org/assets/favicon.png">
-->
    <title>sRNA</title>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="./../css/normalize.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="./../css/screen.css">
    <link href="./../css/bootstrap-3.1.1-dist/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="./../css/jquery-ui.css">
<script type="text/javascript"> var URL='<?php echo $_SERVER['HTTP_HOST'];?>' </script>
<script src="./../js/jquery-1.9.1.min.js"></script>
<script src="./../js/jquery-migrate-1.2.1.min.js"></script>
<script src="./../js/jquery.balloon.js"></script>
<script src="./../js/jquery-ui.js"></script>
<script src="./../js/Controles.js"></script>
<script src="./../js/Send.js"></script>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>-->
<script src="./../js/jquery.balloon.js"></script><!--
<script src="js/socket.io-stream.js"></script>-->
<script src="./../js/sonic.js"></script>
<script src="./../js/jquery.validate.js" type="text/javascript"></script>
<script src="./../js/Validacion.js"></script>
<script src="./../js/Help.js"></script>
<script src="./../js/Autocompletar.js"></script>
<script type="text/javascript" src="./../js/jsapi"></script>

    <script type="text/javascript">

      google.load("visualization", "1", {packages:["corechart"]});
     // google.setOnLoadCallback(UrlFile);
      
    </script>
<!--<link rel="stylesheet" type="text/css" href="css/style.css" />
-->

<style>
.Content
{
		left:200px;
		right:100px;
		position:absolute;
        margin: 0px 0px 0px 10px;
		-moz-box-shadow: 0px 4px 3px #000;
    	-webkit-box-shadow: 0px 4px 3px #000;
        background:white;
		padding:15px;
}
.Menu_Derecho
{
        position:fixed;
        right:0;
        width:160px;
        color:black;
        height: 20px;
        text-shadow:5px 5px 5px #000000;
		z-index:1;
/*Border*/

border:solid 0px #000000;

/*Box-Shadow*/
color:white;
-moz-box-shadow: 6px -16px 16px #000000;
-webkit-box-shadow: 6px -16px 16px #000000;
box-shadow: 6px -16px 16px #000000;
background:black;
opacity:0.5;
}
.Content_Derecho
{
   height:400px;
   background:black;
   border:solid 1px #000000;
   overflow:auto;
}
.Menu_Izquierdo
{
        position:absolute;
        left:0;
        width:200px;
        color:black;
        height:90%;
		-moz-box-shadow: 3px 0px 5px #000000;
		-webkit-box-shadow: 3px 0px 5px #000000;
		box-shadow: 3px 0px 5px #000000;
		overflow-y:auto;
		
}
.Close_General
{
        cursor:pointer;
        float:right;
}

#Hide:hover
{
		background:black;
		cursor:pointer;
		-moz-box-shadow: -3px -1px 32px #000000;
		-webkit-box-shadow: -3px -1px 32px #000000;
		box-shadow: -3px -1px 32px #000000;
		border:none 5px #000000;
		-moz-border-radius: 31px;
		-webkit-border-radius: 31px;
		border-radius: 31px,
		color:black;	
}
#Show:hover
{
		background:black;
		cursor:pointer;
		-moz-box-shadow: -3px -1px 32px #000000;
		-webkit-box-shadow: -3px -1px 32px #000000;
		box-shadow: -3px -1px 32px #000000;
		border:none 5px #000000;
		-moz-border-radius: 31px;
		-webkit-border-radius: 31px;
		border-radius: 31px,
		color:black;	
}
body{
        text-align:center;
        background: white;
}
a:link
{
        color:#FFF;
}
a:visited
{
        color:#FFF;
}
.clase_cargando {
		/*visibility:hidden;
		*/position:absolute;
		top:0px;
		left:0px;
		width:100%;
		height:100%;
}
.Window
{
	background:white;
}
.Flecha:hover
{
   opacity:0.5;
   cursor:pointer;
  }
.Flecha
{
   float:right;
   position:relative;
   right:100px;
   cursor:pointer;
   top:40px;
}
</style>
<script>
$(document).ready(function(e) {
Id_HTOP="";
$("#Hide").click(function(e) {
    $(".Menu_Derecho").animate(
		{right:'-130px'});
		$("#Hide").hide();
		$("#Show").show();
});
$("#Show").click(function(e) {
    $(".Menu_Derecho").animate(
		{right:'0px'});
		$("#Hide").show();
		$("#Show").hide();
				});
     Menu_Derecho();
    /*$(".Menu_Derecho").animate({right:'-100px'},5000);
        $(".Menu_Derecho").hover(function()
        {
                $(this).animate({right:'0px'});
        },function(){
                $(this).animate({right:'-100px'});
        });*/
});
function Menu_Derecho()
{
   $("#Content_Derecho").load("Procesos_Derecho.html");
}
function Close_div(Name)
{
        if(confirm("Really do you want close and remove this library?"))
        {
                Remove_pop(Name);
                $("#Content_Pop_Experiment").fadeOut();
                 $("#Minilabel_"+Name).remove();
                 $("#Content_Experiment_"+Name).remove();
                 Eliminar(Name);
        }
         
}
function Show_MiniLabel(Name)
{
        $("#Content_Experiment_"+Name).slideToggle();
        $("#Content_Pop_Experiment").slideToggle();
        Remove_pop(Name);
        
}
function Eliminar(Name)
{
        $.post("Libreria_Server.php",
                        {
                           Name: Name,
                           Origen: "Eliminar_Libreria"
                        });
}
function Cerrar_Sesion()
{
        $.post("Conexion_Ajax.php",{
                Origen: "Logout"
        },function () {
                setTimeout("location.href='./Login.htm'", 0);   
        
});
}
function Cargar_Logo()
{
   $("#Central").html('<img src="./Images/science8.png" alt="" class="logo">');
}
 function Cambiar(url)
   {
           Drop_Baloons();
		  
                $("#Central").load(url,function(data)
                {
                        
                        //$("#Content_Experiment").slideDown(600);
                });
                
		
         return false;
        
        
   }
function Abrir_Pipe(url,Id_HTOP2)
{
    		Drop_Baloons();
		Id_HTOP=Id_HTOP2;	
                $.post("Procesos_Derecho_Server.php",
		{ Origen:"Sesion_Datos",Id_HTOP:Id_HTOP},
		function(data)
		{
		   $("#Central").load(url,function(data)
                   {
                   });
	        });
         return false;
        

}
function Siguiente_Pipe()
{
  $.post("Procesos_Derecho_Server.php",{Origen:"Proceso_Siguiente",Id_HTOP:Id_HTOP},
         function(data)	 
	{
	  Cargar_Menu_Derecho();
	   Abrir_Pipe(data.Comando,data.Id_HTOP);
	 },'json'); 

}
</script>

    </head>

<body data-spy="scroll" data-target=".navbar-collapse">
<!-- Header / Navigation -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="">
                <img src="./../Images/science8.png" alt="" width="32" height="32" class="logo">
                sRNA
            </a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
               <?php
		session_start();
             if(isset($_SESSION['Permission']))
		{
		if($_SESSION['Permission']=="Administrator")
		{?>
        <li class="active"><a href="#about">About<span class="glyphicon glyphicon-question-sign" style="padding-left:5px" ></span></a></li>
        <li onClick="Cambiar('./../HTML/New.htm')" ><a href="#">Accounts<span class="glyphicon glyphicon-user" style="padding-left:5px"></span></a></li>
        <li onClick="Cambiar('./../PHP/IndexarGenoma.php')" ><a href="#">Genomes<span class="glyphicon glyphicon-paperclip" style="padding-left:5px"></span></a></li>
        <li onClick="Cambiar('./../PHP/Organismo.php')" ><a href="#">Organisms<span class="glyphicon glyphicon-tasks" style="padding-left:5px"></span></a></li>
       
		<li onClick="Cerrar_Sesion();"><a id="Logout" href="./../HTML/Login.html">Logout<span class="glyphicon glyphicon-off" style="padding-left:5px"></span></a></li>
		<?php
		}
		else if($_SESSION['Permission']=="User")
		{?>
		<li class="active"><a href="#about">About<span class="glyphicon glyphicon-question-sign" style="padding-left:5px" ></span></a></li>
                <li onClick="Cambiar('./../HTML/Libreria.html')" ><a href="#">Experiment<span class="glyphicon glyphicon-search" style="padding-left:5px"></span></a></li>
                <li onClick="Cambiar('./../PHP/Grupo.php')" ><a href="#">Groups<span class="glyphicon glyphicon-share" style="padding-left:5px"></span></a></li>
                <li onClick="Cambiar('./../PHP/ModificarLibrerias.php')"  ><a href="#">Library<span class="glyphicon glyphicon-book" style="padding-left:5px"></span></a></li>
                <li onClick="Cambiar('./../PHP/Consultas.php')" ><a href="#">Database<span class="glyphicon glyphicon-floppy-save" style="padding-left:5px"></span></a></li>
           	<li onClick="Cambiar('./../HTML/Load_JBrowse.html')"><a href="#">JBrowse<span class="glyphicon glyphicon-eye-open" style="padding-left:5px"></span></a><li>
		<li onClick="Cambiar('Actualizar.php')"><a href="#Information"><?php echo $_SESSION['Cuenta'] ?><span class="glyphicon glyphicon-wrench" style="padding-left:5px"></span></a></li> 
        <li onClick="Cerrar_Sesion();"><a id="Logout" href="./../HTML/Login.html">Logout<span class="glyphicon glyphicon-off" style="padding-left:5px"></span></a></li>
	     <?php
		}
		}
		else
		{
		  echo '<script>setTimeout("location.href=\'./Login.htm\'", 0); </script>';
		}
	    ?>
	    </ul>
        </div>
    </div>

</div>

<?php
if(isset($_SESSION['Permission']))
{
 if($_SESSION['Permission']=="User")
{?>
<div class="Menu_Derecho" id="Menu_Derecho">
               <span id="Hide" class="glyphicon glyphicon-hand-right" ></span>
                <span id="Show" class="glyphicon glyphicon-hand-left" style="display:none;" ></span>
               <span class="glyphicon glyphicon-tasks" style="width:140px;"><label style="padding-left:5px">Experiments</label></span>
                                <div id="Content_Derecho" class="Content_Derecho"></div>
                </div>
<?php }} ?>

<div id="Menu_Izquierdo" class="Menu_Izquierdo">
</div>
<!--/////////////-->
<div class="Content" id="Central">
		<img src="./../Images/science8.png" alt="" class="logo">			                             
</div>

</body>
</html>
