<?php

namespace Misery\Component\Converter;

class AkeneoCsvStructureConverter implements Converter
{
    public static function convert(array $item, array $codes)
    {
        $separator = '-';

        $output = [];
        foreach ($item as $key => $value) {

            $keys = explode($separator, $key);
            if (false === in_array($keys[0], $codes)) {
                continue;
            }

            $output[$keys[0]][] = [
                'data' => $value,
                'locale' => $keys[1] ?? null,
                'scope' => $keys[2] ?? null,
            ];
            unset($item[$key]);
        }

        return $item+$output;
    }

    public static function revert(array $item, array $codes)
    {
        $separator = '-';

        $output = [];
        foreach ($item as $key => $value) {

            if (in_array($key, $codes)) {
                foreach ($value as $valueItem) {
                    $keys = implode($separator, array_filter([$key, $valueItem['locale'], $valueItem['scope']]));
                    $output[$keys] = $valueItem['data'];
                }

                unset($item[$key]);
            }
        }

        return $item+$output;
    }
}