<?php

namespace Misery\Component\Validator;

class RequiredValidator extends AbstractValidator
{
    public const NAME = 'required';

    public function validate($value, array $context = []): void
    {
        if (null === $value || '' === $value) {
            $this->getCollector()->collect(
                new Constraint\RequiredConstraint(),
                Constraint\RequiredConstraint::NOT_BLANK,
                $context
            );
        }
    }
}