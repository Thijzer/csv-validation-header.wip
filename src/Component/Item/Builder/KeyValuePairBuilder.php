<?php

namespace Misery\Component\Item\Builder;

use Misery\Component\Filter\ColumnReducer;
use Misery\Component\Reader\ReaderInterface;

class KeyValuePairBuilder
{
    public static function build(ReaderInterface $reader, string $key, string $value)
    {
        $concat = [];
        foreach (ColumnReducer::reduce($reader, $key, $value)->getIterator() as $index => $array) {
            $concat[$array[$key]] = $array[$value];
        }

        return $concat;
    }
}