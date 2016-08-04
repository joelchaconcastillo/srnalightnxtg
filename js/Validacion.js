
//Validación de los formularios
function Validar_Formulario(Formulario)
{
	$("#"+Formulario).validate({
		 showErrors: function(errorMap, errorList) 
				{
					for (var i = 0; errorList[i]; i++) {
											var element = this.errorList[i].element;
											//solves the problem with brute force
											//remove existing error label and thus force plugin to recreate it
											//recreation == call to errorplacement function
											this.errorsFor(element).remove();
										}
										
						$.each(this.successList, function(index, value) 
						{
							return $(value).popover("hide");
						});
						
						return $.each(errorList, function(index, value) 
							{
								var _popover;
								console.log(value.message);
								_popover = $(value.element).popover(
												{
													trigger: "manual",
													placement: $(value.element).attr('placement'),
													content: value.message,
													template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\"><div class=\"popover-content\"><p></p></div></div></div>"
												});
												$(value.element).attr('data-content',value.message);
								return $(value.element).popover("show");
							});
				}
		});
}
$(document).ready(function(e) {
$.validator.addMethod('pattern', function(value, element, param) {
        return this.optional(element) || value.match(param);
    },
    'This value doesn\'t match the acceptable pattern.');
/////////////Script kraken.php////////////////
//Formulario en el que se suben los archivos//
////////////////////////////////////////////
$("#Subir_Archivos").validate({
	rules:{
		 Patron:
		 {		
			minlength:2,
			
		 },
		 URL:
		 {		
			required: true
		 }
	},
	 messages: {
            URL: {
                required: "If you to want indicate url this field is required"
            }
        },
        onfocusout: function(element){
			//Es necesario volver a crear el ballon para evitar 
			//el error de borrar un ballon que no existe
			$(element).showBalloon({
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 250
						
					});
			$(element).hideBalloon();
			
			
			
			},
	errorPlacement: function(error, element) {
		
					$(element).showBalloon({
						contents:error.text(),
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 250
						
					});
			if(!error.text())
			 $(element).hideBalloon();	
	},
	success: function(element) {
		
    }
}
);
/////////////////////////////////////////////////////////////
/////Formulario en el que se realiza el corte del adaptador//
////////////////////////////////////////////////////////////
$("#Adaptador").validate({
	rules:{
		Geometria:
		 {
			required: true
		 },
		 Adaptador_3:
		 {		
			pattern: "^[acgtnACGTN]+$"
		 },
		 Barcode:
		 {		
			pattern: "^[acgtnACGTN]+$"
		 },
		 Tabu:
		 {		
			pattern: "^[acgtnACGTN]+$"
		 },
		 Insert_Adaptador_3:
		 {		
			pattern: "^[acgtnACGTN]+$"
		 },
		 Insert_Adaptador_5:
		 {		
			pattern: "^[acgtnACGTN]+$"
		 },
		 T_3p_global:
		 {
			pattern: "^([0-9]+)(/[0-9]+)+$" 
		 },
		 T_3p_prefix:
		 {
			pattern: "^([0-9]+)(/[0-9]+)+$" 
		 },
		 T_3p_barcode:
		 {
			pattern: "^([0-9]+)(/[0-9]+)+$" 
		 },
		 T_5p_barcode:
		 {
			pattern: "^([0-9]+)(/[0-9]+)+$" 
		 },
		 T_5p_sinsert:
		 {
			pattern: "^([0-9]+)(/[0-9]+)+$" 
		 },
		 Mr_tabu:
		 {
			pattern: "^[acgtnACGTN]+$"
		 },
		  T_3p_head_to_tail:
		 {
			pattern: "^[0-9]+$"
		 },
		  qqq_check:
		 {
			pattern: "^([0-9]+)(/[0-9]+)+$"
		 },
		  nnn_check:
		 {
			pattern: "^([0-9]+)/([0-9]+)$"
		 },
		  dust_suffix:
		 {
			pattern: "^[0-9]+$"
		 },
		  dust_suffix_late:
		 {
			pattern: "^[0-9]+$"
		 },
		  clean_length:
		 {
			pattern: "^[0-9]+$"
		 },
		  tri:
		 {
			pattern: "^[0-9]+$"
		 },
		 tri_length:
		 {
			pattern: "^[0-9]+$"
		 },
		  polya:
		 {
			pattern: "^[0-9]+$"
		 },
		  sc_max:
		 {
			pattern: "^[0-9]+\.[0-9]+$"
		 }
		 
	},
	 messages: {
			Geometria:
		 {
			required: "Please select the geometry<< left"
		 },
		 Adaptador_3:
		 {		
			pattern: "If you to want indicate url this field is required<< left"
		 },
		 Barcode:
		 {		
			pattern: "It only accept the characters A C G T N.<< left"
		 },
		 Tabu:
		 {		
			pattern: "It only accept the characters A C G T N.<< left"
		 },
		 Insert_Adaptador_3:
		 {		
			pattern: "It only accept the characters A C G T N.<< left"
		 },
		 Insert_Adaptador_5:
		 {		
			pattern: "It only accept the characters A C G T N.<< left"
		 },
		 T_3p_global:
		 {
			pattern: "Please insert the correct values based l/e/g/oSL(Check (?) \"Aligment Tests\") SLexamples:SL 14/2 OR 8/2/0 OR 8/2/0/2 << right" 
		 },
		  T_3p_prefix:
		 {
			pattern: "Please insert the correct values based l/e/g/oSL(Check (?) \"Aligment Tests\") SLexamples:SL 14/2 OR 8/2/0 OR 8/2/0/2 << right" 
		 },
		 T_3p_barcode:
		 {
			pattern: "Please insert the correct values based l/e/g/oSL(Check (?) \"Aligment Tests\") SLexamples:SL 14/2 OR 8/2/0 OR 8/2/0/2 << right" 
		 },
		 T_5p_barcode:
		 {
			pattern: "Please insert the correct values based l/e/g/oSL(Check (?) \"Aligment Tests\") SLexamples:SL 14/2 OR 8/2/0 OR 8/2/0/2 << right"  
		 },
		 T_5p_sinsert:
		 {
			pattern: "Please insert the correct values based l/e/g/oSL(Check (?) \"Aligment Tests\") SLexamples:SL 14/2 OR 8/2/0 OR 8/2/0/2 << right"
		 },
		 Mr_tabu:
		 {
			pattern: "Please insert the correct values based l/e/g/oSL(Check (?) \"Aligment Tests\") SLexamples:SL 14/2 OR 8/2/0 OR 8/2/0/2 << right"
		 },
		  T_3p_head_to_tail:
		 {
			pattern: "Only numbers<<right"
		 },
		  qqq_check:
		 {
			pattern: "Please insert the correct values based <val>/<winlen>/<winofs>/<readofs>SL(Check (?)) SLexamples:SL 14/2 OR 8/2/0 OR 8/2/0/2 << right"
		 },
		  nnn_check:
		 {
			pattern: "Please insert the correct values based <count>/<outof>SL(Check (?)) SLexamples:SL 14/2 << right"
		 },
		  dust_suffix:
		 {
			pattern: "Only numbers << right"
		 },
		  dust_suffix_late:
		 {
			pattern: "Only numbers << right"
		 },
		  clean_length:
		 {
			pattern: "Only numbers << right"
		 },
		  tri:
		 {
			pattern: "Only numbers << right"
		 },
		 tri_length:
		 {
			pattern: "Only numbers << right"
		 },
		  polya:
		 {
			pattern: "Only numbers << right"
		 },
		  sc_max:
		 {
			pattern: "Only decimals << right"
		 }
		 
        },
        onfocusout: function(element){
			//Es necesario volver a crear el ballon para evitar 
			//el error de borrar un ballon que no existe
			$(element).showBalloon({
						offsetX: -1000,
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 0
						
					});
			$(element).hideBalloon();
			
			
			
			},
	errorPlacement: function(error, element) {
					var string=error.text().split("<<");
					$(element).showBalloon({
						contents: string[0].replace(/SL/g,"<br>"),
						position:string[1],
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 250
						
					});
			if(!error.text())
			{
				$(element).showBalloon({
						offsetX: -1000,
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 0
						
					});
			$(element).hideBalloon();
			}
			 
	},
	success: function(element) {
		
    }
}
);
//Formulario Filtro
$("#Form_Filtro").validate({
	rules:{
		Low:
		 {
			pattern: "^[0-9]+$"
		 },
		 Min_Size:
		 {
			pattern: "^[0-9]+$"
		 },
		 Max_Size:
		 {
			pattern: "^[0-9]+$"
		 },
		 Five:
		 {
			pattern: "^[0-9]+$"
		 },
		 Three:
		 {
			pattern: "^[0-9]+$"
		 }
	},
	 messages: {
			Low:
		 {
			pattern: "Only numbers"
		 },
		 Min_Size:
		 {
			pattern: "Only numbers"
		 },
		 Max_Size:
		 {
			pattern: "Only numbers"
		 },
		 Five:
		 {
			pattern: "Only numbers"
		 },
		 Three:
		 {
			pattern: "Only numbers"
		 }
		 
        },
        onfocusout: function(element){
			//Es necesario volver a crear el ballon para evitar 
			//el error de borrar un ballon que no existe
			$(element).showBalloon({
						offsetX: -1000,
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 0
						
					});
			$(element).hideBalloon();
			
			
			
			},
	errorPlacement: function(error, element) {
					var string=error.text().split("<<");
					$(element).showBalloon({
						contents: string[0].replace(/SL/g,"<br>"),
						position:string[1],
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 250
						
					});
			if(!error.text())
			{
				$(element).showBalloon({
						offsetX: -1000,
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 0
						
					});
			$(element).hideBalloon();
			}
			 
	},
	success: function(element) {
		
    }
}
);
//Formulario Bowtie
$("#Form_Bowtie").validate({
	rules:{
		Select_Genoma:
		 {
			required: true
		 },
		 Alineacion_n:
		 {
			pattern:"^[0-9]+$" 
		 },
		 Opcion_l:
		 {
			pattern:"^[0-9]+$" 
		 },
		 Opcion_e:
		 {
			pattern:"^[0-9]+$" 
		 },
		 Opcion_k:
		 {
			pattern:"^[0-9]+$" 
		 },
		 Opcion_m:
		 {
			pattern:"^[0-9]+$" 
		 },
		 Opcion_M:
		 {
			pattern:"^[0-9]+$" 
		 },
		 TerminalBowtie:
		 {
			pattern:"$bowtie.*(!\\)" 
		 }
		 
	},
	 messages: {
			Select_Genoma:
		 {
			required: "Please select a genome"
		 },
		 Alineacion_n:
		 {
			pattern:"Only numbers" 
		 },
		 Opcion_l:
		 {
			pattern:"Only numbers"  
		 },
		 Opcion_e:
		 {
			pattern:"Only numbers"  
		 },
		 Opcion_k:
		 {
			pattern:"Only numbers"  
		 },
		 Opcion_m:
		 {
			pattern:"Only numbers"  
		 },
		 Opcion_M:
		 {
			pattern:"Only numbers" 
		 },
		 TerminalBowtie:
		 {
			pattern:"The string contain incorrect elements" 
		 }
		 
        },
        onfocusout: function(element){
			//Es necesario volver a crear el ballon para evitar 
			//el error de borrar un ballon que no existe
			$(element).showBalloon({
						offsetX: -1000,
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 0
						
					});
			$(element).hideBalloon();
			
			
			
			},
	errorPlacement: function(error, element) {
					var string=error.text().split("<<");
					$(element).showBalloon({
						contents: string[0].replace(/SL/g,"<br>"),
						position:string[1],
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 250
						
					});
			if(!error.text())
			{
				$(element).showBalloon({
						offsetX: -1000,
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 0
						
					});
			$(element).hideBalloon();
			}
			 
	},
	success: function(element) {
		
    }
}
);

///////////////////////////
/////GRUPOS DE TRABAJO/////
///////////////////////////
//Formulario Bowtie





});


function Balloon_Focusout(element)
{
	//Es necesario volver a crear el ballon para evitar 
			//el error de borrar un ballon que no existe
			$(element).showBalloon({
						offsetX: -1000,
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 0
						
					});
			$(element).hideBalloon();
			
			
			
}
function Balloon_error(error,element)
{
var string=error.text().split("<<");
					$(element).showBalloon({
						contents: string[0].replace(/SL/g,"<br>"),
						position:string[1],
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 250
						
					});
			if(!error.text())
			{
				$(element).showBalloon({
						offsetX: -1000,
						contents:"",
						position:"left",
						css:{
						backgroundColor:"black",
						border: "solid 1px black",
						color:"white"
						},
						showDuration: 0
						
					});
			$(element).hideBalloon();
			}	
}



function Drop_Baloons()
{
	   $( "div:contains(:contains('This field is required')" ).hide();
	   $( "div:contains(:contains('Please insert a name')" ).hide();
	   $( "div:contains(:contains('Please select a genome')" ).hide();
	   $( "div:contains(:contains('Only numbers')" ).hide();
	   $( "div:contains(:contains('Please enter at least 2 characters')" ).hide();
	   $( "div:contains(:contains('If you to want indicate url this field is required')" ).hide();
	  
	   
	 
}
