<?php

namespace Misery\Component\Validator\Constraint;

class ReferencedColumnConstraint implements Constraint
{
    public const UNKNOWN_REFERENCE = 'Unknown reference %s found %s for %s';
}