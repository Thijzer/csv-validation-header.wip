<?php

namespace Component\Csv\Validator\Constraint;

use Component\Validator\Constraint\Constraint;

class UniqueValueConstraint implements Constraint
{
    public const UNIQUE_VALUE = 'Duplicate Value found value: %s';
}