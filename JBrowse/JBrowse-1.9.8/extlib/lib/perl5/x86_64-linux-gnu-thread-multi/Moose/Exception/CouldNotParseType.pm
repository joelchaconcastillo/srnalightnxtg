package Moose::Exception::CouldNotParseType;
BEGIN {
  $Moose::Exception::CouldNotParseType::AUTHORITY = 'cpan:STEVAN';
}
$Moose::Exception::CouldNotParseType::VERSION = '2.1211';
use Moose;
extends 'Moose::Exception';

has 'type' => (
    is       => 'ro',
    isa      => 'Str',
    required => 1
);

has 'position' => (
    is       => 'ro',
    isa      => 'Int',
    required => 1
);

sub _build_message {
    my $self = shift;
    my $type = $self->type;
    my $length = length($type);
    my $position = $self->position;

    return "'$type' didn't parse (parse-pos=$position"
        . " and str-length=$length)";
}

1;