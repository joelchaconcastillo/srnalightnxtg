#! /usr/bin/perl

use Getopt::Long;
GetOptions(\%opts, 'i|files=s', 'o|files=s', 't|files=s', 'e|encode=s', 'l|linker=s', 'm|match=s');

open(FILE, "$opts{i}") || die "error i can not open the infile\n"; # archivo de entrada 
open(OUT, ">$opts{o}/FASTAQ.txt") || die "error i can not create the outfile\n"; # archivo de salida
 $cont=0;
 $size=int(`wc -l $opts{i}`);
$count=0;
while(<FILE>){
	chomp $_;
	$count++;
	
# Nos guarda sin modificar el identificador de la secuencia
if($opts{t} eq "T")
{
	if($_=~ /^[acgtACTGuU]+(\t)(\d)+/ )
	{
	$String=$_;
	$String=~ s/u/a/ig;
	$String=~ s/U/A/ig;
		my @Column = split(/\t/, $String);
		print OUT ">".$cont."|".$Column[1]."\n".$Column[0]."\n";
		$cont++;
	}
	elsif( $_=~ /^(.)+(\t)[ATGCUactgu]+(\t)(\d)+/)
	{
	$String=$_;
	$String=~ s/u/a/ig;
	$String=~ s/U/A/ig;
	    my @Column = split(/\t/, $String);
		print OUT ">".$Column[0]."|".$Column[2]."\n".$Column[1]."\n";
	}
}

if($opts{t} eq "F")
{
   if($_=~/^>/)
   {
    $String=$_;
   $String=~ s/>/@/ig;
   $String=~ s/u/a/ig;
	$String=~ s/U/A/ig;
	print OUT $String."\n";
   }
	elsif($_=~ /^[atgcATGCuU]+/ )
	{
	
	   $String=$_;
		$Char="";
		foreach($i=0;$i<length($String);$i++)
		{$Char.=chr(40+$opts{e});}
		
		print OUT $String."\n"."+\n".$Char."\n";
		$cont++;
	}
}
if($opts{t} eq "FQ2H")
{
   if($_=~/^@/)
   {
	   if($Header ne $_)
	   {
	   $Header=$_;
		print OUT $_."\n";
	   }
   }
	elsif($_=~ /^[atgcATGCuU]+/ )
	{
	$String=$_;
	$String=~ s/u/a/ig;
	$String=~ s/U/A/ig;
	  print OUT $String."\n";
	}
	elsif($_=~ /(\+)(.)*/)
	{
	  print OUT $_."\n";
	}
	elsif($_=~ /^(.)*/)
	{
	  print OUT $_."\n";
	}
}
############################Reporte de avance
	$percent = sprintf("%.3f",($count*100)/$size);
	
	print STDERR "$percent\n";
}
#if($opts{t} ne "T")
#{
#	if($opts{e} eq "33")
#	{
#	(`Rscript Scripts/BeforeFilter.R $opts{o}/FASTAQ.txt $opts{o}/ FastqQuality $opts{l} $opts{m} 2> $opts{o}/Progress.txt`);
#	}
#	else
#	{
#	(`Rscript Scripts/BeforeFilter.R $opts{o}/FASTAQ.txt $opts{o}/ SFastqQuality $opts{l} $opts{m} 2> $opts{o}/Progress.txt`);
#	}
#}


		
