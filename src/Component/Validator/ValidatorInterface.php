<?php

namespace Misery\src\Component\Validator;

interface ValidatorInterface
{
    public function validate($value, array $context = []): void;
}