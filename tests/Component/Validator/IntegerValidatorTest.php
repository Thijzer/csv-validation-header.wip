<?php

namespace Tests\Misery\Component\Validator;

use Misery\Component\Validator\IntegerValidator;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class IntegerValidatorTest extends TestCase
{
    public function test_it_should_invalidate_a_none_integer_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new IntegerValidator($collector);

        $validator->validate($value = 'jennyFromTheBlock');

        $this->assertTrue($collector->hasConstraints());

        $collector->getErrors();

        $validator->validate($value = '0.1');

        $this->assertTrue($collector->hasConstraints());
    }

    public function test_it_should_validate_a_none_integer_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new IntegerValidator($collector);

        $validator->validate('1');

        $this->assertFalse($collector->hasConstraints());
    }
}