<?php

namespace Misery\Component\Validator;

use Misery\Component\Modifier\SnakeCaseModifier;

class SnakeCaseValidator extends AbstractValidator
{
    /** @var string */
    public const NAME = 'snake_case';

    /** @inheritDoc */
    public function validate($value, array $context = []): void
    {
        $formatter = new SnakeCaseModifier();
        if (is_string($value) && $formatter->modify($value) !== $value) {
            // constraint
            $this->getValidationCollector()->collect(
                new Constraint\SnakeCaseConstraint(),
                sprintf(Constraint\SnakeCaseConstraint::INVALID_FORMAT, $value)
            );
        }
    }
}