<?php

namespace Misery\Component\Csv\Reader;

interface CsvInterface
{
    public function getHeaders(): array;
    public function hasHeaders(): bool;
}