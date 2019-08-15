<?php

namespace Component\Csv\Filter;

class CsvDataFilter
{
    public function filter($dataStream, string $columnName, $reference): array
    {
        return $dataStream->findOneBy([$columnName => $reference]);
    }
}