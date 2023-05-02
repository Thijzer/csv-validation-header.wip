<?php

namespace Misery\Component\Common\Utils;

class ValueFormatter
{
    /**
     * format('%amount% %unit%', ['amount' => 1, 'unit' => 'GRAM']);
     * returns '1 GRAM'
     *
     * @param array $values
     * @param string $format
     *
     * @return string|string[]
     */
    public static function format(string $format, array $values)
    {
        $tmp = [];
        foreach ($values as $key => $value) {
            if (is_array($value) || empty($value)) {
                unset($values[$key]);

                continue;
            }

            $tmp[] = "%$key%";
        }

        return str_replace($tmp, array_values($values), $format);
    }

    public static function formatMulti(array $formats, array $values): array
    {
        foreach ($formats as &$format) {
            $format = static::format($format, $values);
        }

        return $formats;
    }
}