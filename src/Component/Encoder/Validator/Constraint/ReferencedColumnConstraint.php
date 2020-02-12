<?php

namespace Misery\Component\Encoder\Validator\Constraint;

use Misery\Component\Validator\Constraint\Constraint;

class ReferencedColumnConstraint implements Constraint
{
    public const UNKNOWN_REFERENCE = 'Unknown reference %s found %s for %s';
}