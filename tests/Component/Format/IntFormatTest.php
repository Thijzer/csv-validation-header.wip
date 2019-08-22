<?php

namespace Tests\Misery\Component\Component\Format;

use Misery\Component\Format\IntFormat;
use PHPUnit\Framework\TestCase;

class IntFormatTest extends TestCase
{
    public function test_it_should_integer_type_a_value(): void
    {
        $format = new IntFormat();

        $this->assertSame($format->format('1'), 1);
        $this->assertSame($format->format('10'), 10);
        $this->assertSame($format->format('124344'), 124344);
        $this->assertSame($format->format('1345'), 1345);
    }
}