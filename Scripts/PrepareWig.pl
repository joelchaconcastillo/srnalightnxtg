	#!/usr/bin/env perl !/usr/bin/perl
	use strict;
	use warnings;
	use Getopt::Long;
	no warnings ('uninitialized', 'substr');;
	my $FileIn="";
	my $FileOut="";
	my $Report="";
	my $Id_Libreria="";
	my $Version="";
	my $Sum=0;
	my $ftpm=0;
	my $rpm=0;
	my %Score_Reads;
	my %Score_Name;
	my %Chr;
	my @SubData="";
	my @Line="";
	GetOptions("FileIn=s" => \$FileIn,
		   "FileOut=s" => \$FileOut,
		   "Report=s" => \$Report,
		   "Id_Libreria=s" => \$Id_Libreria,
		   "Version" => \$Version);
	open(IN,"$FileIn") || die "Not able to read $FileIn : $!\n";
	open(OUT, ">$FileOut") || die "Not able to create $FileOut : $!\n";
	open(REPORT, "$Report") || die "Not able to create $Report\n : $!\n";
	##Revisar el Score de las lecturas y de los nombres (Una tabla de frecuencias)
	while(<IN>)
	{
		chomp;
		 @Line=split(/\t/,$_);
		 @SubData=split(/SEQ/,$Line[3]);
		$Score_Reads{$SubData[0]}++;
	 	$Score_Name{$SubData[1]}++; ##hit
	}
	##Ordenar y escribir los datos para almacenarse en la base de datos
	while(<IN>)
	{
		chomp;
		 @Line=split(/\t/,$_);
		 @SubData=split(/SEQ/,$Line[3]);
		print OUT "null\t$Line[0]\t$Line[1]\t$Line[2]\t$Line[5]\t$Line[4]\t$SubData[0]\tLOCI\t$Score_Reads{$SubData[0]}\t$Line[11]\t$Line[12]\t$Id_Libreria\t$Line[9]\t$Version\n";
		$Sum+=$Score_Reads{$SubData[0]};
		##Dividir los datos por cromosomas
		push($Chr{$Line[0]}{"Seq"},$SubData[0]);
		push($Chr{$Line[0]}{"Start"},$Line[0]);
		push($Chr{$Line[0]}{"Strand"},$Line[5]);
		push($Chr{$Line[0]}{"Score"},$Score_Reads{$SubData[0]});
		
	}
	$ftpm=1000000/$Sum;
	foreach $Elemento(keys  %Chr)
	{
		#foreach(@{$tgs{$key})
		#}
		print REPORT $Chr{$Elemento}["Seq"]."\t$Elemento".$Chr{$Elemento}["Start"]."\t".$Chr{$Elemento}["Strand"]."\t".($Chr{$Elemento}["Score"]*$Sum);
		#}
	}
	

