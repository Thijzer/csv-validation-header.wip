<?php

namespace Component\Format;

class FloatFormat
{
    public function format($value): float
    {
        return (float) $value;
    }
}