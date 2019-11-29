<?php

namespace Misery\Component\Csv\Reader;

interface ReaderInterface
{
    public function findBy(array $filter): array;
    public function findOneBy(array $filter): array;
}