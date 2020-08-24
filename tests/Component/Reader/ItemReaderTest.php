<?php

namespace Tests\Misery\Component\Reader;

use Misery\Component\Filter\ColumnReduces;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Reader\ItemReader;
use PHPUnit\Framework\TestCase;

class ItemReaderTest extends TestCase
{
    private $items = [
        [
            'id' => '1',
            'first_name' => 'Gordie',
            'last_name' => 'Ramsey',
            'phone' => '5784467',

        ],
        [
            'id' => "2",
            'first_name' => 'Frans',
            'last_name' => 'Merkel',
            'phone' => '123456',
        ],
        [
            'id' => "3",
            'first_name' => 'Mieke',
            'last_name' => 'Cauter',
            'phone' => '1234556356',
        ],
    ];

    public function test_parse_a_column(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $filteredReader = ColumnReduces::reduce($reader, 'first_name');

        $expected = [
            [
                'first_name' => 'Gordie',
            ],
            [
                'first_name' => 'Frans',
            ],
            [
                'first_name' => 'Mieke',
            ],
        ];

        $this->assertSame($expected, $filteredReader->getItems());
    }

    public function test_parse_columns(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $filteredReader = ColumnReduces::reduce($reader, 'first_name', 'last_name');

        $this->assertSame(
            array_keys(current($filteredReader->getItems())), ['first_name', 'last_name']
        );
    }

    public function test_parse_a_row(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $filteredReader = $reader->index([1]);

        $expected = [
            1 => [
                'id' => "2",
                'first_name' => 'Frans',
                'last_name' => 'Merkel',
                'phone' => '123456',
            ]
        ];
        $this->assertSame($filteredReader->getItems(), $expected);
    }

    public function test_parse_rows(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $filteredReader = $reader->index([1, 2]);

        $this->assertSame(count($filteredReader->getItems()), 2);
    }

    public function test_mix_parse_rows_and_columns(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $reader = $reader
            ->index([0, 1])
        ;
        $filteredReader = ColumnReduces::reduce($reader, 'first_name', 'last_name');

        $result = [
            0 => [
                'first_name' => 'Gordie',
                'last_name' => 'Ramsey',
            ],
            1 => [
                'first_name' => 'Frans',
                'last_name' => 'Merkel',
            ],
        ];

        $this->assertSame($result, $filteredReader->getItems());
    }

    public function test_find_items(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $reader = $reader
            ->find(['first_name' => 'Frans'])
        ;
        $filteredReader = ColumnReduces::reduce($reader, 'first_name', 'last_name');

        $result = [
            1 => [
                'first_name' => 'Frans',
                'last_name' => 'Merkel',
            ],
        ];

        $this->assertSame($result, $filteredReader->getItems());
    }

    public function test_filter_items(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $reader = $reader
            ->filter(function ($row) {
                return $row['first_name'] === 'Frans';
            })
        ;

        $result = [
            1 => [
                'id' => "2",
                'first_name' => 'Frans',
                'last_name' => 'Merkel',
                'phone' => '123456',
            ],
        ];

        $this->assertSame($result, $reader->getItems());
    }

    public function test_map_items(): void
    {
        $reader = new ItemReader($items = new ItemCollection([$this->items[0]]));

        $reader = $reader
            ->map(function ($row) {
                unset($row['phone']);
                return $row;
            })
        ;

        $result = [
            0 => [
                'id' => '1',
                'first_name' => 'Gordie',
                'last_name' => 'Ramsey',
            ],
        ];

        $this->assertSame($result, $reader->getItems());
    }
}