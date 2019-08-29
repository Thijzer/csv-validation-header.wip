<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cursor\CursorInterface;

interface ReaderInterface
{
    public function getRow(int $line): array;
    public function getColumn(string $columnName): array;
    public function getCursor(): CursorInterface;
    public function loop(callable $callable): void;
    //public function read(string $filename);
    public function indexColumn(string $columnName): void;
    public function findBy(array $filter, array $options = []): array;
    public function findOneBy(array $filter, array $options = []): array;
}