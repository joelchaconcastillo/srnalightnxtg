	#!/usr/bin/env perl !/usr/bin/perl
	use strict;
	use warnings;
	use Getopt::Long;
	no warnings ('uninitialized', 'substr');;
	my $FileIn="";
	my $FileOut="";
	my $Report="";
	my %Score_Reads;
	my @Line="";
	GetOptions("FileIn=s" => \$FileIn,
		   "FileOut=s" => \$FileOut,
		   "Report=s" => \$Report);
	open(IN,"$FileIn") || die "Not able to read $FileIn : $!\n";
	open(OUT, ">$FileOut") || die "Not able to create $FileOut : $!\n";
	open(REPORT, "$Report") || die "Not able to create $Report\n : $!\n";
	while(<REPORT>)
	{
		chomp;	
	  	@Line= split(/\t/,$_);
		$Score_Reads{$Line[1]}=$Line[0];
	}
	while(<IN>)
	{
		chomp;
		 @Line=split(/\t/,$_);
		print OUT "ContadorBD\t$Line[0]\t$Line[8]\t$Line[9]\t$Line[13]\t$Line[12]\t$Line[10]\tLOCI\t$Score_Reads{$Line[10]}\t$Line[5]\t$Line[6]\tID_LIBRERIA\t$Line[3]\n";

	}

