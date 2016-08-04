package Heap::Simple::XS::Package;
# START HISTORY
# autogenerated by release_pm
use vars qw($VERSION %history);
$VERSION = "0.10";
%history = (
  'Changes' => {
    '0.01' => '0.01',
    '0.02' => '0.02',
    '0.03' => '0.03',
    '0.04' => '0.04',
    '0.05' => '0.05',
    '0.06' => '0.06',
    '0.07' => '0.07',
    '0.08' => '0.08',
    '0.09' => '0.09',
    '0.10' => '0.10'
  },
  'MANIFEST' => {
    '0.01' => '0.01',
    '0.02' => '0.04',
    '0.03' => '0.09',
    '0.04' => '0.10'
  },
  'MANIFEST.SKIP' => {
    '0.01' => '0.10'
  },
  'Makefile.PL' => {
    '0.01' => '0.01',
    '0.02' => '0.02',
    '0.03' => '0.04',
    '0.04' => '0.07',
    '0.05' => '0.10'
  },
  'README' => {
    '0.01' => '0.01',
    '0.02' => '0.07'
  },
  'XS.pm' => {
    '0.01' => '0.01',
    '0.02' => '0.02',
    '0.03' => '0.03',
    '0.04' => '0.04',
    '0.05' => '0.05',
    '0.06' => '0.06',
    '0.07' => '0.07',
    '0.08' => '0.08',
    '0.09' => '0.09'
  },
  'XS.xs' => {
    '0.01' => '0.01',
    '0.02' => '0.02',
    '0.03' => '0.03',
    '0.04' => '0.04',
    '0.05' => '0.05',
    '0.06' => '0.07',
    '0.07' => '0.08',
    '0.08' => '0.09',
    '0.09' => '0.10'
  },
  'lib/Heap/Simple/XS.pm' => {
    '0.10' => '0.10'
  },
  'lib/Heap/Simple/XS/Package.pm' => {
    '0.10' => '0.10'
  },
  'ppport.h' => {
    '0.01' => '0.01',
    '0.02' => '0.06',
    '0.03' => '0.10'
  },
  't/00_load.t' => {
    '0.01' => '0.04'
  },
  't/01_basic.t' => {
    '0.01' => '0.01',
    '0.02' => '0.03',
    '0.03' => '0.04',
    '0.04' => '0.08'
  },
  't/02_stress.t' => {
    '0.01' => '0.01',
    '0.02' => '0.02',
    '0.03' => '0.03',
    '0.04' => '0.04',
    '0.05' => '0.06',
    '0.06' => '0.08',
    '0.07' => '0.10'
  },
  't/03_magic.t' => {
    '0.01' => '0.01',
    '0.02' => '0.04',
    '0.03' => '0.06',
    '0.04' => '0.08'
  },
  't/04_overload.t' => {
    '0.01' => '0.01',
    '0.02' => '0.04',
    '0.03' => '0.08'
  },
  't/99_speed.t' => {
    '0.01' => '0.01',
    '0.02' => '0.02',
    '0.03' => '0.07',
    '0.04' => '0.08'
  },
  't/FakeHeap.pm' => {
    '0.01' => '0.01',
    '0.02' => '0.08'
  },
  't/Ties.pm' => {
    '0.01' => '0.01'
  },
  't/speed_array' => {
    '0.01' => '0.01',
    '0.02' => '0.07'
  },
  't/speed_binary' => {
    '0.01' => '0.01'
  },
  't/speed_binomial' => {
    '0.01' => '0.01'
  },
  't/speed_fibonacci' => {
    '0.01' => '0.01'
  },
  't/speed_hash' => {
    '0.01' => '0.01'
  },
  't/speed_priority' => {
    '0.01' => '0.01'
  },
  't/speed_scalar' => {
    '0.01' => '0.01'
  },
  'typemap' => {
    '0.01' => '0.01',
    '0.02' => '0.07'
  }
);

use Carp;

sub released {
    my ($package, $version) = @_;
    my $p = $package;
    $p =~ s!::!/!g;
    my $history = $history{"lib/$p.pm"} ||
        croak "Could not find a history for package '$package'";
    my $lowest = 9**9**9;
    for my $v (keys %$history) {
        $lowest = $v if $v >= $version && $v < $lowest;
    }
    croak "No known version '$version' of package '$package'" if
        $lowest == 9**9**9;
    return $history->{$lowest};
}
# END HISTORY

1;