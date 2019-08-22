<?php

namespace Misery\Component\Validator;

class IntegerValidator extends AbstractValidator
{
    public function validate($value, array $options = []): void
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->getCollector()->collect(
                new Constraint\IntegerConstraint(),
                Constraint\IntegerConstraint::INVALID_VALUE
            );
        }
    }
}