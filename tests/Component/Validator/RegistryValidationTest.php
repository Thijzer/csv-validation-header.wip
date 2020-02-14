<?php

namespace Tests\Misery\Component\Validator;

use Misery\Component\Common\Processor\ItemValidationProcessor;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Encoder\Validator\ReferencedColumnValidator;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Encoder\Validator\UniqueValueValidator;
use Misery\Component\Validator\InArrayValidator;
use Misery\Component\Validator\IntegerValidator;
use Misery\Component\Validator\RequiredValidator;
use Misery\Component\Validator\SnakeCaseValidator;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class RegistryValidationTest extends TestCase
{
    private $items = [
        [
            'id' => '1',
            'code' => 'a',
            'status' => 'REMOVED',
            'first_name' => 'Gordie',
            'last_name' => 'Ramsey',
            'phone' => '5784467',
        ],
        [
            'id' => "2",
            'code' => 'b',
            'status' => 'PUBLISHED',
            'first_name' => 'Frans',
            'last_name' => 'Merkel',
            'phone' => '123456',
        ],
        [
            'id' => "1",
            'code' => 'C',
            'status' => 'CREATED',
            'first_name' => 'Mieke',
            'last_name' => 'Cauter',
            'phone' => '',
        ],
    ];

    private function createRegistry(ValidationCollector $collector): Registry
    {
        $registry = new Registry('validations');
        $registry
            ->register(ReferencedColumnValidator::NAME, new ReferencedColumnValidator($collector))
            ->register(UniqueValueValidator::NAME, new UniqueValueValidator($collector))
            ->register(RequiredValidator::NAME, new RequiredValidator($collector))
            ->register(InArrayValidator::NAME, new InArrayValidator($collector))
            ->register(IntegerValidator::NAME, new IntegerValidator($collector))
            ->register(SnakeCaseValidator::NAME, new SnakeCaseValidator($collector))
        ;

        return $registry;
    }

    public function test_it_should_invalidate_from_registry(): void
    {
        $reader = new ItemReader($items = new ItemCollection($this->items));

        $collector = new ValidationCollector();

        $processor = new ItemValidationProcessor();
        $processor->setRegistry($this->createRegistry($collector));

        $validations = [
            'resource' => 'users',
            'validations' => [
                'property' => [
                    'id' => [
                        'unique' => true,
                        'is_integer' => true,
                        'required' => true,
                    ],
                    'code' => [
                        'unique' => true,
                        'snake_case' => true,
                        'required' => true,
                    ],
                    'status' => [
                        'in_array' => [
                            'options' => ['CREATED', 'PUBLISHED', 'UPDATED'],
                        ],
                    ],
                    'phone' => [
                        'required' => true,
                    ],
                ]
            ]
        ];

        $processor->process($reader, $validations);

        $this->assertTrue($collector->hasConstraints());

        $expectedErrors = [
            "The value REMOVED is not listed as an option {\"property\":\"status\",\"value\":\"REMOVED\"}",
            "Invalid format found for snake_case value : C {\"property\":\"code\",\"value\":\"C\"}",
            "Value cannot be blank {\"property\":\"phone\",\"value\":\"\"}",
        ];

        $this->assertSame($expectedErrors, $collector->getErrors());
    }
}
