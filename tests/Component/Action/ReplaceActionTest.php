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
            ]
        ]
    ];

    public function test_it_should_do_a_replace_action(): void
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
}