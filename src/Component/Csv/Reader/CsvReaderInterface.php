<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cursor\CursorInterface;

interface CsvReaderInterface
{
    public function getRow(int $line): array;
    public function getRows(array $lines): array;
    public function getColumn(string $columnName): array;
    public function getCursor(): CursorInterface;
    public function loop(callable $callable): void;
    public function indexColumn(string $columnName): void;
}