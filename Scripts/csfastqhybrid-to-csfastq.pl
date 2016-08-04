#! /usr/bin/perl

#########################################################################################
# El script se encarga de convertir un archivo csfastq hybrid (la versión que puede procesar
# R - estilo illumina) a un archivo tipo csfastq de SOLiD
# cambia los valores de "N" a "." (error in base calling) y los valores de calidad de "0 o !"
# a espacios en blanco. 
# elimina el primer caracter de la calidad colocado para poder procesar los datos de SOLiD con R
# cuando no lo posea (en los errores de llamado de base), además agrega un valor de calidad 
# cambiar los caracteres [ATGC] a partir de la posición 2 a [0123]


use Getopt::Long;
GetOptions(\%opts, 'i|files=s', 'o|files=s');

open(FILE, "$opts{i}") || die "error i can not open the infile\n"; # archivo de entrada csfastq hybrido
open(OUT, ">$opts{o}") || die "error i can not create the outfile\n"; # archivo de salida csfastq
$size=int(`wc -l $opts{i}`);
$count=0;
$val=1;
while(<FILE>){
	chomp $_;
$count++;
# Nos guarda sin modificar el identificador de la secuencia
	if($_=~ /^@/ && $val == 1){
		$id=$_;
		$val++;
		print OUT $id."\n"; 
		next;
	}
# Cambia del código de letras híbrido al código de números de SOLiD, toma especial cuidado
# con el primer nucléotido
	if( $val == 2){
		$seq=$_;
		$seq=~ s/N/\./g;
		#$seq=~ tr/^T/Z/;
		$seq=~ tr/ATGC/0123/;
		$seq="T".$seq;
		#$seq=~ tr/Z/T/;
		$val++;
		print OUT $seq."\n";
	}
# Signo más	
	if($_=~ /\+/){
		$val++;
		print OUT $_."\n";
		next;
	}
# Cambia los valores de calidad agregados para las zonas que poseían errores en el base 
# calling y recorta la calidad agregada al primer nucleótido 
	if($val == 4){
		$qual=$_;
		$qual=~ s/!/ /g;
		$qual=~ s/I//g;
		$val=1;
		print OUT $qual."\n";
	}
	$percent = sprintf("%.3f",($count*100)/$size);
	
	print STDERR "2.b $percent\n";
}
close FILE;
close OUT;
print STDERR "4 $percent\n";
		