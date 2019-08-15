<?php

namespace Tests\Component\Format;

use Component\Format\ListFormat;
use PHPUnit\Framework\TestCase;

class ListFormatTest extends TestCase
{
    public function test_it_should_list_type_a_value(): void
    {
        $format = new ListFormat();

        $this->assertSame($format->format(',', 'a,b,c'), ['a', 'b', 'c']);
    }
}