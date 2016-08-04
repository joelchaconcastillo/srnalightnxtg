	#!/usr/bin/env perl !/usr/bin/perl
	use strict;
	use warnings;
	use Getopt::Long;
	no warnings ('uninitialized', 'substr');
	my $RutaRelativa= "";
	my $trim5p= "";
	my $trim3p= "";
	my $FileIn="";
	my $cont=0;
	my $trimSeq="";
	my $plus="";
	my $Quality="";
	my $seq="";
	my $maxLength=0;
	my $max=0;
	my %Unique;
	my %Complexivity;
	my $FileOut="";
	my $Dir="";
	my $Fasta="";
	GetOptions( "Ruta=s" => \$RutaRelativa,
				"5p=i" => \$trim5p,
				"3p=i" => \$trim3p,
				"FileIn=s" => \$FileIn,
				"FileOut=s" => \$FileOut,
				"Fasta=s" => \$Fasta,
				"Dir=s" => \$Dir);
    if(!$RutaRelativa){$RutaRelativa=".";}
    if(!$trim5p){$trim5p=0;}
    if(!$trim3p){$trim3p=0;}
    if(!$Dir){$Dir=".";}
     if(!$FileOut){$FileOut="$RutaRelativa/FILECLEAN";}
	open(INFASTAQ,"$FileIn") || die "Not able to read $FileIn : $!\n";
	open(OUTFASTAQ, ">$FileOut") || die "Not able to create File.clean : $!\n";
	open(OUTREPORT, ">$RutaRelativa/$Dir/Trimm.lane.report.unique") || die "Not able to create File.clean : 
$!\n";
	open(OUTCOMPLEXIVITY, ">$RutaRelativa/$Dir/Trimm.lane.report.complexity") || die "Not able to create 
File.clean : $!\n";
	print STDERR "\nUsing \".\" for 10K Line, and Line for Millon\n";
	while(<INFASTAQ>){
	chomp;
	$cont++;
	if(!($cont%10000))
	{
		print STDERR ".";
		if(!($cont%1000000))
	{
		print STDERR "\nLine Millon\n";
	}
	}
	my @Header;
	my $i=0;
	
	
	if($_ =~ /^(\>)/ || $_ =~ /^(\@)/ )
	{
		if($Fasta ne "")
			{
				$_=~s/@/>/;
			}
			print OUTFASTAQ "$_\n";
	$_=~s/w//;
	$_=~s/t//;
	@Header=split(/_/,$_);
	$Unique{$Header[2]}++;
	if($Header[1] > $max){$max=$Header[1];}
	if($Header[2] > $maxLength){$maxLength=$Header[2];}
			for($i=0;$i<=$max;$i+=10)
			{
				if($Header[1] >= $i && $Header[1] <= $i+9)
				{
				$Complexivity{$i}{$Header[2]}++;
				}
			}
			
	}
	chomp($seq=<INFASTAQ>);
	if($seq=~ /^[a-zA-Z]*$/)
	{
	 $trimSeq = substr($seq, $trim5p);
	 $trimSeq = substr($trimSeq, 0, -$trim3p) unless $trim3p == 0;
	 print OUTFASTAQ "$trimSeq\n";
	}
	chomp($plus=<INFASTAQ>);
	chomp($Quality=<INFASTAQ>);
	
			if($Fasta eq "")
			{
			$trimSeq = substr($Quality, $trim5p);
			 $trimSeq = substr($trimSeq, 0, -$trim3p) unless $trim3p == 0;
			print OUTFASTAQ "+\n$trimSeq\n";
			}
	
	}
	foreach my $uniq(keys %Unique)
	{
	my $val=$Unique{$uniq};
	print OUTREPORT "$uniq $val\n" ;
	}
print OUTCOMPLEXIVITY "Complexity\tLength\tFreq\n" ;
##Llenar los elementos faltantes con ceros..
my $i=0; my $j=0;
	for($i=0;$i<=$max;$i+=10)
	{
		for($j=0;$j<=$maxLength;$j++)
		{
				if(! (exists $Complexivity{$i}{$j}))
				{
				$Complexivity{"$i"}{"$j"}=0;
				}	
		}
	}
##Imprimir los elementos
	foreach my $Complex(sort keys %Complexivity)
	{
		foreach my $length(keys %{$Complexivity{$Complex}})
			{
			print OUTCOMPLEXIVITY "$length\t$Complex\t$Complexivity{$Complex}{$length}\n" ;
		}	
	}
	
		print STDERR "\nTotal\tReads $cont \n";

