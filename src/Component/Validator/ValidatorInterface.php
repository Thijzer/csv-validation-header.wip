<?php

namespace Misery\Component\Validator;

interface ValidatorInterface
{
    /**
     * @param array|string $value
     * @param array $context
     */
    public function validate($value, array $context = []): void;
}