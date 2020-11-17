<?php

namespace Tests\Misery\Component\Encoding;

use Misery\Component\Action\ReplaceAction;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Common\Repository\ItemRepository;
use Misery\Component\Decoder\ItemDecoderFactory;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Format\StringToIntFormat;
use Misery\Component\Format\StringToListFormat;
use Misery\Component\Modifier\ArrayUnflattenModifier;
use Misery\Component\Modifier\NullifyEmptyStringModifier;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Reader\ItemReader;
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

    public function test_hard_akeneo_examples_test()
    {
        $category = [
            [
                'code' => 'AB',
                'label' => [
                    'nl_BE' => 'AB-nlBE',
                    'fr_BE' => 'AB-frBE',
                ],
            ],
            [
                'code' => 'CD',
                'label' => [
                    'nl_BE' => 'CD-nlBE',
                    'fr_BE' => 'CD-frBE',
                ],
            ],
            [
                'code' => 'EF',
                'label' => [
                    'nl_BE' => 'EF-nlBE',
                    'fr_BE' => 'EF-frBE',
                ],
            ],
            [
                'code' => 'GH',
                'label' => [
                    'nl_BE' => 'GH-nlBE',
                    'fr_BE' => 'GH-frBE',
                ],
            ],
            [
                'code' => 'IJ',
                'label' => [
                    'nl_BE' => 'IJ-nlBE',
                    'fr_BE' => 'IJ-frBE',
                ],
            ],
        ];

        $item = ['sku' => '8604597', 'categories' => 'AB,CD,EF,GH,IJ', 'enabled' => '1', 'family' => 'para'];

        $encoderFactory = new ItemEncoderFactory();

        $modifierRegistry = new Registry('modifier');
        $modifierRegistry
            ->register(ArrayUnflattenModifier::NAME, new ArrayUnflattenModifier())
        ;
        $encoderFactory->addRegistry($modifierRegistry);

        $formatRegistry = new Registry('format');
        $formatRegistry
            ->register(StringToListFormat::NAME, new StringToListFormat())
        ;
        $encoderFactory->addRegistry($formatRegistry);

        $encoder = $encoderFactory->createItemEncoder([
            'encode' => [
                'categories' => [
                    'list' => [],
                ],
            ],
            'parse' => [
                'unflatten' => [],
            ]
        ]);

        $encodedItem = $encoder->encode($item);

        $reader = new ItemReader(new ItemCollection($category));
        $format = new ReplaceAction();
        $format->setReader($reader);

        $format->setOptions([
            'method' => 'getLabelsFromList',
            'locales' => ['nl_BE', 'fr_BE'],
            'key' => 'categories',
        ]);

        $convertedItem = $format->apply($encodedItem);

        $decoderFactory = new ItemDecoderFactory();
        $decoderFactory->addRegistry($formatRegistry);
        $decoderFactory->addRegistry($modifierRegistry);

        $decoder = $decoderFactory->createItemDecoder([
            'encode' => [
                'categories' => [
                    'list' => [],
                ],
            ],
            'parse' => [
                'unflatten' => [
                    'separator' => '-',
                ],
            ]
        ]);

        $this->assertSame(
            [
                "sku" => "8604597",
                "categories-nl_BE" => "AB-nlBE,CD-nlBE,EF-nlBE,GH-nlBE,IJ-nlBE",
                "categories-fr_BE" => "AB-frBE,CD-frBE,EF-frBE,GH-frBE,IJ-frBE",
                "enabled" => "1",
                "family" => "para",
            ],
            $decoder->decode($convertedItem)
        );
    }
}