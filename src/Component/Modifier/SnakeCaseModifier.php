<?php

namespace Misery\Component\Modifier;

use Misery\Component\Common\Modifier\CellModifier;

class SnakeCaseModifier implements CellModifier
{
    public const NAME = 'snake_case';

    /**
     *
     * @param string $value
     * @return string
     */
    public function modify(string $value): string
    {
        $delimiter = '_';

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value), 'UTF-8');
        }

        return $value;
    }
}