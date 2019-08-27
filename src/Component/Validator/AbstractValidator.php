<?php

namespace Misery\Component\Validator;

use Misery\src\Component\Validator\ValidatorInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    private $collector;

    public function __construct(ValidationCollector $collector)
    {
        $this->collector = $collector;
    }

    public function getCollector(): ValidationCollector
    {
        return $this->collector;
    }

    abstract function validate($value, array $context = []): void;
}