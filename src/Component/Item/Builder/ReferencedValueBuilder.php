<?php

namespace Misery\Component\Item\Builder;

use Misery\Component\Filter\ColumnFilter;
use Misery\Component\Reader\ReaderInterface;

class ReferencedValueBuilder
{
    public static function combine(ReaderInterface $reader, string ...$references)
    {
        $concat = [];
        foreach (ColumnFilter::filter($reader, ...$references)->getIterator() as $array) {
            foreach ($array as $pointer => $item) {
                $concat[$pointer] = isset($concat[$pointer]) ? $concat[$pointer].'|'.$item : $item;
            }
        }

        return $concat;
    }
}