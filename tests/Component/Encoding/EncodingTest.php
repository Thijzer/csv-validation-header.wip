<?php

namespace Tests\Misery\Component\Encoding;

use Misery\Component\Common\Registry\Registry;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Encoder\ItemEncoderFactory;
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

        $encoderFactory = new ItemEncoderFactory();

        $formatRegistry = new Registry('format');
        $formatRegistry
            ->register(StringToListFormat::NAME, new StringToListFormat())
            ->register(StringToIntFormat::NAME, new StringToIntFormat())
        ;

        $encoderFactory->addRegistry($formatRegistry);

        $encoder = $encoderFactory->createItemEncoder([
            'encode' => [
                'id' => [
                    'integer' => []
                ],
                'codes' => [
                    'list' => []
                ],
            ]
        ]);

        $encodedItem = $encoder->encode($this->items[0]);

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

        $encoderFactory = new ItemEncoderFactory();

        $modifierRegistry = new Registry('modifier');
        $modifierRegistry
            ->register(NullifyEmptyStringModifier::NAME, new NullifyEmptyStringModifier())
        ;

        $encoderFactory->addRegistry($modifierRegistry);

        $encoder = $encoderFactory->createItemEncoder([
            'parse' => [
                'nullify' => [],
            ]
        ]);

        $encodedItem = $encoder->encode($this->items[2]);

        $this->assertSame($encodedItem, [
            'id' => '3',
            'first_name' => 'Dolly',
            'codes' => null,
        ]);
    }
}