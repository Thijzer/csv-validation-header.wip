<?php

namespace Tests\Misery\Component\Modifier;

use Misery\Component\Modifier\NullifyEmptyStringModifier;
use PHPUnit\Framework\TestCase;

class NullifyEmptyStringModifierTest extends TestCase
{
    public function test_it_should_nullify_empty_string(): void
    {
        $modifier = new NullifyEmptyStringModifier();

        $this->assertNull($modifier->modify(''));
        $this->assertSame($modifier->modify('AB'), 'AB');
    }
}