<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\RemoveAction;
use PHPUnit\Framework\TestCase;

class RemoveActionTest extends TestCase
{
    public function test_it_should_do_a_remove_action(): void
    {
        $format = new RemoveAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'keys' => ['brand', 'description'],
        ]);

        $this->assertEquals([
            'sku' => '1',
        ], $format->apply($item));
    }
}