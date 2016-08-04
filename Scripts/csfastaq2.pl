#!/usr/bin/perl -w


#### Begin Program
use Getopt::Std;

$usage = "USAGE:
csfasta2cs-fastq.pl -c [.csfasta] -q [.qual] | gzip > [.cs-fastq.gz]

-c : .csfasta file, with leading T's
-q : SOLiD quality file, with Phred-scaled space-delimited integers.  Same read order as in csfasta file

for detailed documentation, type perldoc $0
";

getopt('frlme');


(`perl Scripts/csfastq-to-csfastqhybrid.pl -i $opt_f -o $opt_r/FASTAQ.txt 2> $opt_r/Progress.txt`);

if($opt_e eq "33")
{
(`Rscript Scripts/BeforeFilter.R $opt_r/FASTAQ.txt $opt_r/ FastqQuality $opt_l $opt_m 2> $opt_r/Progress.txt`);
}
else
{
(`Rscript Scripts/BeforeFilter.R $opt_r/FASTAQ.txt $opt_r/ SFastqQuality $opt_l $opt_m 2> $opt_r/Progress.txt`);

}
