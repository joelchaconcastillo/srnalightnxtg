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
	my %Score_Reads;
	my @Line="";
	GetOptions("FileIn=s" => \$FileIn,
		   "FileOut=s" => \$FileOut,
		   "Report=s" => \$Report,
		   "Id_Libreria=s" => \$Id_Libreria,
		   "Version" => \$Version);
	open(IN,"$FileIn") || die "Not able to read $FileIn : $!\n";
	open(OUT, ">$FileOut") || die "Not able to create $FileOut : $!\n";
	open(REPORT, "$Report") || die "Not able to create $Report\n : $!\n";
	while(<REPORT>)
	{
		chomp;
		$_=~ s/ +/ /g;	
	  	@Line= split(/ /,$_);
		$Score_Reads{$Line[2]}=$Line[1];
	}
	while(<IN>)
	{
		chomp;
		 @Line=split(/\t/,$_);
		print OUT "null\t$Line[0]\t$Line[1]\t$Line[2]\t$Line[5]\t$Line[4]\t$Line[3]\tLOCI\t$Score_Reads{$Line[3]}\t$Line[11]\t$Line[12]\t$Id_Libreria\t$Line[9]\t$Version\n";

	}

