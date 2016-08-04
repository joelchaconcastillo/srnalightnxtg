<html>
<body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js">
</script>
<link rel="stylesheet" href="http://jquery.bassistance.de/validate/demo/site-demos.css">
<script src="js/jquery.validate.js" type="text/javascript"></script>
<script>
var Information=0;
$(document).ready(function(){
	
	$.post("Login_Server.php",{Origen:"CUsers"},
				function(data)
				{
					Information=data;
				},'json');

	jQuery.validator.addMethod("notEqualField", function(value, element, param) 
{
	var Existe=true;
	for(i=0;i<Information[param].length;i++)
	{
		if(Information[param][i].toUpperCase()==value.toUpperCase())
		{
		 	Existe = false;	
		}
	}
   		return Existe;		
				
  //return  value != param;
}, "This already exist"); 	
//Crear una nueva cuenta
$("#Register").click(function()
{
	 if($("#NewForm").valid())
	 {
		 
		 alert("We will send you a email more less on ten minutes \"from: TransciptomeGene@chacon.com\" remember check the spam.");
		
		 $.post("Login_Server.php",
			{
			   Origen: "Registrar_Invitacion",
			   User: $("#User").val(),
			   Pass: $("#Pass").val(),
			   Name: $("#Name").val(),
			   LastName: $("#LastName").val(),
			   Email: $("#email").val(),
			   Id_Grupo: '<?php echo base64_decode($_REQUEST['Id_Grupo']); ?>'
			},
			function(data,status)
			{
				setTimeout("location.href='./Login.htm'", 0); 
				$("#PassL").val("");
				$("#PassC").val("");
				$("#UserL").val("");
				$("#User").val("");
			    $("#Pass").val("");
			    $("#Name").val("");
			    $("#LastName").val("");
			    $("#email").val("");
				
			}
			);
	 }
});
$( "#NewForm").validate({
	
  rules: {
    User: {
		required: true,
		notEqualField: "Cuenta",
		minlength: 2,
	},
	Pass:{
		required: true,
		minlength: 6
	},
	confirm_password: {
				required: true,
				minlength: 6,
				equalTo: "#Pass"
			},
	Name: "required",
	LastName: "required",
	email: {
		required: true,
		email: true,
		notEqualField: "Email"
	}
  }
});



	
});
</script>

<div id="Registrar" >
<form id="NewForm"> 

<label>Username:</label><input type="text" id="User" name="User"/><br />
<label>Pass:</label><input type="password" id="Pass" name="Pass" /><br />
<label>Confirm Pass:</label><input type="password" id="PassC" name="confirm_password" /><br/>
<label>Name(s):</label><input type="text" name="Name" id="Name" /><br />
<label>Last Names:</label><input type="text" name="LastName" id="LastName" /><br />
<label>e-mail:</label><input type="text" name="email" id="email" /><br />
<input type="button" id="Register" value="Register"/>
</form>
</div>

</div>
</body>
</html>
