<?php

namespace Tests\Misery\Component\Component\Csv\Reader;

use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Reader\CsvReader;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    public function test_parse_a_column(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvReader(new CsvParser($file, ','));

        $filteredReader = $reader->getColumn('first_name');
        $data = iterator_to_array($filteredReader->getIterator());

        $this->assertSame(\count($data), 300);
    }

    public function test_parse_columns(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvReader($parser = new CsvParser($file, ','));

        $filteredReader = $reader->getColumns('first_name', 'last_name');
        $data = iterator_to_array($filteredReader->getIterator());

        $this->assertSame(array_keys($data), ['first_name', 'last_name']);
    }

    public function test_parse_a_row(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvReader($parser = new CsvParser($file, ','));

        $filteredReader = $reader->getRow(150);
        $data = iterator_to_array($filteredReader->getIterator());

        $this->assertSame(array_keys($data), $parser->getHeaders());
    }

    public function test_parse_rows(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvReader($parser = new CsvParser($file, ','));

        $filteredReader = $reader->getRows([149, 150]);
        $data = iterator_to_array($filteredReader->getIterator());

        $this->assertSame(count($data), 2);
    }

    public function test_mix_parse_rows_and_columns(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvReader(new CsvParser($file, ','));

        $filteredReader = $reader->getColumns('first_name', 'last_name');
        $filteredReader = $filteredReader->getRows([149, 150]);

        $data = iterator_to_array($filteredReader->getIterator());

        $result = [
            [
                'first_name' => 'A',
                'last_name' => 'B',
            ],
            [
                'first_name' => 'C',
                'last_name' => 'D',
            ],
        ];

        $this->assertSame($result, $data);
    }
}