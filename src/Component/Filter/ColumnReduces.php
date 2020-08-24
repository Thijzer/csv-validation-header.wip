<?php

namespace Misery\Component\Filter;

use Misery\Component\Reader\ReaderInterface;

/**
 * / input
 *  [
 *       'id' => "2",
 *       'first_name' => 'Mieke',
 *       'last_name' => 'Cauter',
 *       'phone' => '1234556356',
 *  ],
 *  [
 *       'id' => "3",
 *       'first_name' => 'Gordie',
 *       'last_name' => 'Ramsey',
 *       'phone' => '1234556',
 *  ],
 *
 * ColumnReduces::reduce($reader, 'first_name');
 *
 * / output
 *  [
 *       'first_name' => 'Mieke',
 *  ],
 *  [
 *       'first_name' => 'Gordie',
 *  ],
 *
 */
class ColumnReduces
{
    public static function reduce(ReaderInterface $reader, string ...$columnNames): ReaderInterface
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
}