<?php

namespace RFC\Component\Csv\Fetcher;

use RFC\Component\Csv\Filter\CsvDataFilter;
use RFC\Component\Csv\Reader\ReaderInterface;

class CellDataFetcher
{
    private $reader;
    private $dataFilter;

    public function __construct(ReaderInterface $reader, CsvDataFilter $dataFilter)
    {
        $this->reader = $reader;
        $this->dataFilter = $dataFilter;
    }

    public function fetch(string $filename, string $reference, string $cellValue)
    {
        return $this->dataFilter->filter($this->reader->read($filename), $reference, $cellValue);
    }
}

// join(code, catalog_brand)