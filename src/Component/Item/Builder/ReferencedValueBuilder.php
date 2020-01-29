<?php

namespace Misery\Component\Item\Builder\Combine;

use Misery\Component\Csv\Fetcher\ColumnValuesFetcher;
use Misery\Component\Csv\Reader\ReaderInterface;

class ReferencedValueBuilder
{
    public static function combine(ReaderInterface $reader, string ...$references)
    {
        $concat = [];
        foreach (ColumnValuesFetcher::fetch($reader, ...$references)->getIterator() as $array) {
            foreach ($array as $pointer => $item) {
                $concat[$pointer] = isset($concat[$pointer]) ? $concat[$pointer].'|'.$item : $item;
            }
        }

        return $concat;
    }
}