
var obj = (
	{

		width: 100,
		height: 100,

		stepsPerFrame: 1,
		trailLength: 1,
		pointDistance: .02,
		fps: 30,

		fillColor: '#05E2FF',

		step: function(point, index) {
	
			this._.beginPath();
			this._.moveTo(point.x, point.y);
			this._.arc(point.x, point.y, index * 7, 0, Math.PI*2, false);
			this._.closePath();
			this._.fill();

		},

		path: [
			['arc', 50, 50, 30, 0, 360]
		]
	}
);
function Loading_Experiment(Enable,timeout,Element)
{
	if(Enable)
	{
	$("#Content_Experiment_"+Element).append('<div class="Loading_Center_'+Element+'"><div style="opacity:0.5" class="Loading_Inside_'+Element+'"></div></div>');
	$("#Content_Experiment_"+Element).append('<div class="Loading_'+Element+'"></div>');
		var  a = new Sonic(obj);
	 $(".Loading_Inside_"+Element).append(a.canvas);
	a.canvas.style.marginTop = (150 - a.fullHeight) / 2 + 'px';
	a.canvas.style.marginLeft = (150 - a.fullWidth) / 2 + 'px';
	a.play();
	$(".sonic").css(
	{
		"margin":"auto",
		"display":"block"
	});
	
		$(".Loading_Inside_"+Element).append('Loading');
		var Height="",width="";
		 //if($("#Content_Experiment_"+Element).height() > $("#Content_Pop_Experiment").height())
		  Height=$("#Content_Experiment_"+Element).height();
		 //else  Height=$("#Content_Pop_Experiment").height();
		
		 //if($("#Content_Experiment_"+Element).width() > $("#Content_Pop_Experiment").width())
		  width=$("#Content_Experiment_"+Element).width();
		 //else  width=$("#Content_Pop_Experiment").width();
		
		
		$(".Loading_"+Element).css(
		{
			"background":"black",
			"color":"white",
			"z-index":"999",
			"height": Height,
			"width": width,
			"position": "fixed",
			"top": "0px",
			"opacity": "0.85",
			"overflow":"auto"
			

		});
		

$(".Loading_Center_"+Element).css("display","inline-block");
$(".Loading_Center_"+Element).css("width","850px");
$(".Loading_Center_"+Element).css("z-index","1000");
$(".Loading_Center_"+Element).css("height","300px");
$(".Loading_Center_"+Element).css("display","inline-block");
$(".Loading_Center_"+Element).css("color","white");


		$(".Loading_Center_"+Element).center();
	}
else {
	 if(!$("#Content_Experiment_"+Element).is(':visible'))
        {
				/*$("#Minilabel_"+Element).showBalloon({
									offsetY: -2,
									contents:"Step Finished",
									position:"left",
									css:{
									backgroundColor:"red",
									border: "solid 1px black",
									color:"white"
									},
									showDuration: 0
									
								});*/
		}
				$(".Loading_Center_"+Element).fadeOut(timeout,function(){
					$(".Loading_Center_"+Element).remove(); 
				});
				
				$(".Loading_"+Element).fadeOut(timeout,function () {
					$(".Loading_"+Element).remove();
				});
				$(".Loading_Center_"+Element).remove();
				
				
		}
		
}
function Loading(Enable,timeout)
{

	if(Enable)
	{
	$("body").append('<div class="Loading_Center"><div style="opacity:0.5" class="Loading_Inside"></div></div>');
	$("body").append('<div class="Loading"></div>');
		var  a = new Sonic(obj);
	 $(".Loading_Inside").append(a.canvas);
	a.canvas.style.marginTop = (150 - a.fullHeight) / 2 + 'px';
	a.canvas.style.marginLeft = (150 - a.fullWidth) / 2 + 'px';
	a.play();
	$(".sonic").css(
	{
		"margin":"auto",
		"display":"block"
	});
	
		$(".Loading_Inside").append('Loading');
		
		$(".Loading").css(
		{
			"background":"black",
			"color":"white",
			"z-index":"999",
			"height": "100%",
			"width": "100%",
			"position": "fixed",
			"top": "0px",
			"opacity": "0.85"
			

		});
		

$(".Loading_Center").css("display","inline-block");
$(".Loading_Center").css("width","850px");
$(".Loading_Center").css("z-index","1000");
$(".Loading_Center").css("height","300px");
$(".Loading_Center").css("display","inline-block");
$(".Loading_Center").css("color","white");


		$(".Loading_Center").center();
	}
else {
				$(".Loading_Center").fadeOut(timeout,function(){
					$(".Loading_Center").remove(); 
				});
				
				$(".Loading").fadeOut(timeout,function () {
					$(".Loading").remove();
				});
				$(".Loading_Center").remove();
				
				
		}
		
}

$(document).ready(function(e) {
	jQuery.fn.ScreenOn = function () {
		
		$("body").append("<div id='Super_Content'></div>");
	$("#Super_Content").css(
	{
		"width":"100%",
		"height":"100%",
		"background":"black",
		"position":"fixed",
		"top":"0px",
		"left":"0px",
		"opacity":"0.5"
	});
	this.center().show().css({"z-index":"100"});
	this.css("top","20%");
    this.css("position","absolute");
}
jQuery.fn.ScreenOff = function () {
	this.hide();
	$("#Super_Content").remove();
	
}

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + 
                                                $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + 
                                                $(window).scrollLeft()) + "px");
    return this;
}
//Convertir formulario a JSON
jQuery.fn.MytoJson = function(options) {

    options = jQuery.extend({}, options);

    var self = this,
        json = {},
        push_counters = {},
        patterns = {
            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push":     /^$/,
            "fixed":    /^\d+$/,
            "named":    /^[a-zA-Z0-9_]+$/
        };


    this.build = function(base, key, value){
        base[key] = value;
        return base;
    };

    this.push_counter = function(key){
        if(push_counters[key] === undefined){
            push_counters[key] = 0;
        }
        return push_counters[key]++;
    };

    jQuery.each(jQuery(this).serializeArray(), function(){

        // skip invalid keys
        if(!patterns.validate.test(this.name)){
            return;
        }

        var k,
            keys = this.name.match(patterns.key),
            merge = this.value,
            reverse_key = this.name;

        while((k = keys.pop()) !== undefined){

            // adjust reverse_key
            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

            // push
            if(k.match(patterns.push)){
                merge = self.build([], self.push_counter(reverse_key), merge);
            }

            // fixed
            else if(k.match(patterns.fixed)){
                merge = self.build([], k, merge);
            }

            // named
            else if(k.match(patterns.named)){
                merge = self.build({}, k, merge);
            }
        }

        json = jQuery.extend(true, json, merge);
    });


    return json;
}


//Elementos tipo Radio
	   
	   $( ".Reporting" ).change(function() {
     var Valor=$("input[name='Reporting']:checked").val();
	 if(Valor=="-k")
	 {
		 $("#k_text").removeAttr('disabled');
	 }else
	 {
		  $("#k_text").attr('disabled','disabled');
	 $("#k_text").val("");
	 }
   
       });
	
});
//Función para habilitar input text relativos a un input Check, recibe el identificador del textbox
function Check(id,Path)
{
	Name=basename(Path);
if($("#"+Name+" #"+id).attr('disabled'))
	{
		$("#"+Name+" #"+id).removeAttr('disabled');
	}else
	{
		$("#"+Name+" #"+id).attr('disabled','disabled');
		$("#"+Name+" #"+id).val("");
	}
		
}	
function Cerrar_Ventana(Path)
{
	 Name=basename(Path);
	$("#Content_Experiment_"+Name).slideToggle(2000);
	$("#"+Name+" #Total_Meter").val(100);
}
function basename(path) {
    return path.replace(/\\/g,'/').replace( /.*\//, '' );
}
function Minion(Archivo,Path)
{
	Name=basename(Path);
	Loading(true);
	$.post("Identificar.php",{Origen:"Minion",Archivo:Archivo},
	function(data){
		Loading(false);
		data=data.replace(/\\r\\n/g, "<br />");
		$("#"+Name+" #Minion").html(data);
			});
}
function Flujo(Path)
{
	Name=basename(Path);
	Fase=$("input[name='"+Name+"Flujo']:checked").val();
	if(Fase=="Mapear")
	{
    $("#"+Name+" #Directorio").slideToggle(1000);
	$("#"+Name+" #Content_Adaptador").slideToggle(1000);
    $("#"+Name+" #Content_Flujo").fadeToggle(1500);
    $("#"+Name+" #Content_Mapeado").fadeToggle(0);
	}
	else
	{
		Esconder_Adaptador(Path);
		Esconder_Filtrado(Path);
	}
	
}
function btn_Stderr(Path)
{
	Name=basename(Path);
	 $("#"+Name+" #Log").slideToggle();
	
}
 /*function UrlFile(Path)
 {
	 var Name=basename(Path);
	 var curl=$("#"+Name+" #WGET").val();
	 var Obj={Path:Path,NameFile:basename(curl), URL:curl};
	 socket.emit('CURL',Obj);	

 }*/
 function Linechart(json,id,label,labelx,labely,Name)
 {
	  var options = {
		title:label,
		 hAxis: {title: labelx, titleTextStyle: {color: 'black'}},
		 vAxis: {title: labely, titleTextStyle: {color: 'black'}},
        width: 1200,
        height: 600,
        legend: { position: 'top', maxLines: 3 },
	bar: { groupWidth: '75%' },
        isStacked: true,
      }; 
	   var data = new google.visualization.DataTable(json);
     
	 drawChart(data,options,id,"Line",Name); 
 }
   function ChartFrecuency(json,id,label,labelx,labely,Name)
   {
	  
           var options = {
		title:label,
		 hAxis: {title: labelx, titleTextStyle: {color: 'black'}},
		 vAxis: {title: labely, titleTextStyle: {color: 'black'}},
        width: 600,
        height: 400,
        legend: { position: 'top', maxLines: 3 },
	bar: { groupWidth: '75%' },
        isStacked: true,
      }; 
	   var data = new google.visualization.DataTable(json);
     
	 drawChart(data,options,id,"Column",Name); 
   }
    function ChartFrecuencyNormal(json,id,label,labelx,labely,Name)
   {
	  
           var options = {
		title:label,
		 hAxis: {title: labelx, titleTextStyle: {color: 'black'}},
		 vAxis: {title: labely, titleTextStyle: {color: 'black'}},
        width: 1200,
        height: 600,
        legend: { position: 'top', maxLines: 3 },
	bar: { groupWidth: '75%' }
      }; 
	   var data = new google.visualization.DataTable(json);
     
	 drawChart(data,options,id,"Column",Name); 
   }
      function drawChart(data,options,id,type,Name) {
		 var chart;
		 switch(type)
		 {
			case "Column":
			chart = new google.visualization.ColumnChart($("#"+Name+" #"+id)[0]);		
			break; 
			case "Line":
			chart = new google.visualization.LineChart($("#"+Name+" #"+id)[0]);
			break;
		 } 
        chart.draw(data, options);
       
    
      }
function Show_Rules(Path)
{
var Name=basename(Path);
$("#"+Name+" #Patron").val("");
if($("#"+Name+" #Patron_btn").val()=="Don't use a pattern")
$("#"+Name+" #Patron_btn").val("I want to specity the format input");
else
$("#"+Name+" #Patron_btn").val("Don't use a pattern");

	$("#"+Name+" #Rules").slideToggle();
}
function Calcular_Adaptador(Path)
{
var Name=basename(Path);
Archivo=$("#"+Name+" #Archivo").val();
Minion(Archivo,Path);
}
function Append_Analyze(data)
{
	  ChartFrecuency(data.Frecuencia,"Analisis_Frecuencia","Nucleotide frequency per base for the lane sample input",'Base frequency by cycle','Frequency',data.Name);
	  ChartFrecuency(data.Longitud,"Analisis_Longitud","Read Length",'Read length','Number of reads',data.Name);
	  Linechart(data.Calidad,"Analisis_Calidad","Quality per cycle for the lane sample displayed as raw ASCII values",'Quality by cycle','Quality',data.Name);
	
}
function Append_Adapter(data)
{
	   ChartFrecuency(data.Frecuencia_Input,"Frecuencia_Input","Nucleotide frequency per base for the lane sample input",'Base frequency by cycle','Frequency',data.Name);
	   ChartFrecuency(data.Frecuencia_Output,"Frecuencia_Output","Nucleotide frequency per base for the lane sample output",'Base frequency by cycle','Frequency',data.Name);
	   ChartFrecuencyNormal(data.Longitud_Output,"Longitud_Output","Read length plot for ACTG sample following Reaper",'Read length following Reaper','Number of reads',data.Name);
	   Linechart(data.Calidad_Input,"Calidad_Input","Quality per cycle for the lane sample displayed as raw ASCII values",'Quality by cycle','Quality',data.Name);
	   Linechart(data.Complexity_Ouput,"Complexity_Adapter","Ranges cleaned read complexity by read length",'Sequence length (nt)','Number of reads',data.Name);

}
function Append_Filter(data)
{
	   ChartFrecuencyNormal(data.Longitud_Output,"Longitud_Output_Filter","Read length plot for ACTG sample following Reaper",'Read length following Reaper','Number of reads',data.Name);
	   Linechart(data.Complexity_Ouput,"Complexivity_Filter","Ranges cleaned read complexity by read length",'Sequence length (nt)','Number of reads',data.Name);

}
function Size(size) {
      
          
          if (size > 1024 * 1024)
            return (Math.round(size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
          else
           return (Math.round(size * 100 / 1024) / 100).toString() + 'KB';
		 
        
   }
   function Toggle_Div(Path,Id)
   {
	   var Name= basename(Path);
	   $("#"+Name+" #"+Id).slideToggle();
   }
 function myprompt(Header,Label,Body,Help,callback)
 {
	 $("body").append('<div id="Temp">'+Header+'<br><br><div class="Help" id="Help_prompt">?</div>'+Label+'<input type="text" id="Temp_text"><br><br><input type="button" style="margin:10px; padding:4px" id="Temp_btn_ok" value="Ok"><input type="button" id="Temp_btn_cancel" style="margin:10px; padding:4px" value="Cancel"><br>'+Body+'</div><div id="content_prompt"></div>');
	Balloon_Help("Help_prompt",Help,"bottom");

   
	 $("#content_prompt").css(
	 {
		 "background":"black",
		 "height": $(document).height(),
	  "width": $(document).width(),
			"z-index":"999",
			"position": "fixed",
			"top": "0px",
			"opacity": "0.2"
	 });
	 $("#Temp").css(
	 {
		  "z-index":"1000",
		 "width":"400px",
		  "background-color":"rgb(158, 234, 137)",
		 "height":"275px",
		"border":"solid 6px #807b80",
		"-moz-border-radius": "5px",
		"-webkit-border-radius": "5px",
		"border-radius": "5px" ,
			"moz-box-shadow": "-10px 12px 9px #000000",
			"-webkit-box-shadow": "-10px 12px 9px #000000",
			"box-shadow": "-10px 12px 9px #000000"
	 });
	 $("#Temp").center();
	 $("#Temp_btn_ok").click(function(e) {
		 
                            callback($("#Temp_text").val());
							$("#Temp").remove();
							$("#content_prompt").remove();
		});
		$("#Temp_btn_cancel").click(function(e) {
			$("#Temp").remove();
			$("#content_prompt").remove();
			callback(false);
		});
	 
 }

function Cerrar(Path,Id)
{
	var Name= basename(Path);
	$("#"+Name+" #"+Id).remove();
	
}
function Toggle_Bowtie(Path)
{
	var Name=basename(Path);
	$("#"+Name+" #Interface").slideToggle();
	$("#"+Name+" #Console").slideToggle();
	$("#"+Name+" #TerminalBowtie").val("bowtie");
	if($("#"+Name+" #btn_toggle_bowtie").val()=="Use Interface")
	{	
		$("#"+Name+" #btn_toggle_bowtie").val("Use Console");
	}
	else
	{
		$("#"+Name+" #btn_toggle_bowtie").val("Use Interface");
	}
}
function Send_File_Automatic(File,Path)
{
		var Name=basename(Path);
		var file = File.files[0];
		//var fd = new FormData();
 		//var Form= $("#Form_Automatic").serializeArray();
 		//d.append("fileUpload",file);
   var Form = new FormData($('#Form_Automatic')[0]);
     Form.append("fileUpload",file);
     Form.append("Prueba","ABC");
  $.ajax({
	   xhr: function() {
        var xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(evt) {
            if (evt.lengthComputable) {
           var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        $("#"+Name+" #Progreso_Numero").html(percentComplete.toString() + '%');
		$("#"+Name+" #Progreso").val(evt.loaded  / evt.total);
            }
       }, false);
       return xhr;
    },
        url: 'Automatizar_Server.php',
        type: 'POST',
        data: Form,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(Obj, textStatus, jqXHR)
        {
			 alert("We will send you an email when finish the process \"from: TransciptomeGene@chacon.com\" remember check the spam.");
			$("#Form_Automatic").remove();
			//Loading(false,0,Name);
			//Administrar_Upload(Obj,Name)
			
			
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
        	console.log('ERRORS: ' + textStatus);
        }
    });
 
}
