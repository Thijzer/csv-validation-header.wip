<?php

namespace Tests\Misery\Component\Component\Csv\Fetcher;

use Misery\Component\Csv\Fetcher\CsvDataFetcher;
use Misery\Component\Csv\Filter\CsvDataFilter;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Reader\RowReader;
use PHPUnit\Framework\TestCase;

class CsvDataFetcherTest extends TestCase
{
    // join(code, catalog_brand, X)
    public function test_it_should_fetch_data_from_reader(): void
    {
        $exampleFile = __DIR__ . '/../../../examples/example_no_format_row.csv';

        $reader = new RowReader(CsvParser::create($exampleFile));
        $filter = new CsvDataFilter();
        $fetcher = new CsvDataFetcher($reader, $filter);

        $row = $fetcher->fetchRow($code = 'brand', $x = 'puma');

        $this->assertSame($row['brand'], 'puma');
        $this->assertSame($row['code'], 'Test-3');
    }
}