<?php

namespace Component\Csv\Validator\Constraint;

use Component\Validator\Constraint\Constraint;

class ReferencedColumnConstraint implements Constraint
{
    public const UNKNOWN_REFERENCE = 'Unknown reference found for %s : %s';
}