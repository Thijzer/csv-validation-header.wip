<?php

namespace Tests\Misery\Component\Validator;

use Misery\Component\Reader\ItemCollection;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Validator\UniqueValueValidator;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class UniqueValueValidatorTest extends TestCase
{
    private $items = [
        [
            'id' => '1',
            'code' => 'A',
            'first_name' => 'Gordie',
            'last_name' => 'Ramsey',
            'phone' => '5784467',

        ],
        [
            'id' => "2",
            'code' => 'B',
            'first_name' => 'Frans',
            'last_name' => 'Merkel',
            'phone' => '123456',
        ],
        [
            'id' => "1",
            'code' => 'C',
            'first_name' => 'Mieke',
            'last_name' => 'Cauter',
            'phone' => '1234556356',
        ],
    ];

    public function test_it_should_validate_referenced_columns(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $collector = new ValidationCollector();
        $validator = new UniqueValueValidator($collector);
        $validator->setReader($reader);
        $validator->validate('code');

        $this->assertFalse($collector->hasConstraints());
    }

    public function test_it_should_invalidate_referenced_columns(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $collector = new ValidationCollector();
        $validator = new UniqueValueValidator($collector);
        $validator->setReader($reader);
        $validator->validate('id');

        $this->assertTrue($collector->hasConstraints());
    }
}
