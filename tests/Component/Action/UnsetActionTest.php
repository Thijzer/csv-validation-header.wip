<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\UnsetAction;
use PHPUnit\Framework\TestCase;

class UnsetActionTest extends TestCase
{
    public function testApplyUnsetKeys()
    {
        $item = ['field' => ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']];

        $action = new UnsetAction();
        $action->setOptions([
            'field' => 'field',
            'list' => ['key1', 'key3']
        ]);

        $result = $action->apply($item);

        $this->assertEquals(['field' => ['key2' => 'value2']], $result);
    }

    public function testApplyWithNonArrayField()
    {
        $item = ['field' => 'value'];

        $action = new UnsetAction();
        $action->setOptions([
            'field' => 'field',
            'list' => ['key1', 'key2']
        ]);

        $result = $action->apply($item);

        $this->assertEquals(['field' => 'value'], $result);
    }
}
