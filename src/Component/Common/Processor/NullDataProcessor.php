<?php

namespace Misery\Component\Common\Processor;

class NullDataProcessor implements CsvDataProcessorInterface
{
    public function processRow(array $row): array
    {
        return $row;
    }
}