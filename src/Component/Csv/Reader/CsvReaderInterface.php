<?php

namespace Misery\Component\Csv\Reader;

interface CsvReaderInterface
{
    public function getRow(int $line): array;
    public function getRows(array $lines): array;
    public function getColumn(string $columnName): array;
    public function getColumns(string...$columnNames): array;
    public function getIterator(): \Generator;
}