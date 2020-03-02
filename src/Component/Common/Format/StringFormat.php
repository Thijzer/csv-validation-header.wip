<?php

namespace Misery\Component\Common\Format;

interface StringFormat extends Format
{
    /** @param string $value */
    public function format(string $value);

    /** @param mixed $value */
    public function reverseFormat($value);
}
