<?php

namespace Misery\Component\Validator;

use Misery\Component\Modifier\SnakeCaseModifier;

class SnakeCaseValidator extends AbstractValidator
{
    public const NAME = 'snake_case';

    public function validate($value, array $options = []): void
    {
        $formatter = new SnakeCaseModifier();
        if ($formatter->modify($value) !== $value) {
            // constraint
            $this->getValidationCollector()->collect(
                new Constraint\SnakeCaseConstraint(),
                Constraint\SnakeCaseConstraint::INVALID_FORMAT
            );
        }
    }
}