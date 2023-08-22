<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\SkipAction;
use Misery\Component\Common\Pipeline\Exception\SkipPipeLineException;
use PHPUnit\Framework\TestCase;

class SkipActionTest extends TestCase
{
    public function testSkipEmptyField()
    {
        $item = ['field' => ''];

        $action = new SkipAction();
        $action->setOptions([
            'field' => 'field',
            'state' => 'EMPTY',
            'skip_message' => 'Field is empty'
        ]);

        $this->expectException(SkipPipeLineException::class);
        $this->expectExceptionMessage('Field is empty');
        $action->apply($item);
    }

    public function testSkipMatchingState()
    {
        $item = ['field' => 'SKIP'];

        $action = new SkipAction();
        $action->setOptions([
            'field' => 'field',
            'state' => 'SKIP',
            'skip_message' => 'Field matches state'
        ]);

        $this->expectException(SkipPipeLineException::class);
        $this->expectExceptionMessage('Field matches state');
        $action->apply($item);
    }

    public function testNoSkip()
    {
        $item = ['field' => 'value'];

        $action = new SkipAction();
        $action->setOptions([
            'field' => 'field',
            'state' => 'EMPTY',
            'skip_message' => 'Field is empty'
        ]);

        $result = $action->apply($item);

        $this->assertEquals($item, $result);
    }

    public function testSkipWithForceSkip()
    {
        $item = ['field' => ''];

        $action = new SkipAction();
        $action->setOptions([
            'field' => 'field',
            'state' => 'EMPTY',
            'skip_message' => 'Field is empty',
            'force_skip' => true
        ]);

        $this->expectException(SkipPipeLineException::class);
        $this->expectExceptionMessage('Field is empty');
        $action->apply($item);
    }
}
