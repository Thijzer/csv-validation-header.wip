<?php

namespace Tests\Misery\Component\Component\Validator;

use Misery\Component\Validator\Constraint\IntegerConstraint;
use Misery\Component\Validator\IntegerValidator;
use Misery\Component\Validator\SnakeCaseValidator;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class IntegerValidatorTest extends TestCase
{
    public function test_it_should_invalidate_a_none_integer_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new IntegerValidator($collector);

        $validator->validate('jennyFromTheBlock');

        $this->assertTrue($collector->hasConstraints());
        $this->assertSame($collector->getErrors(), [IntegerConstraint::INVALID_VALUE]);

        $validator->validate('0.1');

        $this->assertTrue($collector->hasConstraints());
        $this->assertSame($collector->getErrors(), [IntegerConstraint::INVALID_VALUE]);
    }

    public function test_it_should_validate_a_none_integer_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new SnakeCaseValidator($collector);

        $validator->validate('1');

        $this->assertFalse($collector->hasConstraints());
    }
}