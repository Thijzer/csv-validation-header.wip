<?php

namespace Tests\Misery\Component\Csv\Reader;

use Misery\Component\Filter\ColumnFilter;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReader;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    public function test_parse_a_column(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new ItemReader(new CsvParser($file, ','));

        $filteredReader = ColumnFilter::filter($reader, 'first_name');
        $data = iterator_to_array($filteredReader->getIterator());

        $this->assertSame(\count($data), 300);
    }

    public function test_parse_columns(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new ItemReader($parser = new CsvParser($file, ','));

        $filteredReader = ColumnFilter::filter($reader, 'first_name', 'last_name');

        $this->assertSame(
            array_keys(current($filteredReader->getItems())), ['first_name', 'last_name']
        );
    }

    public function test_parse_a_row(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new ItemReader($parser = new CsvParser($file, ','));

        $filteredReader = $reader->index([150]);

        $this->assertSame(array_keys($filteredReader->getItems()[150]), $parser->getHeaders());
    }

    public function test_parse_rows(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new ItemReader($parser = new CsvParser($file, ','));

        $filteredReader = $reader->index([149, 150]);

        $this->assertSame(count($filteredReader->getItems()), 2);
    }

    public function test_mix_parse_rows_and_columns(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new ItemReader(new CsvParser($file, ','));

        $reader
            ->index([149, 150])
        ;
        $filteredReader = ColumnFilter::filter($reader, 'first_name', 'last_name');

        $result = [
            149 => [
                'first_name' => 'Fifi',
                'last_name' => 'Rapier',
            ],
            150 => [
                'first_name' => 'Catherina',
                'last_name' => 'Fewless',
            ],
        ];

        $this->assertSame($result, $filteredReader->getItems());
    }

    public function test_find_items(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new ItemReader(new CsvParser($file, ','));

        $reader
            ->find(['first_name' => 'Fifi'])
        ;
        $filteredReader = ColumnFilter::filter($reader, 'first_name', 'last_name');

        $result = [
            149 => [
                'first_name' => 'Fifi',
                'last_name' => 'Rapier',
            ],
        ];

        $this->assertSame($result, $filteredReader->getItems());
    }

    public function test_filter_items(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new ItemReader(new CsvParser($file, ','));

        $reader
            ->filter(function ($row) {
                return $row['last_name'] === 'Rapier';
            })
        ;
        $filteredReader = ColumnFilter::filter($reader, 'first_name', 'last_name');

        $result = [
            149 => [
                'first_name' => 'Fifi',
                'last_name' => 'Rapier',
            ],
        ];

        $this->assertSame($result, $filteredReader->getItems());
    }
}