<?php

namespace Misery\Component\Csv\Reader;

interface CsvReaderInterface
{
    public function getRow(int $line): self;
    public function getRows(array $lines): self;
    public function getColumnNames(string $columnName): self;
    public function getColumns(string...$columnNames): self;
    public function filter(array $constraints): self;
    public function getIterator(): \Iterator;
    public function getValues(): array;
}