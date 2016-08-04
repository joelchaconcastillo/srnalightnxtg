#! /usr/bin/perl


use DBI;
use Getopt::Long;
GetOptions(\%opts, 'g|gff=s', 'r|relative=s', 'o|output=s','i|id=s');
open(GFF, "$opts{g}") || die "error i can not open the GFF file\n";
open(OUT, ">$opts{o}") || die "error i can not create the outfile\n";

############################################################################################

## abrimos el archivo con la anotación del genoma
while(<GFF>){
	chomp $_;
	@data=split(/\t/,$_);

## solo nos interesa la información presente en el genoma nuclear
	next if($data[0] eq "ChrM" || $data[0] eq "ChrC");	
	next if($data[2] =~  m/pseudogenic_exon/);
	if($data[2] =~ m/chromosome/){ ## guardamos la información posicional de cada cromosoma
		$chr{$data[0]}{"start"}=$data[3];
		$chr{$data[0]}{"end"}=$data[4];
	}
	elsif($data[2] =~ m/(gene|pseudogene|transposable_element|transposable_element_gene)/){
## guardamos la información de coordenadas de todos los elementos para calcular los valores de las regiones intergénicas		
		$element{$data[0]}{$data[3]}=$data[4];   
	}
	elsif($data[2] =~ m/mRNA/){
	## si llegamos aquí es porque tenemos a un elemento en gen que posee relación con este mensajero por medio de parent
		$data[8] =~ /(Parent=\w+)/;
		$Parent = $1;
		$Parent =~ s/Parent=//;
		$data[8] =~ /(ID=\w+\.\d+)/;
		$ID=$1;
		$ID =~ s/ID=//;
		$data[8] =~ /(Name=\w+\.\d+)/;
		$Name=$1;
		$Name =~ s/Name=//;
		$mRNA{$ID}{"start"}=$data[3];
		$mRNA{$ID}{"end"}=$data[4];
		$mRNA{$ID}{"note"}=$Note;
		$mRNA{$ID}{"name"}=$Name;
		$mRNA{$ID}{"parent"}=$Parent;
		$mRNA{$ID}{"strand"}=$data[6];
	}
	
	## "identificar" las regiones sobrelapantes.. ?? e identificar los huecos para el cálculo de los intrones
	elsif($data[2] =~ m/^exon$|intron|(five_prime_UTR)|(three_prime_UTR)/){
		$data[8] =~ /(Parent=\w+\.\d+)/;
		$Parent = $1;
		$Parent =~ s/Parent=//;
		if(exists($subelements{$Parent}{$data[3]})){
			$subelements{$Parent}{$data[3]} = $data[4] if($data[4] > $subelements{$Parent}{$data[3]});
		} 
		$subelements{$Parent}{$data[3]}=$data[4];
	}
}	
close(GFF);

## identificamos las regiones sobrelapantes dentro de cada gen
for $key(sort {$a<=>$b} keys %subelements){
	$start = 0;
	$end = 0;
	for $keya(sort {$a<=>$b} %{$subelements{$key}}){
		$flag = 0;
		if(!$start){
			$start = $keya;
			$end = $subelements{$key}{$keya};
		} else{
			if(($start <= $keya && $keya <= $end) && $subelements{$key}{$keya} > $end){
				$end = $subelements{$key}{$keya};
			}
			elsif($keya < $start && ($subelements{$key}{$keya} >= $start && $subelements{$key}{$keya} <= $end)){
				$start = $keya;
			}
			elsif($keya >= $start && $subelements{$key}{$keya} <= $end){
				$start = $start;
			}
			else{
				$overlap{$key}{($start)}=($end);
				$start=$keya;
				$end=$subelements{$key}{$keya};
				$flag=1;
			}
		}
	}
	if(!$flag){
		$overlap{$key}{$start}=$end;
	}
}

for $key(sort{$a<=>$b} keys %mRNA){
	$start = $mRNA{$key}{"start"};
	$cend = $mRNA{$key}{"end"};
	for $keya(sort{$a<=>$b} keys %{$overlap{$key}}){
		$end = $start + ($keya - $start) - 1;
		if($end >= $start && $start != $keya){
			$subdomains{$key}{$start}=$end."|".$mRNA{$key}{"strand"};
		}
		$start = $overlap{$key}{$keya} + 1;
	}
	$end = $start + ($cend - $start) - 1;
	if($end >= $start){
		$subdomains{$key}{$start}=($end - 1)."|".$mRNA{$key}{"strand"};
	}
}

%overlpa=();
%subelements=();

## definimos y agrupamos todas las regiones sobrelapantes de los elementos para calcular los espacios de las regiones intergénicas
for $key(sort {$a<=>$b} keys %element){
	$start=0;
	$end=0;
	for $keya(sort {$a<=>$b} keys %{$element{$key}}){
		$flag=0;
		if(!$start){
			$start=$keya - 1;
			$end=$element{$key}{$keya} + 1;
		} else{
			if(($start <= $keya && $keya <= $end) && $element{$key}{$keya} > $end){
				$end=$element{$key}{$keya} + 1;
			}
			elsif($keya < $start && ($keya >= $start && $keya <= $end)){
				$start=$keya - 1;
			}
			elsif($keya >= $start && $element{$key}{$keya} <= $end){
				$start=$start;
			}
			else{
				$overlap{$key}{($start + 1)}=($end - 1);
				$start=$keya - 1;
				$end=$element{$key}{$keya} + 1;
				$flag=1;
			}
		}
	}
	if(!$flag){
		$overlap{$key}{$start}=$end;
	}
}

## ahora es momento de generar los elementos que se encuentren en regiones intergénicas
for $key(sort{$a<=>$b} keys %chr){
	$start = $chr{$key}{"start"};
	$cend = $chr{$key}{"end"};
	for $keya(sort{$a<=>$b} keys %{$overlap{$key}}){
		if($start == 1){	
			$end = $start + ($keya - $start) - 1;
			if($end >= $start){
				$intergenic{$key}{$start}=$end;
			}
			$start = $overlap{$key}{$keya} + 1;
		}
		else{
			$end = $start + ($keya - $start) - 1;
			if($end >= $start){
				$intergenic{$key}{$start}=$end;
			}
			$start = $overlap{$key}{$keya} + 1;
		}
	}
	$end = $start + ($cend - $start);
	if($end >= $start){
		$intergenic{$key}{$start}=$end;
	}
}

%overlap=();
%element=();

for $key(sort {$a<=>$b} keys %intergenic){
	$cont = 1;
	for $keya(sort {$a <=> $b} keys %{$intergenic{$key}}){
		$Chr = $key;
		$Chr =~ s/Chr//;
		$name = "AT".$Chr."I".(sprintf('%05s', $cont));
		$cont++;
		print OUT $key."\tintergenic_region\t".$keya."\t".$intergenic{$key}{$keya}."\t*\t".$name."\t".$name."\n"; ## falta agregar el strand del intron
	}
}

for $key(sort {$a<=>$b} keys %subdomains){
	$cont = 1;
	for $keya(sort {$a <=>$b} keys %{$subdomains{$key}}){
		$name = $key.".I".$cont;
		$cont++;
		$key =~ /AT(\d+)G(\d+)\.\d+/;
		$Chr = "Chr".$1;
		$subdomains{$key}{$keya} =~ /(\d+)\|(\+|\-)/;
		print OUT $Chr."\tintron\t".$keya."\t".$1."\t".$2."\t".$name."\t".$key."\n";
	}
}






open(GFF, "$opts{g}") || die "error i can not open the GFF file\n";
while(<GFF>){
	chomp $_;
	@data=split(/\t/,$_);

## solo nos interesa la información presente en el genoma nuclear
	next if($data[0] eq "ChrM" || $data[0] eq "ChrC");	
	next if($data[2] =~ m/chromosome/);
	next if($data[2] =~ m/pseudogenic_exon/);
	
	if($data[2] =~ m/transposable_element/){
		$data[8] =~ /(ID=\w+)/;
		$ID=$1;
		$ID =~ s/ID=//;
		$data[8] =~ /(Name=\w+)/;
		$Name=$1;
		$Name =~ s/Name=//;
		$data[8] =~ /(Alias=\w+)/;		
		$Alias=$1;
		$Alias =~ s/Alias=//;
		print OUT $data[0]."\t".$data[2]."\t".$data[3]."\t".$data[4]."\t".$data[6]."\t".$ID."\t".$Alias."\n";
	}

	elsif($data[2] =~ m/(gene|pseudogene|transposable_element_gene)/){
		## estar seguros que no reconoce a "transposable_element_gene"	
		$data[8] =~ /(ID=\w+)/;     #nos permite parsear la información del elemento gen
		$ID=$1;
		$ID =~ s/ID=//;
	####
		if($data[8] =~ m/Note/){
			$data[8] =~ /(Note=\w+)/;
			$Note=$1;
			$Note =~ s/Note=//;
		}
		elsif($data[8] =~ m/biotype/){	
			$data[8] =~ /(biotype=\w+)/;
			$Note=$1;
			$Note =~ s/biotype=//;
		}
	####
		$data[8] =~ /(Name=\w+)/;
		$Name=$1;
		$Name =~ s/Name=//;
		print OUT $data[0]."\t".$Note."\t".$data[3]."\t".$data[4]."\t".$data[6]."\t".$ID."\t".$Name."\n";
	}
	
	elsif($data[2] =~ m/mRNA/){
		$data[8] =~ /(Parent=\w+)/;
		$Parent = $1;
		$Parent =~ s/Parent=//;
		$data[8] =~ /(ID=\w+\.\d+)/;
		$ID=$1;
		$ID =~ s/ID=//;
		$data[8] =~ /(Name=\w+\.\d+)/;
		$Name=$1;
		$Name =~ s/Name=//;
		$cont = 1;
		print OUT $data[0]."\t".$data[2]."\t".$data[3]."\t".$data[4]."\t".$data[6]."\t".$ID."\t".$Parent."\n";
	}
	
	if($data[2] =~ m/exon|intron|(five_prime_UTR)|(three_prime_UTR)/){
		$data[8] =~ /(Parent=\w+\.\d+)/;
		$Parent = $1;
		$Parent =~ s/Parent=//;
		$Name = $Parent.".E".$cont;
		$cont ++;
		print OUT $data[0]."\t".$data[2]."\t".$data[3]."\t".$data[4]."\t".$data[6]."\t".$Name."\t".$Parent."\n";
	}
}

close(OUT);
close(GFF);
system("Rscript Scripts/order.R $opts{o}");

########### conexión con mysql
 open(GFF, "$opts{o}") || die "error i couldn't open the infile\n";
 open(OUT1, ">$opts{r}"."annotation") || die "error i can not create the annotation file\n";
 open(OUT2, ">$opts{r}"."subtype") || die "error i can not create the subannotation file\n";
 open(OUT3, ">$opts{r}"."subdomain") || die "error i can not create the subtype file\n";
 open(OUT4, ">$opts{r}"."no_match") || die "error i can not create the annotation file\n";

# print "user name?\n";
# $usr=<STDIN>;
# chomp $usr;
# print "password:\n";
# $passwd=<STDIN>;
# chomp $passwd;
# $conexion = DBI->connect("dbi:mysql:<nombre-base-de-datos>:localhost",$usr,$passwd) || die "Database connection not made: $DBI::errstr";
  print STDERR "$opts{r}";

while(<GFF>){
	chomp $_;
	@data=split(/\t/,$_);
	
	if($data[1]=~ m/(protein_coding_gene|intergenic_region|pseudogene|gene|intergenic|transposable_element_gene|transposable_element)/){#Anotación biotype
		#print OUT1 $_."\n";
		print OUT1 $data[5]."\t".$data[0]."\t".$data[1]."\t".$data[2]."\t".$data[3]."\t".$data[4]."\t".$data[6]."\t".$opts{i}."\n";

#		$r1 = $conexion->do("INSERT INTO subdomain VALUES ($_)");
	}
    elsif($data[1]=~ m/(mRNA|protein_coding|miRNA|rRNA|tRNA|snRNA|snoRNA|protein|other_RNA)/){#Subdominio subtype
		#print OUT2 $_."\n";
		
		if($data[5]=~/(\w+\.\d+)/)
		{
		print OUT2 $data[5]."\t".$data[0]."\t".$data[1]."\t".$data[2]."\t".$data[3]."\t".$data[4]."\t".$data[6]."\n";
		}
#		$r1 = $conexion->do("INSERT INTO subdomain VALUES ($_)");
	}
	elsif($data[1]=~ m/(pseudogenic_exon|intron|exon|three_prime_UTR|five_prime_UTR)/){
		#print OUT3 $_."\n";#restantes_subtipos
		print OUT3 $data[5]."\t".$data[0]."\t".$data[1]."\t".$data[2]."\t".$data[3]."\t".$data[4]."\t".$data[6]."\n";

		#		$r1 = $conexion->do("INSERT INTO subdomain VALUES ($_)");#
	}
	else{ 	
	print OUT4 $data[5]."\t".$data[0]."\t".$data[1]."\t".$data[2]."\t".$data[3]."\t".$data[4]."\t".$data[6]."\n";
#print OUT4 $_."\n";#sobrantes no deben existir....
	}
	
}

#$conexion1->disconnect() || warn "Error while disconecting";
close(GFF);
close(OUT1);
close(OUT2);
close(OUT3);
close(OUT4);
