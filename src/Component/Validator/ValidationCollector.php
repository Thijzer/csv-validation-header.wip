<?php

namespace Component\Validator;

use Component\Validator\Constraint\Constraint;

class ValidationCollector
{
    private $constraints = [];

    public function collect(Constraint $constraint, string $message = null): void
    {
        $this->constraints[\get_class($constraint)] = $message;
    }

    public function hasConstraints(): bool
    {
        return \count($this->constraints) > 0;
    }

    public function getMessages(): array
    {
        $messages = array_values($this->constraints);

        $this->constraints = [];

        return $messages;
    }
}