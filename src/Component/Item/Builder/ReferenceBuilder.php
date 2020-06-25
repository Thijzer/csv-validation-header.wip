<?php

namespace Misery\Component\Item\Builder;

use Misery\Component\Filter\ColumnFilter;
use Misery\Component\Reader\ReaderInterface;

class ReferenceBuilder
{
    public static function build(ReaderInterface $reader, string ...$references)
    {
        $concat = [];
        foreach (ColumnFilter::filter($reader, ...$references)->getIterator() as $index => $array) {
            $concat[implode('-', array_keys($array))][$index] = strtolower(implode('-', array_values($array)));
        }

        return $concat;
    }
}