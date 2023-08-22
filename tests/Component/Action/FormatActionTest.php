<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\FormatAction;
use PHPUnit\Framework\TestCase;

class FormatActionTest extends TestCase
{
    public function testApplyReplaceFunction()
    {
        $item = ['field' => 'abc123'];

        $action = new FormatAction();
        $action->setOptions([
            'field' => 'field',
            'functions' => ['replace'],
            'search' => '123',
            'replace' => '456'
        ]);

        $result = $action->apply($item);

        $this->assertEquals(['field' => 'abc456'], $result);
    }

    public function testApplyNumberFunction()
    {
        $item = ['field' => 12345.6789];

        $action = new FormatAction();
        $action->setOptions([
            'field' => 'field',
            'functions' => ['number'],
            'decimals' => 2,
            'decimal_sep' => ',',
            'mille_sep' => '.'
        ]);

        $result = $action->apply($item);

        $this->assertEquals(['field' => '12.345,68'], $result);
    }
}
