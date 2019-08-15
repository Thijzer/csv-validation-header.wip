<?php

namespace Tests\Component\Format;

use Component\Format\SnakeCaseFormat;
use PHPUnit\Framework\TestCase;

class SnakeCaseFormatTest extends TestCase
{
    function test_it_should_snake_case_a_value(): void
    {
        $format = new SnakeCaseFormat();

        $this->assertEquals('my_new_value', $format->format('myNewValue'));
        $this->assertEquals('my_new_value', $format->format('MyNewValue'));

        $this->assertEquals('my_new_value_and_his_new_value', $format->format('myNewValue   And His New Value'));

        $this->assertEquals('@my_new_value_and_his_new_value1', $format->format('@myNewValue And His New Value1'));
    }
}