#!/usr/bin/perl -w

eval 'exec /usr/bin/perl -w -S $0 ${1+"$@"}'
    if 0; # not running under some shell

use Data::Stag qw(:all);
use Data::Stag::ITextParser;
use Data::Stag::SxprWriter;
my $p = Data::Stag::ITextParser->new;
my $h = Data::Stag::SxprWriter->new;
$p->handler($h);
foreach my $f (@ARGV) {
    $p->parse($f);
}


__END__

=head1 NAME

stag-itext2sxpr - converts between stag formats

=head1 DESCRIPTION

Converts from itext to sxpr format.

=head1 SEE ALSO

L<Data::Stag>

=cut
