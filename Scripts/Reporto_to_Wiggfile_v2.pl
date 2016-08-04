#! /usr/bin/perl
################################################################################################
## De un archivo del tipo report-file a un archivo wigfile
################################################################################################
## la versión 2 permite trabajar con un archivo "reporte" diferente 
################################################################################################
## pequeño script que a partir de un archivo de reporte de anotación (clásico obtenido a partir 
## de los scripts que he escrito en R) del tipo:
## secuencia   chromosoma   chrstart   strand   expression-value (raw score/rpm/rpm-hits)
## genera archivo wigfile con el header
################################################################################################

use Getopt::Long;
GetOptions(\%opts, 'i|file=s', 'o|outfile=s');

open(INFILE, "$opts{i}") || die "error i could not open the infile\n";
open(OUT1, ">$opts{o}".".plus_strand") || die "error i could not create the outfile\n";
open(OUT2, ">$opts{o}".".negative_strand") || die "error i could not create the outfile\n";

## estaría cool tener una opción de visualización de grupos de secuencias (19-21 nt) y (22 nt) y (23-24 nt)

print "reading file.... ".$opts{"i"}."\n";
while(<INFILE>){
	chomp $_;
	@data=split(/\t/,$_);
	#$data[1]=~ tr/A-Z/a-z/;
## guardamos los puntos en un hash, siendo la primera llave el cromosoma
## por cada posición sumamos el valor de expresión encontrado 
## separamos la información dependiendo de si el read corresponde al strand "+" o "-"
	for($i=$data[2]; $i<=(length($data[0]) + $data[2] - 1); $i++){
		$array{$data[1]}{$i}{$data[3]} += $data[4]; 
	}
}
#####################

print "reading... done\n";
print "printing the coordinates... \n";
for $key(sort {$a<=>$b} keys %array){
	print OUT1 "variableStep chrom=".$key."\n";
	print OUT2 "variableStep chrom=".$key."\n";
	for $key2(sort {$a<=>$b} keys %{$array{$key}}){
		if(exists($array{$key}{$key2}{"+"})){
			print OUT1 $key2."\t".($array{$key}{$key2}{"+"})."\n";
			if(exists($array{$key}{$key2}{"-"})){
				print OUT2 $key2."\t".($array{$key}{$key2}{"-"} * -1)."\n";
			}
		}
		else{
			if(exists($array{$key}{$key2}{"-"})){
				print OUT2 $key2."\t".($array{$key}{$key2}{"-"} * -1)."\n";
			}
		}
	}
}	

print "done!!\n";		
