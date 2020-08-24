<?php

namespace Tests\Misery\Component\Compare;

use Misery\Component\Common\Cursor\FunctionalCursor;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Compare\ItemCompare;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Format\StringToIntFormat;
use Misery\Component\Format\StringToListFormat;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Reader\ItemReader;
use PHPUnit\Framework\TestCase;

class CompareWithEncodingTest extends TestCase
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

    public function test_encode_and_compare(): void
    {
        // SETUP
        $encoderFactory = new ItemEncoderFactory();

        $formatRegistry = new Registry('format');
        $formatRegistry
            ->register(StringToListFormat::NAME, new StringToListFormat())
            ->register(StringToIntFormat::NAME, new StringToIntFormat())
        ;
        $encoderFactory->addRegistry($formatRegistry);

        $context = [
            'encode' => [
                'codes' => [
                    'list' => [],
                ],
            ],
        ];

        $collectionA = new ItemCollection($this->items);
        $collectionB = clone $collectionA;
        $collectionB->set(1, [
            'id' => '2',
            'first_name' => 'Frans',
            'codes' => 'E,F,G,Z',
        ]);

        $encoder = $encoderFactory->createItemEncoder($context);

        $setA = new FunctionalCursor($collectionA, (function($item) use ($encoder) {
            return $encoder->encode($item);
        }));
        $setB = new FunctionalCursor($collectionB, (function($item) use ($encoder) {
            return $encoder->encode($item);
        }));

        $tool = new ItemCompare(
            $readerA = new ItemReader($setA),
            $readerB = new ItemReader($setB)
        );

        $result = $tool->compare('id');

        $changedValues = current($result[ItemCompare::CHANGED])['changes'][ItemCompare::ADDED]['codes'];

        $this->assertSame([3 => 'Z'], $changedValues);
        $this->assertCount(1, $result[ItemCompare::CHANGED]);
    }
}