<?php

namespace Misery\Component\Common\Processor;

class NullDataProcessor
{
    public function processRow(array $row): array
    {
        return $row;
    }
}