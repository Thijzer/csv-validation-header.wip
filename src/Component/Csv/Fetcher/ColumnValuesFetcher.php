<?php

namespace Misery\Component\Csv\Fetcher;

use Misery\Component\Csv\Reader\ReaderInterface;

class ColumnValuesFetcher
{
    public static function fetch(ReaderInterface $reader, string ...$columnNames): ReaderInterface
    {
        return $reader
            ->map(static function(array $row) use ($columnNames) {
                $nRow = [];
                foreach ($columnNames as $columnName) {
                    $nRow[$columnName] = $row[$columnName];
                }

                return $nRow;
            })
        ;
    }

    public static function fetchValues(ReaderInterface $reader, string ...$columnName): array
    {
        return self::fetch($reader, $columnName)->getItems();
    }
}