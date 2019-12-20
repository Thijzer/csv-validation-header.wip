<?php

namespace Misery\Component\Csv\Filter;

use Misery\Component\Csv\Reader\ReaderInterface;

class CsvDataFilter
{
    public function filter(ReaderInterface $dataStream, string $columnName, $reference): array
    {
        return $dataStream->find([$columnName => $reference]);
    }
}