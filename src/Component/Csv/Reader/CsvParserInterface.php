<?php

namespace Component\Csv\Reader;

interface CsvParserInterface extends \Countable, \SeekableIterator
{
    public function getHeaders(): array;
    public function hasHeaders(): bool;
    public function getRow(int $line): array;
    public function getColumn(string $columnName): array;
    public function indexColumn(string $columnName): void;
    public function findBy(array $filter): array;
    public function findOneBy(array $filter): array;
}