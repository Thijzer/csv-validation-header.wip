<?php

namespace Misery\Component\Validator;

use Misery\Component\Validator\ValidatorInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    /** @var ValidationCollector */
    private $collector;

    public function __construct(ValidationCollector $collector)
    {
        $this->collector = $collector;
    }

    public function getValidationCollector(): ValidationCollector
    {
        return $this->collector;
    }

    /** @inheritDoc */
    abstract function validate($value, array $context = []): void;
}