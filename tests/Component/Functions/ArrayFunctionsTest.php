<?php

namespace Tests\Misery\Component\Functions;

use Misery\Component\Common\Functions\ArrayFunctions;
use PHPUnit\Framework\TestCase;

class ArrayFunctionsTest extends TestCase
{
    private $item = [
        'id' => '1',
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

    public function test_impossible_values(): void
    {
        $item = [
            'id' => '1',
            'id.first_name' => 'Simon',
        ];

        $result = ArrayFunctions::unflatten($item);

        $expected = [
            'id' => [
                '' => '1',
                'first_name' => 'Simon',
            ],
        ];

        $this->assertSame($expected, $result);

        $result = ArrayFunctions::flatten($expected);

        $this->assertSame($item, $result);
    }

//    public function test_performance(): void
//    {
//        $file = new \SplFileObject(__DIR__ . '/../../examples/users.csv');
//        $reader = new ItemReader(new CachedCursor(new CsvParser($file, ',')));
//
//        // aprox 300.000 lines test
//        $tracker = new TimeTracker();
//        foreach (range(1, 1000) as $i) {
//            foreach ($reader->getIterator() as $item) {
//                $this->assertSame($item, ArrayFunctions::flatten(ArrayFunctions::unflatten($item)));
//            }
//        }
//        $this->assertLessThan($check = $tracker->check(), 4);
//
//        print_r($check);
//    }
}