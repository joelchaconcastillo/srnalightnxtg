
function Autocompletar_Estatico(Clase,Elementos)
{
$(function() {
var array=Elementos.split(",");
		$("."+Clase).autocomplete({
        source: array	
		});


});

}
