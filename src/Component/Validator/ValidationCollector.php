<?php

namespace Misery\Component\Validator;

use Misery\Component\Validator\Constraint\Constraint;

class ValidationCollector
{
    /** @var array */
    private $constraints = [];

    public function collect(Constraint $constraint, string $message = null, array $context = []): void
    {
        $this->constraints[] = [
            'message' => $message,
            'constraint' => \get_class($constraint),
            'context' => $context,
        ];
    }

    public function hasConstraints(): bool
    {
        return \count($this->constraints) > 0;
    }

    public function getErrors(): array
    {
        $messages = [];
        foreach ($this->constraints as $constraint) {
            $messages[] = implode(' ', [$constraint['message'], json_encode($constraint['context'])]);
        }

        $this->constraints = [];

        return $messages;
    }
}