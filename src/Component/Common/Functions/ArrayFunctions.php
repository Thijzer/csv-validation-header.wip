<?php

namespace Misery\Component\Common\Functions;

class ArrayFunctions
{
    /**
     * ISSUE with unflatten
     * label-23 => becomes label[23]
     * but label => resets label[] via overwriting.
     *
     * Reverse flatten an associative array to multidimensional one
     * by separating keys on separator.
     *
     * @param array $array
     * @param string $separator
     *
     * @return array
     */
    public static function unflatten(array $array, $separator = '.')
    {
        $output = [];
        foreach ($array as $key => $value) {
            static::array_set($output, $key, $value, $separator);
            if (\is_array($value) && !strpos($key, $separator)) {
                $nested = static::unflatten($value, $separator);
                $output[$key] = $nested;
            }
        }

        return $output;
    }

    /**
     * Flattens an multi-dimensional array to associative array
     * by adding combining the keys with a prefix.
     *
     * @param array $array
     * @param string $prefix
     * @param mixed $separator
     *
     * @return array
     */
    public static function flatten(array $array, $separator = '.', $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $result += static::flatten($value, $separator, $prefix . $key . $separator);
                continue;
            }
            $result[$prefix . $key] = $value;
        }

        return $result;
    }

    public static function multiCompare(array $a, array $b): array
    {
        $a = static::flatten($a);
        $b = static::flatten($b);

        return static::unflatten(array_diff($b, $a));
    }

    /**
     * Normalizes array keys to integers.
     *
     * @param array $param
     *
     * @return array
     */
    public static function normalizeKeys(array $param)
    {
        $keys = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($param)) as $key => $val) {
            $keys[$key] = '';
        }

        $data = [];
        foreach ($param as $values) {
            $data[] = array_merge($keys, $values);
        }

        return $data;
    }

    public static function array_set(&$array, $key, $value, $prefix = '.'): array
    {
        if (null === $key) {
            return $array = $value;
        }

        $keys = explode($prefix, $key);

        while (\count($keys) > 1) {
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !\is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;

        return $array;
    }
}
