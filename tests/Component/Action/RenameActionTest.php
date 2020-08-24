<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\CopyAction;
use Misery\Component\Action\RenameAction;
use PHPUnit\Framework\TestCase;

class RenameActionTest extends TestCase
{
    public function test_it_should_do_a_rename_action(): void
    {
        $format = new RenameAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'from' => 'brand',
            'to' => 'merk',
        ]);

        $this->assertEquals([
            'merk' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }
}