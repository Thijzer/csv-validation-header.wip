<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\FilterAction;
use PHPUnit\Framework\TestCase;

class FilterActionTest extends TestCase
{
    public function test_it_should_not_filter_values_action(): void
    {
        $format = new FilterAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'key' => 'brand',
            'match' => 'lou',
        ]);

        $this->assertEquals(
            $item
        , $format->apply($item));
    }

    public function test_it_should_filter_values_action(): void
    {
        $format = new FilterAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'key' => 'brand',
            'match' => 'chris',
        ]);

        $this->assertEquals(
            [
                'brand' => null,
                'description' => 'LV',
                'sku' => '1',
            ],
            $format->apply($item)
        );
    }

    public function test_it_should_filter_values_in_list_action(): void
    {
        $format = new FilterAction();

        $item = [
            'categories' => ['A-a', 'A-b', 'C-a', 'C-b'],
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'key' => 'categories',
            'match' => 'A-',
        ]);

        $this->assertSame(
            [
                'categories' =>  ['A-a', 'A-b'],
                'description' => 'LV',
                'sku' => '1',
            ],
            $format->apply($item)
        );
    }
}