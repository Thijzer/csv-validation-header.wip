<?php

namespace Component\Validator;

abstract class AbstractValidator
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

    abstract function validate($value, array $options = []): void;
}