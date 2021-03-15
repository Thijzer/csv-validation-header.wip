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
            if (\is_array($value) && \strpos($key, $separator) !== false) {
                $output[$key] = static::unflatten($value, $separator);
            }
        }

        foreach (\array_keys($output) as $key) {
            if (\array_key_exists($key, $array) && \is_array($output[$key])) {
                $output[$key] = ['' => $array[$key]] + $output[$key];
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
            if (\is_array($value) && !empty($value)) {
                $result += static::flatten($value, $separator, $prefix . $key . $separator);
                continue;
            }

            $result[$key === '' ? \rtrim($prefix, $separator): $prefix . $key] = $value;
        }

        return $result;
    }

    public static function merge(array $a, array $b)
    {
        return static::unflatten(array_merge(static::flatten($a), static::flatten($b)));
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

    /**
     * Merge Array A and B together as One
     * Some new keys will be generated to avoid collision with same values
     * Array A is leading
     *
     * @param array $a
     * @param array $b
     *
     * @return array
     */
    public static function arrayUnion(array $a, array $b)
    {
        return array_merge(
            array_intersect($a, $b),
            array_diff($a, $b),
            array_diff($b, $a)
        );
    }

    /**
     * @param $haystack
     * @param array $needles
     *
     * @return bool
     */
    public static function strpos_array($haystack, $needles = [])
    {
        return $haystack !== str_replace($needles, '', $haystack);
    }

    public static function array_set(&$array, $key, $value, $prefix = '.'): array
    {
        if (null === $key) {
            return $array = $value;
        }

        $keys = \explode($prefix, $key);

        while (\count($keys) > 1) {
            $key = \array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !\is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[\array_shift($keys)] = $value;

        return $array;
    }
}
