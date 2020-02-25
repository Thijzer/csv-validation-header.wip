<?php

namespace Misery\Component\Validator\Constraint;

class UniqueValueConstraint implements Constraint
{
    public const UNIQUE_VALUE = 'Duplicate Value(s) found: %s';
}