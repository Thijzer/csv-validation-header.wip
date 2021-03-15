<?php

namespace Misery\Component\Statement;

class Field
{
    private $field;
    private $value;

    public function __construct(string $field, string $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue(): ? string
    {
        return $this->value;
    }
}