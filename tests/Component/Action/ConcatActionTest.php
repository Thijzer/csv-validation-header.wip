<?php

namespace Component\Action;

use Misery\Component\Action\ConcatAction;
use PHPUnit\Framework\TestCase;

class ConcatActionTest extends TestCase
{
    public function testApplyWithKeyExists()
    {
        $item = ['weight' => null, 'amount' => 1, 'unit' => 'GRAM'];

        $action = new ConcatAction();
        $action->setOptions([
            'key' => 'weight',
            'format' => '%amount% %unit%'
        ]);

        $result = $action->apply($item);

        $this->assertEquals([
            'weight' => '1 GRAM',
            'amount' => 1,
            'unit' => 'GRAM',
        ], $result);
    }

    public function testApplyWithKeyMissing()
    {
        $item = ['other_key' => 'value'];

        $action = new ConcatAction();
        $action->setOptions([
            'key' => 'weight',
        ]);

        $result = $action->apply($item);

        $this->assertEquals(['other_key' => 'value'], $result);
    }
}
