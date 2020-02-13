<?php

namespace Tests\Misery\Component\Format;

use Misery\Component\Format\ListStringFormat;
use PHPUnit\Framework\TestCase;

class ListFormatTest extends TestCase
{
    public function test_it_should_list_type_a_value(): void
    {
        $format = new ListStringFormat();

        $format->setOptions([
            'separator' => ',',
        ]);

        $this->assertSame($format->format('a,b,c'), ['a', 'b', 'c']);
    }
}