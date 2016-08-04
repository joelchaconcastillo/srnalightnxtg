#! /usr/bin/perl

############################################################################
##
# Toma como referencia el archivo de anotación GFF y filtra todos los elementos anotados
# en base a las coordenadas identifica las zonas no anotadas que denominamos "intergénicas"
# imprime los diferentes conjuntos de regiones intergénicas y anotadas en formato "biomart"


#gene_id = "identificador Arabidopsis" 
#gene_biotype = "gene|intergenic|transposable_element_gene|transposable_element|pseudogene"
#gene_subtype = "protein_coding|miRNA|rRNA|tRNA|snRNA|snoRNA|protein|other_RNA"

# acepta como argumentos el -g|gff archivo GFF de inicio y -o|outfile archivo de salida "biomart"
use Getopt::Long;
%opts=();
GetOptions(\%opts, 'g|gff=s', 'o|outfile=s');
open(GFF, "$opts{g}") || die "error i can not open the GFF file\n";
open(OUT, ">$opts{o}") || die "error i can not create the outfile ".$opts{'o'}."\n";

while(<GFF>){ 
 chomp $_;
 @data=split(/\t/,$_);
 next if($data[0] eq 'ChrC' || $data[0] eq 'ChrM');  # no nos interesan las secuencias de la mitocondria o del cloroplasto
 if($data[2] eq "gene"){
  @data2=split(/\;/, $data[8]);
  $data2[0] =~ s/ID=//;
  $data2[1] =~ s/Note=//;
  $info{$data[0]}{$data[3]}{$data[4]}=$data2[0]."\t".$data[6]."\t".$data[2]."\t".$data2[1];
 }
 elsif($data[2] eq "transposable_element"){
  @data2=split(/\;/, $data[8]);
  $data2[0]=~ s/ID=//;
  $data2[1]=~ s/Name=//;
  $data2[2]=~ s/Alias=//;
  $info{$data[0]}{$data[3]}{$data[4]}=$data2[0]."\t".$data[6]."\ttransposable_element\ttransposable_element";
 }
 elsif($data[2] eq "transposable_element_gene"){
  @data2=split(/\;/, $data[8]);
  $data2[0]=~ s/ID=//;
  $data2[1]=~ s/Note=//;
  $data2[2]=~ s/Name=//;
  $data2[3]=~ s/Derives_from=//;
  $info{$data[0]}{$data[3]}{$data[4]}=$data2[0]."\t".$data[6]."\t".$data[2]."\t".$data2[1];
 }
 elsif($data[2] eq "pseudogene"){
  @data2=split(/\;/, $data[8]);
  $data2[0]=~ s/ID=//;
  $data2[1]=~ s/Note=//;
  $data2[2]=~ s/Name=//;
  $info{$data[0]}{$data[3]}{$data[4]}=$data2[0]."\t".$data[6]."\t".$data[2]."\t".$data2[1];
 }
 elsif($data[2] eq "chromosome"){
 	$chromosome{$data[0]}=$data[4];
 }
}

# en base a las coordenadas definimos las regiones "vacías" como regiones intergéncias 
# con un identificador arbitrario "AT[d]I[d0x5]", al estilo del identificador de arabidopsis
$chr=0;
$before=0;
for $key1(sort {$a <=> $b} keys %info){
	if($chr ne $key1){
		$chr=$key1;
		$before=0;
	}
	$cont=1;
	$id=$key1;
	$id=~ s/Chr//;
	for $key2(sort {$a <=> $b} keys %{$info{$key1}}){
		$free=$key2-$before-1;
		$flag=0;
		if($free < 0){
			$flag=1;
		}
		if($free > 0){
			$info{$key1}{$before+1}{$key2-1}="AT".$id."I".(sprintf('%05s', $cont))."\t*\tintergenic\tintergenic";
			$cont++;
		}
		for $key3(sort keys %{$info{$key1}{$key2}}){
			if($flag){
				if($key3 > $before){
					$before=$key3;
				} else{
					$before=$before;
				}
			} else{
				$before=$key3;
			}
		}
	}
	$free=$chromosome{$chr}-$before;
	if($free > 0){
		$info{$key1}{$before+1}{$chromosome{$chr}}="AT".$id."I".(sprintf('%05s', $cont))."\t*\tintergenic\tintergenic";
	}	
}

# imprimimos el archivo
for $key1(sort {$a <=> $b} keys %info){
	for $key2(sort {$a <=> $b} keys %{$info{$key1}}){
		for $key3(sort keys %{$info{$key1}{$key2}}){
			print OUT $key1."\t".$key2."\t".$key3."\t".$info{$key1}{$key2}{$key3}."\n";
		}
	}
}

close(OUT);
close(GFF);
