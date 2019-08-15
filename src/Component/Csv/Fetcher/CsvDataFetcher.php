<?php

namespace Component\Csv\Fetcher;

use Component\Csv\Filter\CsvDataFilter;
use Component\Csv\Reader\ReaderInterface;
use phpDocumentor\Reflection\Types\Array_;

class CsvDataFetcher
{
    private $reader;
    private $dataFilter;

    public function __construct(ReaderInterface $reader, CsvDataFilter $dataFilter)
    {
        $this->reader = $reader;
        $this->dataFilter = $dataFilter;
    }

    public function fetchRow(string $filename, string $columnName, $reference): array
    {
        return $this->dataFilter->filter($this->reader->read($filename), $columnName, $reference);
    }
}

// join(code, catalog_brand)

// example :: fetch('catalog_brand', 'code', 'nike') => brand nike his properties