<?php

namespace Misery\Component\Validator;

class RequiredValidator extends AbstractValidator
{
    /** @var string */
    public const NAME = 'required';

    /** @inheritDoc */
    public function validate($value, array $context = []): void
    {
        if (is_string($value) && '' === $value) {
            $this->getValidationCollector()->collect(
                new Constraint\RequiredConstraint(),
                Constraint\RequiredConstraint::NOT_BLANK,
                $context
            );
        }
    }
}