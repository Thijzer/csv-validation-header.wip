<?php

namespace Tests\Misery\Component\Reader;

use Misery\Component\Parser\CsvParser;
use PHPUnit\Framework\TestCase;

class CsvParserTest extends TestCase
{
    public function test_parse_csv_file(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../examples/users.csv');
        $reader = new CsvParser($file, ';');

        $this->assertTrue($file->isFile());
        $this->assertSame($reader->count(), 300);

        $count = 0;
        while ($reader->current()) {
            $count++;
            $reader->next();
        }

        $this->assertSame($count, 300);
    }

    public function test_parse_csv_with_iterator(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../examples/users.csv');
        $reader = new CsvParser($file, ';');

        $this->assertTrue($file->isFile());
        $this->assertSame($reader->count(), 300);

        $count = 0;
        foreach ($reader->getIterator() as $item) {
            $count++;
        }

        $this->assertSame($count, 300);
    }

    public function test_parse_csv_with_loop(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../examples/users.csv');
        $reader = new CsvParser($file, ';');

        $this->assertTrue($file->isFile());
        $this->assertSame($reader->count(), 300);

        $count = 0;
        $reader->loop(function ($row) use (&$count) {
            $count++;
        });

        $this->assertSame($count, 300);
    }
}