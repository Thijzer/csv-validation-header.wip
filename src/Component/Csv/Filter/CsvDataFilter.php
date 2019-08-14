<?php

namespace RFC\Component\Csv\Filter;

class CsvDataFilter
{
    public function filter($dataStream, $reference, $cellValue): array
    {
        return $dataStream->findBy([$reference => $cellValue]);
    }
}