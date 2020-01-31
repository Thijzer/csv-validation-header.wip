<?php

namespace Tests\Misery\Component\Csv\Compare;

use Misery\Component\Csv\Compare\ItemCompare;
use Misery\Component\Csv\Reader\ItemCollection;
use Misery\Component\Csv\Reader\ItemReader;
use PHPUnit\Framework\TestCase;

class CsvCompareTest extends TestCase
{
    private $items = [
        [
            'id' => '1',
            'first_name' => 'Gordie',
        ],
        [
            'id' => '2',
            'first_name' => 'Frans',
        ],
    ];

    public function test_parse_csv_file(): void
    {
        $setA = new ItemCollection($this->items);
        $setB = clone $setA;
        $setB->set(1, [
            'id' => '2',
            'first_name' => 'Fransken',
        ]);

        $tool = new ItemCompare(
            new ItemReader($setA),
            new ItemReader($setB)
        );

        $result = $tool->compare('id');
    }
}