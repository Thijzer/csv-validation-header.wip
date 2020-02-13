<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\StringFormat;

class IntToStringFormat implements StringFormat
{
    public const NAME = 'integer';

    /** @inheritDoc */
    public function format(string $value): int
    {
        return (int) $value;
    }

    /**
     * @param int $value
     * @return string
     */
    public function reverseFormat($value): string
    {
        return (string) $value;
    }
}