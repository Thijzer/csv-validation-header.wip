<?php

namespace Misery\Component\Csv\Reader;

interface ReaderInterface
{
    public function getRow(int $line): array;
    public function getColumn(string $columnName): array;

    public function loop(callable $callable): void;
    //public function read(string $filename);
    public function indexColumn(string $columnName): void;
    public function findBy(array $filter): array;
    public function findOneBy(array $filter): array;
}