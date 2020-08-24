<?php

namespace Misery\Component\Item\Builder;

use Misery\Component\Filter\ColumnReduces;
use Misery\Component\Reader\ReaderInterface;

class ReferenceBuilder
{
    public static function build(ReaderInterface $reader, string ...$references)
    {
        $concat = [];
        foreach (ColumnReduces::reduce($reader, ...$references)->getIterator() as $index => $array) {
            $concat[implode('-', array_keys($array))][$index] = implode('-', array_values($array));
        }

        return $concat;
    }

    public static function buildValues(ReaderInterface $reader, string ...$references)
    {
        return current(static::build($reader, ...$references));
    }
}