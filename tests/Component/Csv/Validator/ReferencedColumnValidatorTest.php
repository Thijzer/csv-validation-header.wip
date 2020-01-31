<?php

namespace Tests\Misery\Component\Csv\Validator;

use Misery\Component\Csv\Reader\ItemCollection;
use Misery\Component\Csv\Reader\ItemReader;
use Misery\Component\Csv\Validator\ReferencedColumnValidator;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class ReferencedColumnValidatorTest extends TestCase
{
    private $items = [
        [
            'brand' => 'Nike',
        ],
        [
            'brand' => 'Puma',
        ]
    ];

    public function test_it_should_validate_referenced_columns(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $collector = new ValidationCollector();
        $validator = new ReferencedColumnValidator($collector);
        $validator->setReader($reader);
        $validator->setOptions([
            'reference' => 'brand',
        ]);

        $validator->validate('Puma');

        $this->assertFalse($collector->hasConstraints());
    }

    public function test_it_should_invalidate_referenced_columns(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $collector = new ValidationCollector();
        $validator = new ReferencedColumnValidator($collector);
        $validator->setReader($reader);
        $validator->setOptions([
            'reference' => 'brand',
        ]);
        $validator->validate('Reebok');

        $this->assertTrue($collector->hasConstraints());
    }
}
