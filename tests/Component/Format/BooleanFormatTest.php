<?php

namespace Tests\Misery\Component\Format;

use Misery\Component\Format\BooleanFormat;
use PHPUnit\Framework\TestCase;

class BooleanFormatTest extends TestCase
{
    public function test_it_should_boolean_type_a_value(): void
    {
        $format = new BooleanFormat();

        $format->setOptions([
            'true' => 'YES',
            'false' => 'NO',
        ]);

        $this->assertTrue($format->format('YES'));
        $this->assertFalse($format->format('NO'));
        $this->assertNull($format->format('JA'));
    }
}