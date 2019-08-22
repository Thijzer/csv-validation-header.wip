<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\Format;

class IntFormat implements Format
{
    public const NAME = 'integer';

    public function format($value): int
    {
        return (int) $value;
    }

    public function reverseFormat($value): string
    {
        return (string) $value;
    }
}