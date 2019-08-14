<?php

namespace RFC\Component\Format;

class StringFormat
{
    public function format($value): string
    {
        return (string) $value;
    }
}