<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\RetainAction;
use PHPUnit\Framework\TestCase;

class RetainActionTest extends TestCase
{
    public function test_it_should_retain_action_with_keys(): void
    {
        $format = new RetainAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'keys' => ['brand', 'description'],
        ]);

        $this->assertEquals([
            'brand' => 'louis',
            'description' => 'LV',
        ], $format->apply($item));
    }

    public function test_it_should_not_retain_action_with_keys(): void
    {
        $format = new RetainAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'keys' => [],
        ]);

        $this->assertEquals([
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }


    public function test_it_should_do_a_retain_action_with_bad_keys(): void
    {
        $format = new RetainAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
            0 => [false],
        ];

        $format->setOptions([
            'keys' => ['brand', 'description', 0, false, true, '', -1, 'the-unknown'],
        ]);

        $this->assertEquals([
            'brand' => 'louis',
            'description' => 'LV',
            0 => [false],
        ], $format->apply($item));
    }
}