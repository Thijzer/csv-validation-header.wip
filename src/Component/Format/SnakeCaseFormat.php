<?php

namespace Component\Format;

class SnakeCaseFormat
{
    /**
     * badly converts
     * @Abc to @_abc
     * @ABC to @_a_b_c
     *
     *
     * @param string $value
     * @return string
     */
    public function format(string $value): string
    {
        $delimiter = '_';

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value), 'UTF-8');
        }

        return $value;
    }
}