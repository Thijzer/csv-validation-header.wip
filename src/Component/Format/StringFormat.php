<?php

namespace Component\Format;

class StringFormat
{
    public function format($value): string
    {
        return (string) $value;
    }
}