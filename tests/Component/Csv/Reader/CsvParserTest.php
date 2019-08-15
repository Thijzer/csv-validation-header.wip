<?php

namespace Tests\Component\Csv\Reader;

use Component\Csv\Reader\CsvParser;
use PHPUnit\Framework\TestCase;

class CsvParserTest extends TestCase
{
    public function test_parse_csv_file(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvParser($file, ',');

        $this->assertTrue($file->isFile());
        $this->assertSame($reader->count(), 300);

        $count = [];
        while ($line = $reader->current()) {
            $count[] = $reader->key();
            $reader->next();
        }

        $this->assertSame(\count($count), 300);
    }

    public function test_parse_a_column(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvParser($file, ',');

        $data = $reader->getColumn('first_name');

        $this->assertSame(\count($data), 300);
    }

    public function test_parse_a_row(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvParser($file, ',');

        $data = $reader->getRow(150);

        $this->assertSame(array_keys($data), $reader->getHeaders());
    }

    public function test_find_by(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvParser($file, ',');

        $data = $reader->findOneBy(['first_name' => 'Gordie']);

        $this->assertSame($data, $reader->getRow(30));
    }

    public function test_find_by_with_index(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvParser($file, ',');

        $reader->indexColumns('first_name');

        $data = $reader->findOneBy(['first_name' => 'Gordie']);

        $this->assertSame($data, $reader->getRow(30));
    }
}