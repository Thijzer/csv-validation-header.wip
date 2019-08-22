<?php

namespace Misery\Component\Validator\Constraint;

class IntegerConstraint implements Constraint
{
    public const INVALID_VALUE = 'Value %s is not an integer';
}