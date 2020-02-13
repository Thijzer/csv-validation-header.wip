<?php

namespace Misery\Component\Common\Format;

interface ArrayFormat extends Format
{
    /** @param array $value */
    public function format(array $value);

    /** @param string|array $value */
    public function reverseFormat($value);
}
