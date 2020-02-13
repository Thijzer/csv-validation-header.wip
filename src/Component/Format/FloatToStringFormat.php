<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\StringFormat;

class FloatToStringFormat implements StringFormat
{
    public const NAME = 'float';

    /** @inheritDoc */
    public function format(string $value): float
    {
        return (float) $value;
    }

    /**
     * @param float $value
     * @return string
     */
    public function reverseFormat($value): string
    {
        return (string) $value;
    }
}