<?php

namespace Misery\Component\Mapping;

use Akeneo\Tool\Component\Connector\Exception\DataArrayConversionException;

/**
 * Class ColumnMapper
 * @package Misery\Component\Mapping
 */
class ColumnMapper implements Mapper
{
    public function mapColumns(array $item, array $mappings)
    {
        if (count(array_diff(array_keys($mappings), array_keys($item))) > 0){
            return new DataArrayConversionException('Mapping items are not found in item ');
        }

        foreach ($mappings as $key => $value) {
            $item[$value] = $item[$key];
            unset($item[$key]);
        }

        return $item;
    }
}
