<?php

namespace Tests\Misery\Component\Common\Repository;

use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Reader\RowReader;
use PHPUnit\Framework\TestCase;

class FileRepositoryTest extends TestCase
{
//    public function test_find_by(): void
//    {
//        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
//        $reader = new CsvReader(new CsvParser($file, ','));
//
//        $data = $reader->findOneBy(['first_name' => 'Gordie']);
//
//        $this->assertSame($data, $reader->getRow(30));
//    }
//
//    public function test_find_by_with_index(): void
//    {
//        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
//        $reader = new CsvReader(new CsvParser($file, ','));
//
//        $reader->indexColumns('first_name');
//
//        $data = $reader->findOneBy(['first_name' => 'Gordie']);
//
//        $this->assertSame($data, $reader->getRow(30));
//    }
    public function test_find_by(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvReader(new CsvParser($file, ','));

        $data = $reader->findOneBy(['first_name' => 'Gordie']);

        $this->assertSame($data, $reader->getRow(30));
    }

    public function test_find_by_with_index(): void
    {
        $file = new \SplFileObject(__DIR__ . '/../../../examples/users.csv');
        $reader = new CsvReader(new CsvParser($file, ','));

        $reader->indexColumns('first_name');

        $data = $reader->findOneBy(['first_name' => 'Gordie']);

        $this->assertSame($data, $reader->getRow(30));
    }
}