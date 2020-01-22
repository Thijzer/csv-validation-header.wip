<?php

namespace Tests\Misery\Component\Functions;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Functions\ArrayFunctions;
use PHPUnit\Framework\TestCase;

class ArrayFunctionsTest extends TestCase
{
    private $item = [
        'id' => "1",
        'user' => ['first_name' => 'Simon'],
    ];

    public function test_function_flatten_values(): void
    {
        $result = ArrayFunctions::flatten($this->item);

        $expected = [
            'id' => '1',
            'user.first_name' => 'Simon',
        ];

        $this->assertSame($expected, $result);
    }

    public function test_function_unflatten_values(): void
    {
        $item = [
            'id' => '1',
            'user.first_name' => 'Simon',
        ];

        $result = ArrayFunctions::unflatten($item);

        $this->assertSame($this->item, $result);
    }
}