<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\SetValueAction;
use PHPUnit\Framework\TestCase;

class SetValueActionTest extends TestCase
{
    public function test_it_should_set_a_value_action(): void
    {
        $format = new SetValueAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
            'enabled' => '0',
        ];

        $format->setOptions([
            'key' => 'enabled',
            'value' => '1',
        ]);

        $this->assertEquals([
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
            'enabled' => '1',
        ], $format->apply($item));
    }

    public function test_it_should_set_a_new_value_action(): void
    {
        $format = new SetValueAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'key' => 'published',
            'value' => '1',
        ]);

        $this->assertEquals([
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
            'published' => '1',
        ], $format->apply($item));
    }
}