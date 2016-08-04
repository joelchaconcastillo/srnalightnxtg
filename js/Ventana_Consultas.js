function Mostrar_Ventana(Element)
{
	var modal;
	//Eliminar la ventana modal si existe
	if($("#TextBrowser").length)
	$("#TextBrowser").remove();
	modal='<!-- Modal --><div class="modal fade" id="TextBrowser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
	modal+='<div class="modal-dialog" style="width:90%;">';
	modal+='  <div class="modal-content">';
	modal+='      <div class="modal-header">';
	modal+='      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
	modal+='      <h4 class="modal-title" id="myModalLabel">Text Browser</h4>';
	modal+='    </div>';
	modal+='    <div class="modal-body">';
	modal+='    <label class="control-label">Please insert the line to start:</label><br>';
	modal+='      <input type="text" id="Numero_Linea" value="1" class="form-control">';
	modal+='     <label class="control-label"> Number of lines:</label>';
	modal+='      <input type="text" id="Longitud" value="1000" class="form-control">';
	modal+='     <label class="control-label"> Search substring:</label>';
	modal+='      <input type="text"  class="form-control" id="Patron"><br>';
	modal+='      <div class="btn btn-success" id="Enviar_Filtro" onclick="Enviar_Filtro(\''+$(Element).attr("Id_Libreria")+'\')" >Go</div><br>';
	modal+='      <div id="Contenedor_Resultado">';
	modal+='     </div>';
	modal+='    </div>';
	modal+='    <div class="modal-footer">';
	modal+='      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
	modal+='      <button type="button" id="Descargar_Busqueda" onclick="Descargar_Busqueda(\''+$(Element).attr("Id_Libreria")+'\')" class="btn btn-primary disabled">Download Search</button>';
	modal+='    </div>';
	modal+='  </div>';
	modal+='</div>';
	modal+='</div>';
	
	$("body").append(modal).promise().done(function(){$("#TextBrowser").modal();});

}
function Descargar_Busqueda(Id_Libreria)
{
	$("#Descargar_Busqueda").html('Loading <img src="./Images/ajax-loader2.gif">');
	$("#Descargar_Busqueda").attr('class','disabled btn btn-default');
	 $.post("./PHP/Consulta_Server.php",{
				Origen:"Descargar_Filtro",
				Patron:$("#Patron").val(),
				Numero_Linea: $("#Numero_Linea").val(),
				Longitud:$("#Longitud").val(),
				Id_Libreria: Id_Libreria
			},function (data) 
			{
				setTimeout("location.href='./Comodin/"+data+"'", 0);
				$("#Descargar_Busqueda").html('Download Search');
				$("#Descargar_Busqueda").attr('class','btn btn-primary');
			});
}
function Enviar_Filtro(Id_Libreria)
{
	
	$("#Enviar_Filtro").html('Loading <img src="./Images/ajax-loader2.gif">');
	$("#Enviar_Filtro").attr('class','disabled btn btn-default');
	 $.post("./PHP/Consulta_Server.php",{
				Origen:"Enviar_Filtro",
				Patron:$("#Patron").val(),
				Numero_Linea: $("#Numero_Linea").val(),
				Longitud:$("#Longitud").val(),
				Id_Libreria: Id_Libreria
			},function (data) 
			{
				$("#Contenedor_Resultado").html(data).promise().done(function()
				{
					
					 var table = $('#Tabla_Texto').dataTable(
						{  
						  "order": [[ 0, "desc" ]],
						/*"columnDefs": [
								{ "width": "50", "targets": "1" }
							  ],
							*/ scrollY:        "500px",
							 scrollX:        true,
							 scrollCollapse: true,
							 "bFilter": false,
							"pagingType": "full_numbers",
							"autoWidth": false,
							dom: 'T<"clear">lfrtip'
					    } 
			           );
					   $("th").css('text-align','center');
					   $("th").css('vertical-align','middle');
					   $("td").css('text-align','center');
					   $("td").css('vertical-align','middle');
					 $("#Enviar_Filtro").html('Go');
					 $("#Enviar_Filtro").attr('class','btn btn-success');
					 $("#Descargar_Busqueda").attr('class','btn btn-primary');
				});		
			
			});
}
function Descargar(Element)
{
	var Clase="";
    $(Element).html('Loading <img src="./Images/ajax-loader2.gif">');
     Clase= $(Element).attr('class');
    $(Element).attr('class','disable');
	$.post("./PHP/Grupo_Server.php",{Origen:"Descargar",Id_Libreria:$(Element).attr("Id_Libreria")},
	function(data)
	{
		setTimeout("location.href='./"+data.Url+"'", 0);
		$(Element).attr('class',Clase);
		$(Element).html('Download');

	},'json');
}
function Mostrar_Ventana_Archivo(Archivo)
{
	var modal;
	//Eliminar la ventana modal si existe
	if($("#TextBrowser").length)
	$("#TextBrowser").remove();
	 modal='<!-- Modal --><div class="modal fade" id="TextBrowser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
  modal+='<div class="modal-dialog" style="width:90%;">';
  modal+='  <div class="modal-content">';
  modal+='      <div class="modal-header">';
  modal+='      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
  modal+='      <h4 class="modal-title" id="myModalLabel">Text Browser</h4>';
  modal+='    </div>';
  modal+='    <div class="modal-body">';
  modal+='    <label class="control-label">Please insert the line to start:</label><br>';
  modal+='      <input type="text" id="Numero_Linea" value="1" class="form-control">';
  modal+='     <label class="control-label"> Number of lines:</label>';
  modal+='      <input type="text" id="Longitud" value="1000" class="form-control">';
  modal+='     <label class="control-label"> Search substring:</label>';
    modal+='      <input type="text"  class="form-control" id="Patron"><br>';
    modal+='      <div class="btn btn-success" id="Enviar_Filtro" onclick="Enviar_Filtro_Archivo(\''+Archivo+'\')" >Go</div><br>';
    modal+='      <div id="Contenedor_Resultado">';
    modal+='     </div>';
  modal+='    </div>';
  modal+='    <div class="modal-footer">';
  modal+='      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
  modal+='      <button type="button" id="Descargar_Busqueda" onclick="Descargar_Busqueda_Archivo(\''+Archivo+'\')" class="btn btn-primary disabled">Download Search</button>';
  modal+='    </div>';
  modal+='  </div>';
  modal+='</div>';
  modal+='</div>';
	
	$("body").append(modal).promise().done(function(){$("#TextBrowser").modal();});

}
function Descargar_Busqueda_Archivo(Archivo)
{
	$("#Descargar_Busqueda").html('Loading <img src="./Images/ajax-loader2.gif">');
	$("#Descargar_Busqueda").attr('class','disabled btn btn-default');
	 $.post("./PHP/Consulta_Server.php",{
				Origen:"Descargar_Filtro_Archivo",
				Patron:$("#Patron").val(),
				Numero_Linea: $("#Numero_Linea").val(),
				Longitud:$("#Longitud").val(),
				Archivo: Archivo
			},function (data) 
			{
				setTimeout("location.href='./Comodin/"+data+"'", 0);
				$("#Descargar_Busqueda").html('Download Search');
				$("#Descargar_Busqueda").attr('class','btn btn-primary');
			});
}
function Enviar_Filtro_Archivo(Archivo)
{
	
	$("#Enviar_Filtro").html('Loading <img src="./Images/ajax-loader2.gif">');
	$("#Enviar_Filtro").attr('class','disabled btn btn-default');
	 $.post("./PHP/Consulta_Server.php",{
				Origen:"Enviar_Filtro_Archivo",
				Patron:$("#Patron").val(),
				Numero_Linea: $("#Numero_Linea").val(),
				Longitud:$("#Longitud").val(),
				Archivo: Archivo
			},function (data) 
			{
				$("#Contenedor_Resultado").html(data).promise().done(function()
				{
					 var table = $('#Tabla_Texto').dataTable(
						{  
						  "order": [[ 0, "desc" ]],
						/*"columnDefs": [
								{ "width": "50", "targets": "1" }
							  ],
							*/ scrollY:        "500px",
							 scrollX:        true,
							 scrollCollapse: true,
							 "bFilter": false,
							"pagingType": "full_numbers",
							"autoWidth": false,
							dom: 'T<"clear">lfrtip'
					    } 
			           );
					 $("#Enviar_Filtro").html('Go');
					 $("#Enviar_Filtro").attr('class','btn btn-success');
					 $("#Descargar_Busqueda").attr('class','btn btn-primary');
				});		
			
			});
}
function JBrowse(Ruta_Genoma)
{
	
			Loading(true);
				
					
					($("#Id_Biologico_txt").val())?Ruta_Genoma+="&loc="+$("#Id_Biologico_txt").val():true;
					
					if($("#Chr_txt").val())
					Ruta_Genoma+="&loc="+$("#Chr_txt").val();
					else
					{
						($("#Inicio_txt").val())?Ruta_Genoma+=":"+$("#Inicio_txt").val()+"..":true;
						if($("#Inicio_txt").val() && $("#Fin_txt").val())Ruta_Genoma+=$("#Fin_txt").val();
						else if(!$("#Inicio_txt").val() && $("#Fin_txt").val())Ruta_Genoma+=":0.."+$("#Fin_txt").val();
					}
					
					callIframe(Ruta_Genoma,function()
					{
					Loading(false,1000);
						
					});
				//$("#JBrowse_frame").attr();
				$("#JBrowse_frame").css(
				{
					"height":"500px",
					"width":"100%"
				});
				$("#JBrowse_frame").fadeIn();
		
}
function callIframe(url, callback) {
	
    $('#JBrowse_frame').attr('src', url);

    $('#JBrowse_frame').load(function() {
        callback(this);
    });
}

