<?php

namespace Tests\Component\Format;

use Component\Filter\NullEmptyStringFilter;
use PHPUnit\Framework\TestCase;

class NullEmptyStringFilterTest extends TestCase
{
    public function test_it_should_nullify_empty_string(): void
    {
        $filter = new NullEmptyStringFilter();

        $this->assertNull($filter->filter(''));
        $this->assertSame($filter->filter('AB'), 'AB');
    }
}