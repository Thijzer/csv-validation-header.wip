<?php

namespace Tests\Component\Csv\Fetcher;

use Component\Csv\Fetcher\CsvDataFetcher;
use Component\Csv\Filter\CsvDataFilter;
use Component\Csv\Reader\CsvReader;
use PHPUnit\Framework\TestCase;

class CsvDataFetcherTest extends TestCase
{
    // join(code, catalog_brand, X)
    public function test_it_should_fetch_data_from_reader(): void
    {
        $exampleFile = __DIR__ . '/../../../examples/example_no_format_row.csv';

        $reader = new CsvReader();
        $filter = new CsvDataFilter();
        $fetcher = new CsvDataFetcher($reader, $filter);

        $row = $fetcher->fetchRow($exampleFile, $code = 'brand', $x = 'puma');

        $this->assertSame($row['brand'], 'puma');
        $this->assertSame($row['code'], 'Test-3');
    }
}