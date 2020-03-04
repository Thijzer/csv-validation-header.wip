<?php

namespace Misery\Component\Validator;

class UpperCaseValidator extends AbstractValidator
{
    /** @var string */
    public const NAME = 'upper_case';

    /** @inheritDoc */
    public function validate($value, array $context = []): void
    {
        // remove underscores from string for validation since underscore returns false
        $value = str_replace('_', '', $value);

        if (is_string($value) && !ctype_upper($value)) {
            // constraint
            $this->getValidationCollector()->collect(
                new Constraint\UpperCaseConstraint(),
                sprintf(Constraint\UpperCaseConstraint::INVALID_FORMAT, $value),
                $context
            );
        }
    }
}