<?php

namespace Misery\Component\Filter;

use Misery\Component\Reader\ReaderInterface;

class ColumnFilter
{
    public static function filter(ReaderInterface $reader, string ...$columnNames): ReaderInterface
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

    public static function filterItems(ReaderInterface $reader, string ...$columnName): array
    {
        return self::filter($reader, $columnName)->getItems();
    }
}