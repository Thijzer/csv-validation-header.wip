<?php

namespace Tests\Component\Validator;

use Component\Validator\Constraint\IntegerConstraint;
use Component\Validator\IntegerValidator;
use Component\Validator\SnakeCaseValidator;
use Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class IntegerValidatorTest extends TestCase
{
    public function test_it_should_invalidate_a_none_integer_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new IntegerValidator($collector);

        $validator->validate('jennyFromTheBlock');

        $this->assertTrue($collector->hasConstraints());
        $this->assertSame($collector->getMessages(), [IntegerConstraint::INVALID_VALUE]);

        $validator->validate('0.1');

        $this->assertTrue($collector->hasConstraints());
        $this->assertSame($collector->getMessages(), [IntegerConstraint::INVALID_VALUE]);
    }

    public function test_it_should_validate_a_none_integer_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new SnakeCaseValidator($collector);

        $validator->validate('1');

        $this->assertFalse($collector->hasConstraints());
    }
}