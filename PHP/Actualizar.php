<!DOCTYPE html>
<html>
<head>
<style>

</style>
<script>
$(document).ready(function(e) {
	$.post("Actualizar_Server.php",{
		Origen:"Consulta"
	},function(Obj)
	{
		$("#Name").html(Obj.Name);
		$("#Last_Name").html(Obj.Last_Name);
		$("#Email").html(Obj.Email);
		$("#Nick_Name").html(Obj.Nick_Name);
	},'json'
	);
});


</script>
</head>
<body>	
	<div id="Informacion">
    <span id="Name"></span>
    <span id="Last_Name"></span>
    <span id="Email"></span>
    <span id="Nick_Name"></span>
    </div>

</body>
</html>
