<?php

namespace Component\Csv\Fetcher;

use Component\Csv\Filter\CsvDataFilter;
use Component\Csv\Reader\ReaderInterface;

class CellDataFetcher
{
    private $reader;
    private $dataFilter;

    public function __construct(ReaderInterface $reader, CsvDataFilter $dataFilter)
    {
        $this->reader = $reader;
        $this->dataFilter = $dataFilter;
    }

    public function fetch(string $filename, string $columnName, $reference)
    {
        return $this->dataFilter->filter($this->reader->read($filename), $columnName, $reference);
    }
}

// join(code, catalog_brand)

// example :: fetch('catalog_brand', 'code', 'nike') => brand nike his properties