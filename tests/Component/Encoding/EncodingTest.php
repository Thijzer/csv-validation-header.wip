<?php

namespace Tests\Misery\Component\Encoding;

use Misery\Component\Common\Registry\Registry;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Format\StringToIntFormat;
use Misery\Component\Format\StringToListFormat;
use Misery\Component\Modifier\NullifyEmptyStringModifier;
use Misery\Component\Reader\ItemCollection;
use PHPUnit\Framework\TestCase;

class EncodingTest extends TestCase
{
    private $items = [
        [
            'id' => '1',
            'first_name' => 'Gordie',
            'codes' => 'A,B,C,D',
        ],
        [
            'id' => '2',
            'first_name' => 'Frans',
            'codes' => 'E,F,G',
        ],
        [
            'id' => '3',
            'first_name' => 'Dolly',
            'codes' => '',
        ],
    ];

    public function test_encode_item(): void
    {
        $setA = new ItemCollection($this->items);
        $setB = clone $setA;
        $setB->set(1, [
            'id' => '2',
            'codes' => 'E,F,G,H',
        ]);

        $encoder = new ItemEncoder();

        $formatRegistry = new Registry('format');
        $formatRegistry
            ->register(StringToListFormat::NAME, new StringToListFormat())
            ->register(StringToIntFormat::NAME, new StringToIntFormat())
        ;

        $encoder->addRegistry($formatRegistry);

        $encodedItem = $encoder->encode($this->items[0], [
            'columns' => [
                'id' => [
                    'integer' => []
                ],
                'codes' => [
                    'list' => []
                ],
            ]
        ]);

        $this->assertSame($encodedItem, [
            'id' => 1,
            'first_name' => 'Gordie',
            'codes' => ['A','B','C','D'],
        ]);
    }

    public function test_encode_item_values(): void
    {
        $setA = new ItemCollection($this->items);
        $setB = clone $setA;
        $setB->set(1, [
            'id' => '2',
            'codes' => 'E,F,G,H',
        ]);

        $encoder = new ItemEncoder();

        $modifierRegistry = new Registry('modifier');
        $modifierRegistry
            ->register(NullifyEmptyStringModifier::NAME, new NullifyEmptyStringModifier())
        ;

        $encoder->addRegistry($modifierRegistry);

        $encodedItem = $encoder->encode($this->items[2], [
            'rows' => [
                'nullify' => [],
            ]
        ]);

        $this->assertSame($encodedItem, [
            'id' => '3',
            'first_name' => 'Dolly',
            'codes' => null,
        ]);
    }
}