<?php

namespace Misery\Component\Csv\Fetcher;

use Misery\Component\Csv\Filter\CsvDataFilter;
use Misery\Component\Csv\Reader\ReaderInterface;

class CsvDataFetcher
{
    private $reader;
    private $dataFilter;

    public function __construct(ReaderInterface $reader, CsvDataFilter $dataFilter)
    {
        $this->reader = $reader;
        $this->dataFilter = $dataFilter;
    }

    public function fetchRow(string $columnName, $reference): array
    {
        return $this->reader->find([$columnName => $reference])->getValues();
    }
}

// join(code, catalog_brand)

// example :: fetch('catalog_brand', 'code', 'nike') => brand nike his properties