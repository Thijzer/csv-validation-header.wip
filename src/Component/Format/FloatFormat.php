<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\Format;

class FloatFormat implements Format
{
    public const NAME = 'float';

    public function format($value): float
    {
        return (float) $value;
    }

    public function reverseFormat($value): string
    {
        return (string) $value;
    }
}