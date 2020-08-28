<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\CopyAction;
use Misery\Component\Action\RenameAction;
use Misery\Component\Action\ReplaceAction;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Reader\ItemReader;
use PHPUnit\Framework\TestCase;

class ReplaceActionTest extends TestCase
{
    private $brands = [
        [
            'code' => '_nike_',
            'label' => [
                'nl_BE' => 'Nike',
                'fr_BE' => 'Nikell',
            ],
        ],
        [
            'code' => '_adi_',
            'label' => [
                'nl_BE' => 'Adidas',
                'fr_BE' => 'Adieu',
            ],
        ]
    ];

    public function test_it_should_do_a_replace_a_label_action(): void
    {
        $reader = new ItemReader(new ItemCollection($this->brands));

        $format = new ReplaceAction();
        $format->setReader($reader);

        $item = [
            'brand' => '_nike_',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'method' => 'getLabel',
           # 'source' => 'brands',
            'locale' => 'nl_BE',
            'key' => 'brand'
        ]);

        $this->assertEquals([
            'brand' => 'Nike',
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }

    public function test_it_should_do_a_replace_labels_action(): void
    {
        $reader = new ItemReader(new ItemCollection($this->brands));

        $format = new ReplaceAction();
        $format->setReader($reader);

        $item = [
            'brand' => '_nike_',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'method' => 'getLabels',
            'locales' => ['nl_BE', 'fr_BE'],
            'key' => 'brand'
        ]);

        $this->assertEquals([
            'brand' => ['nl_BE' => 'Nike', 'fr_BE' => 'Nikell'],
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }

    public function test_it_should_do_a_replace_labels_in_list_action(): void
    {
        $reader = new ItemReader(new ItemCollection($this->brands));

        $format = new ReplaceAction();
        $format->setReader($reader);

        $item = [
            'brand' => ['_nike_','_adi_'],
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'method' => 'getLabelsFromList',
            'locales' => ['nl_BE', 'fr_BE'],
            'key' => 'brand'
        ]);

        $this->assertEquals([
            'brand' => [
                'nl_BE' => ['Nike', 'Adidas'],
                'fr_BE' => ['Nikell', 'Adieu']
            ],
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }

    public function test_it_should_do_a_replace_label_in_list_action(): void
    {
        $reader = new ItemReader(new ItemCollection($this->brands));

        $format = new ReplaceAction();
        $format->setReader($reader);

        $item = [
            'brand' => ['_nike_','_adi_'],
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'method' => 'getLabelFromList',
            'locale' => 'nl_BE',
            'key' => 'brand'
        ]);

        $this->assertEquals([
            'brand' => ['Nike', 'Adidas'],
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }
}