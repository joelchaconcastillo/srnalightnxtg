package Moose::Exception::CannotAugmentNoSuperMethod;
BEGIN {
  $Moose::Exception::CannotAugmentNoSuperMethod::AUTHORITY = 'cpan:STEVAN';
}
$Moose::Exception::CannotAugmentNoSuperMethod::VERSION = '2.1209';
use Moose;
extends 'Moose::Exception';
with 'Moose::Exception::Role::ParamsHash';

has 'class' => (
    is       => 'ro',
    isa      => 'Str',
    required => 1
);

has 'method_name' => (
    is       => 'ro',
    isa      => 'Str',
    required => 1
);

sub _build_message {
    my $self = shift;
    "You cannot augment '".$self->method_name."' because it has no super method";
}

1;
