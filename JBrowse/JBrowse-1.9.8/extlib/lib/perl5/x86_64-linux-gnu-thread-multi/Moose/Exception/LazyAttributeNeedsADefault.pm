package Moose::Exception::LazyAttributeNeedsADefault;
BEGIN {
  $Moose::Exception::LazyAttributeNeedsADefault::AUTHORITY = 'cpan:STEVAN';
}
$Moose::Exception::LazyAttributeNeedsADefault::VERSION = '2.1211';
use Moose;
extends 'Moose::Exception';
with 'Moose::Exception::Role::EitherAttributeOrAttributeName';

sub _build_message {
    my $self = shift;
    "You cannot have a lazy attribute (".$self->attribute_name.") without specifying a default value for it";
}

1;
