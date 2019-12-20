<?php

namespace Misery\Component\Validator;

class IntegerValidator extends AbstractValidator
{
    public const NAME = 'is_integer';

    public function validate($value, array $context = []): void
    {
        if (empty($value)) {
            return;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->getValidationCollector()->collect(
                new Constraint\IntegerConstraint(),
                sprintf(Constraint\IntegerConstraint::INVALID_VALUE, $value),
                $context
            );
        }
    }
}