<?php

namespace Misery\Component\Modifier;

use Misery\Component\Common\Modifier\Modifier;

class NullifyEmptyStringModifier implements Modifier
{
    public const NAME = 'nullify';

    public function modify($value)
    {
        if(\is_array($value)) {
            return array_map(function ($row) {
                return $this->modify($row);
            }, $value);
        }

        return '' === $value ? null : $value;
    }
}
