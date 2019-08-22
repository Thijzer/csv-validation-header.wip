<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\Format;

class StringFormat implements Format
{
    public const NAME = 'string';

    public function format($value): string
    {
        return $value;
    }

    public function reverseFormat($value)
    {
        return $value;
    }
}