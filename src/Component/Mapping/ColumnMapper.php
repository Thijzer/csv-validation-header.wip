<?php

namespace Misery\Component\Mapping;

/**
 * Class ColumnMapper
 * @package Misery\Component\Mapping
 */
class ColumnMapper implements Mapper
{
    public function map(array $item, array $mappings)
    {
        if (count(array_diff(array_keys($mappings), array_keys($item))) == count(array_keys($mappings))) {
            throw new \InvalidArgumentException(sprintf(
                'No mapped items %s are not found in item.',
                json_encode($mappings)
            ));
        }

        $keys = [];
        foreach ($item as $key => $value) {
            if (isset($mappings[$key])) {
                $keys[] = $mappings[$key];
                continue;
            }
            $keys[] = $key;
        }

        return array_combine($keys, array_values($item));
    }
}