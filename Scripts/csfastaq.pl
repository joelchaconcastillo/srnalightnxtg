#!/usr/bin/perl -w

$version = "0.2";  ## March 9, 2012  MJA

=head1 LICENSE

csfasta2cs-fastq.pl

Copyright (C) 2012 Michael J. Axtell

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see www.gnu.org/licenses

=head1 SYNOPSIS

Given a color-space fasta file (.csfasta file) and the corresponding SOLiD-generated quality file (with Phred-scaled values arranged in a space-delimited format), compress data to color-space-FASTQ format, using Phred+33 ASCII encoding for the Q values.

=head1 INSTALL

If needed, make script executable:

    chmod +x csfasta2cs-fastq.pl

Ensure perl is executable in your PATH at /usr/bin/ ...  if not, modify line 1 of the script accordingly

Add script csfasta2cs-fastq.pl to your PATH (examples:)

    sudo cp trim_illumina_sRNA_fastq.pl /usr/bin/

OR just for one session assuming script is in your working directory:
    
    PATH=$PATH:.

=head1 USAGE

    csfasta2cs-fastq.pl -c [.csfasta] -q [.qual] | gzip > [.cs-fastq.gz]

Run information comes to STDERR, cs-fastq goes to STDOUT

=head1 OPTIONS

All options are required

-c : the .csfasta file.  Comment lines (begin with #) are ignored.  Reads must begin with leading primer T, as expected for 'forward' reads from the SOLiD system

-q : the corresponding quality value file generated by the SOLiD system. Comment lines ignored.  Read names must match exactly and be in the same order as the .csfasta file, and the number of quality values found must equal the number of colors for the corresponding read.

=head1 NOTES

=head2 Ambiguous colors

Any reads with ambiguous colors (e.g. a "." instead of 0, 1, 2, or 3) in the trimmed portion are rejected and not output.

=head2 Format for cs-fastq

Given an input .csfasta read:

    >1_56_305_F3
    T30221330023200122103203302010303101

.. and a corresponding quality entry of:

    >1_56_305_F3
    31 4 27 23 16 18 9 22 5 10 25 7 23 4 8 24 7 21 4 7 19 8 4 19 8 8 9 6 8 12 12 13 18 4 4

.. the script will return a cs-fastq entry of:

    @1_56_305_F3
    T30221330023200122103203302010303101
    +
    @%<813*7&+:(8%)9(6%(4)%4))*')--.3%%

Note that the color-sequence appears to be one character longer than the quality string.  this is because the leading 'T' is retained.  The first quality value, in this example 31 (or @ in Phred+33 ASCII) corresponds to the first color, in this example, the '3' that follows the primer 'T'

=head1 ACKNOWLEDGEMENTS

There is a similar utility (though not identical in some details, like handling of ambiguous characters) available on the Galaxy public server : main.g2.bx.psu.edu

=head1 VERSIONS

0.1 : Internal version never released on the web.

0.2 : This version.  Added documentation. Initial release  March 9, 2012

=head1 AUTHOR

Michael J. Axtell, Penn State University, mja18@psu.edu

=cut
    
#### Begin Program
use Getopt::Std;

$usage = "USAGE:
csfasta2cs-fastq.pl -c [.csfasta] -q [.qual] | gzip > [.cs-fastq.gz]

-c : .csfasta file, with leading T's
-q : SOLiD quality file, with Phred-scaled space-delimited integers.  Same read order as in csfasta file

for detailed documentation, type perldoc $0
";

getopt('cqorlme');

unless(-r $opt_c) {
    die "Cannot open csfasta file $opt_c\n$usage\n";
}

unless(-r $opt_q) {
    die "Cannot open quality file $opt_q\n$usage\n";
}

open(CS, "$opt_c");
open(Q, "$opt_q");
open(CSFASTQ, ">$opt_o");
$cs_name_count = 0;
$output_count = 0;
$size=int(`wc -l $opt_c`);
$count=0;
# Report on session
print STDERR "$0 version $version\n";
print STDERR "directory: ";
print STDERR "input csfasta file: $opt_c\n";
print STDERR "input SOLiD quality file: $opt_q\n\n";

$qual_name = "null";  ## just to prevent complaints about uninitialized value

while (<CS>) {
    chomp;
	$count++;
    if($_ =~ /^>/) {
	++$cs_name_count;
	$cs_name = $_;
    } elsif ($_ =~ /^T[0123\.]+/) {  ## no "." or other characters except colors, demands leading T
	$cs_out = $_;
	
	# get qual
	until ($qual_name eq $cs_name) {
	    $qual_name = <Q>;
	    $qual_name =~ s/\n//g;
	}
	
	# convert qual
	$q_values = <Q>;
	$q_values =~ s/\n//g;
	@q_val = split (" ", $q_values);
	## ensure same size
	unless((scalar @q_val) == ((length $cs_out) - 1)) {  ## this one is for when the leading T is kept!
	    next;
	}
	## convert
	$q_out = '';
	foreach $q_int (@q_val) {
	    $q_out .= chr(($q_int<=93? $q_int : 93 ) + 33);  ## code from http://maq.sourceforge.net/fastq.shtml
	}
	## ensure same size after conversion of quality scores
	unless((length $q_out) == ((length $cs_out) - 1)) { ## this one for when leading T is kept!
	    next;
	}
	## print fastq and count
	$cs_name =~ s/>/@/g;
	print CSFASTQ "$cs_name\n";
	print CSFASTQ "$cs_out\n";
	print CSFASTQ "+\n";
	print CSFASTQ "$q_out\n";
	## tally
	++$output_count;
    }
	############################Reporte de avance
	$percent = sprintf("%.3f",($count*100)/$size);
	
	print STDERR "$percent\n";
}
close CS;
close Q;
close CSFASTQ;
print STDERR "Input csfasta headers: $cs_name_count\n";
print STDERR "Output color-fastq reads: $output_count\n";
(`perl Scripts/csfastq-to-csfastqhybrid.pl -i $opt_o -o $opt_r/FASTAQ.txt 2> $opt_r/Progress.txt`);

if($opt_e eq "33")
{
(`Rscript Scripts/BeforeFilter.R $opt_r/FASTAQ.txt $opt_r/ FastqQuality $opt_l $opt_m 2> $opt_r/Progress.txt`);
}
else
{
(`Rscript Scripts/BeforeFilter.R $opt_r/FASTAQ.txt $opt_r/ SFastqQuality $opt_l $opt_m 2> $opt_r/Progress.txt`);

}
