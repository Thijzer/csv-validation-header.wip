<?php

namespace RFC\Component\Validator;

use RFC\Component\Format\SnakeCaseFormat;

class SnakeCaseValidator
{
    public function validate($value)
    {
        $formatter = new SnakeCaseFormat();
        if ($formatter->format($value) !== $value) {
            // constraint

        }
    }
}