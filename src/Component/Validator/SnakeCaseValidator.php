<?php

namespace Misery\Component\Validator;

use Misery\Component\Modifier\SnakeCaseModifier;

class SnakeCaseValidator extends AbstractValidator
{
    public function validate($value, array $options = []): void
    {
        $formatter = new SnakeCaseModifier();
        if ($formatter->modify($value) !== $value) {
            // constraint
            $this->getCollector()->collect(
                new Constraint\SnakeCaseConstraint(),
                Constraint\SnakeCaseConstraint::INVALID_FORMAT
            );
        }
    }
}