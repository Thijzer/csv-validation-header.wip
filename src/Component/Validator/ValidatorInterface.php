<?php

namespace Misery\Component\Validator;

interface ValidatorInterface
{
    public function validate($value, array $context = []): void;
}