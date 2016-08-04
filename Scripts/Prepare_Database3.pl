	#!/usr/bin/env perl !/usr/bin/perl
	use strict;
	use warnings;
	use Getopt::Long;
	use Fcntl;
	no warnings ('uninitialized', 'substr');;
	my $FileIn="";
	my $FileOut="";
	my $Ruta="";
	my $Id_Libreria="";
	my $Id_Genoma="";
	my $Sum=0;
	my $ftpm=0;
	my $rpm=0;
	my %Score_Reads;
	my %Score_Name;
	my %Element_Name_Reads;
	my %Name;
	my %Chr;
	my @SubData="";
	my @Line="";
	GetOptions("FileIn=s" => \$FileIn,
		   "FileOut=s" => \$FileOut,
		   "Ruta=s" => \$Ruta,
		   "Id_Libreria=s" => \$Id_Libreria,
		   "Id_Genoma=s" => \$Id_Genoma);
	open(IN,"$FileIn") || die "Not able to read $FileIn : $!\n";
	 open(IN2,"$FileIn") || die "Not able to read $FileIn : $!\n";
	open(OUT, ">$FileOut") || die "Not able to create $FileOut : $!\n";
	#open(REPORT, ">$Report") || die "Not able to create $Report\n : $!\n";
	##Revisar el Score de las lecturas y de los nombres (Una tabla de frecuencias)
	while(<IN>)
	{
		chomp;
		 @Line=split(/\t/,$_);
		 @SubData=split(/SEQ/,$Line[3]);
	    #Se almacena la frecuencia de cada read en el hash Score_Reads
	        $Score_Reads{$SubData[1]}++;
	 	$Score_Name{$SubData[0]}++; ##hit	
		$Element_Name_Reads{$Line[3]}=0;
	}
       foreach my $Element(keys %Element_Name_Reads)
	{	
		@SubData=split(/SEQ/,$Element);
		$Sum+=$Score_Reads{$SubData[1]};
	}
      $ftpm=1000000/$Sum;
	##Ordenar y escribir los datos para almacenarse en la base de datos
	while(<IN2>)
	{
		chomp;
		 @Line=split(/\t/,$_);
		 @SubData=split(/ /,$Line[3]);
		print OUT "null\t$Line[0]\t$Line[1]\t$Line[2]\t$Line[5]\t$Line[4]\t$SubData[1]\tLOCI\t$Score_Reads{$SubData[1]}\t$Line[8]\t$Line[14]\t$Id_Genoma\t$Id_Libreria\n";
		##Dividir los datos por cromosomas
		push @{$Chr{$Line[0]}{"Seq"}},$SubData[1];
		push @{$Chr{$Line[0]}{"Start"}},$Line[1];
		push @{$Chr{$Line[0]}{"Strand"}},$Line[5];
		push @{$Chr{$Line[0]}{"Score"}},$Score_Reads{$SubData[1]};
	}
	
	
print STDERR "Normalization value: $ftpm";
	foreach my $Elemento(sort keys  %Chr)
	{
	open(Wiggle,">$Ruta/$Elemento.prewigglefile") || die "No able to create WiggelFile";
		for(my $i; $i< $#{$Chr{$Elemento}{"Seq"}};$i++ )
		{
	print Wiggle $Chr{$Elemento}{"Seq"}[$i]."\t$Elemento\t".$Chr{$Elemento}{"Start"}[$i]."\t".$Chr{$Elemento}{"Strand"}[$i]."\t".($Chr{$Elemento}{"Score"}[$i]*$ftpm)."\n";
		}
	close(Wiggle);
	}
	

