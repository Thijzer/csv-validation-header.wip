<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\StringFormat;

class StringToStringFormat implements StringFormat
{
    public const NAME = 'string';

    /** @inheritDoc */
    public function format(string $value): string
    {
        return $value;
    }

    /** @inheritDoc */
    public function reverseFormat($value)
    {
        return $value;
    }
}