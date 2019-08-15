<?php

namespace Component\Validator;

use Component\Validator\Constraint\SnakeCaseConstraint;
use Component\Format\SnakeCaseFormat;

class SnakeCaseValidator extends AbstractValidator
{
    public function validate($value, array $options = []): void
    {
        $formatter = new SnakeCaseFormat();
        if ($formatter->format($value) !== $value) {
            // constraint
            $this->getCollector()->collect(
                new Constraint\SnakeCaseConstraint(),
                Constraint\SnakeCaseConstraint::INVALID_FORMAT
            );
        }
    }
}