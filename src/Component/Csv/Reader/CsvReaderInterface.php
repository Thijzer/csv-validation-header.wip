<?php

namespace Misery\Component\Csv\Reader;

interface CsvReaderInterface
{
    public function getRow(int $line): ItemCollection;
    public function getRows(array $lines): ItemCollection;
    public function getColumn(string $columnName): ItemCollection;
    public function getColumns(string...$columnNames): ItemCollection;
    public function getIterator(): \Generator;
    public function getValues(): array;
}