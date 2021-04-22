<?php

namespace Tests\Misery\Component\Compare;

use Misery\Component\Compare\ItemCompare;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Reader\ItemReader;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
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
            $setA,
            $setB
        );

        $result = $tool->compare('id');

        $this->assertCount(1, $result[ItemCompare::CHANGED]);
    }
}