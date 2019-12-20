<?php

namespace Tests\Misery\Component\Component\Csv\Reader;

use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Reader\RowReader;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    public function test_parse_a_column(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new RowReader(new CsvParser($file, ','));

        $filteredReader = $reader->getColumns('first_name');
        $data = iterator_to_array($filteredReader->getIterator());

        $this->assertSame(\count($data), 300);
    }

    public function test_parse_columns(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new RowReader($parser = new CsvParser($file, ','));

        $filteredReader = $reader->getColumns('first_name', 'last_name');

        $this->assertSame(
            array_keys(current($filteredReader->getValues())), ['first_name', 'last_name']
        );
    }

    public function test_parse_a_row(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new RowReader($parser = new CsvParser($file, ','));

        $filteredReader = $reader->getRow(150);

        $this->assertSame(array_keys($filteredReader->getValues()[150]), $parser->getHeaders());
    }

    public function test_parse_rows(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new RowReader($parser = new CsvParser($file, ','));

        $filteredReader = $reader->getRows([149, 150]);

        $this->assertSame(count($filteredReader->getValues()), 2);
    }

    public function test_mix_parse_rows_and_columns(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new RowReader(new CsvParser($file, ','));

        $filteredReader = $reader
            ->getColumns('first_name', 'last_name')
            ->getRows([149, 150])
        ;

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

        $this->assertSame($result, $filteredReader->getValues());
    }

    public function test_find_items(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new RowReader(new CsvParser($file, ','));

        $filteredReader = $reader
            ->getColumns('first_name', 'last_name')
            ->find(['first_name' => 'Fifi'])
        ;

        $result = [
            149 => [
                'first_name' => 'Fifi',
                'last_name' => 'Rapier',
            ],
        ];

        $this->assertSame($result, $filteredReader->getValues());
    }

    public function test_filter_items(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new RowReader(new CsvParser($file, ','));

        $filteredReader = $reader
            ->getColumns('first_name', 'last_name')
            ->filter(function ($row) {
                return $row['last_name'] === 'Rapier';
            })
        ;

        $result = [
            149 => [
                'first_name' => 'Fifi',
                'last_name' => 'Rapier',
            ],
        ];

        $this->assertSame($result, $filteredReader->getValues());
    }
}