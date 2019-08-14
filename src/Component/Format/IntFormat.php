<?php

namespace Component\Format;

class IntFormat
{
    public function format($value): int
    {
        return (int) $value;
    }
}