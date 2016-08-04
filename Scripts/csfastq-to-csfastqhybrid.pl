#! /usr/bin/perl

#########################################################################################
# El script se encarga de convertir un archivo csfastq de solid a un csfastq estilo illumina
# cambiando los errores en los llamados de base a N y agregando un valor de calidad de 0
# cuando no lo posea (en los errores de llamado de base), además agrega un valor de calidad 
# de 40 a la primera base para intentar que sea leido por R
# Adicionalmente para que R tome el archivo cambiamos los valores del código de colores a letras
# de la siguiente manera [0123] [ATGC].

use File::stat;
use Getopt::Long;
GetOptions(\%opts, 'i|files=s', 'o|files=s');

open(FILE, "$opts{i}") || die "error i can not open the infile\n"; # archivo de entrada csfastq
open(OUT, ">$opts{o}") || die "error i can not create the outfile\n"; # archivo de salida con las correcciones
$size=int(`wc -l $opts{i}`);
$count=0;
$val=1;
while(<FILE>){
	chomp $_;
	$count++;
# Guarda la información del identificador
	if($_=~ /^@/ && $val == 1){
		$id=$_;
		$val++;
		print OUT $id."\n";
	}
# Cambia el código de colores siguiendo el orden 0123 para ATGC (generando una secuencia
# similar a fasta más no es en realidad una cadena nucleotídica 
	if($_=~ /^T/ && $val == 2){
		$seq=$_;
		$seq=substr($seq,1);
		$seq=~ s/\./N/g;
		$seq=~ tr/0123/ATGC/;
		$val++;
		print OUT $seq."\n";
	}
# Guarda el signo más
	if($_=~ /\+/){
		$val++;
		print OUT $_."\n";
		next;
	}
# Agrega información de calidad a la primera letra de la secuencia de SOLiD y el valor ASCII + 33
# correspondiente a 0 a las regiones donde existían los errores de base calling
	if($val == 4){
		$qual=$_;
		$qual=~ s/\s/!/g;
		#$qual= "I".$qual;
		$val=1;
		print OUT $qual."\n";
	}
	$percent = sprintf("%.3f",($count*100)/$size);
	
#	print STDERR "To Hybrid $percent\n";
	
}
close FILE;
close OUT;