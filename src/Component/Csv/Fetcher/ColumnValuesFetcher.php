<?php

namespace Misery\Component\Csv\Fetcher;

use Misery\Component\Csv\Reader\RowReaderInterface;

class ColumnValuesFetcher
{
    public static function fetch(RowReaderInterface $reader, string $columnName)
    {
        return array_values($reader
            ->map(static function(array $row) use ($columnName) {
                return $row[$columnName];
            })
            ->getItems())
        ;
    }
}