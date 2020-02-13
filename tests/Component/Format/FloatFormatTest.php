<?php

namespace Tests\Misery\Component\Format;

use Misery\Component\Format\FloatToStringFormat;
use PHPUnit\Framework\TestCase;

class FloatFormatTest extends TestCase
{
    public function test_it_should_float_type_a_value(): void
    {
        $format = new FloatToStringFormat();

        $this->assertSame($format->format('0.1'), 0.1);
        $this->assertSame($format->format('10100.5464'), 10100.5464);
    }
}