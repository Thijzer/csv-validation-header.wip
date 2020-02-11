<?php

namespace Tests\Misery\Component\Csv\Reader;

use Misery\Component\Parser\CsvParser;
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
}