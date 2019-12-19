<?php

namespace Misery\Component\Csv\Reader;

interface CsvReaderInterface extends ReaderInterface
{
    public function getRow(int $line): self;
    public function getRows(array $lines): self;
    public function getColumnNames(string $columnName): self;
    public function getColumns(string...$columnNames): self;
    public function find(array $constraints): self;
    public function filter(callable $callable): CsvReaderInterface;
    public function getIterator(): \Iterator;
    public function getValues(): array;
}