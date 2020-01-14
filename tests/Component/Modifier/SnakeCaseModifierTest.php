<?php

namespace Tests\Misery\Component\Modifier;

use Misery\Component\Modifier\SnakeCaseModifier;
use PHPUnit\Framework\TestCase;

class SnakeCaseModifierTest extends TestCase
{
    function test_it_should_snake_case_a_value(): void
    {
        $modifier = new SnakeCaseModifier();

        $this->assertEquals('my_new_value', $modifier->modify('myNewValue'));
        $this->assertEquals('my_new_value', $modifier->modify('MyNewValue'));

        $this->assertEquals('my_new_value_and_his_new_value', $modifier->modify('myNewValue   And His New Value'));

        $this->assertEquals('@my_new_value_and_his_new_value1', $modifier->modify('@myNewValue And His New Value1'));
    }
}