<?php

namespace Misery\Component\Common\Processor;

interface CsvDataProcessorInterface
{
    public function processRow(array $row): array;
}