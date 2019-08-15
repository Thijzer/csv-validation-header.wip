<?php

namespace Tests\Component\Format;

use Component\Format\BoolFormat;
use PHPUnit\Framework\TestCase;

class BooleanFormatTest extends TestCase
{
    public function test_it_should_boolean_type_a_value(): void
    {
        $format = new BoolFormat();

        $boolType = explode('/', 'YES/NO');

        $this->assertTrue($format->format('YES', ...$boolType));
        $this->assertFalse($format->format('NO', ...$boolType));
        $this->assertNull($format->format('JA', ...$boolType));
    }
}