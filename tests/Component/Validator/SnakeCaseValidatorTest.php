<?php

namespace Tests\Component\Validator;

use Component\Validator\Constraint\SnakeCaseConstraint;
use Component\Validator\SnakeCaseValidator;
use Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class SnakeCaseValidatorTest extends TestCase
{
    public function test_it_should_invalidate_a_none_snake_case_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new SnakeCaseValidator($collector);

        $validator->validate('jennyFromTheBlock');

        $this->assertTrue($collector->hasConstraints());
        $this->assertSame($collector->getMessages(), [SnakeCaseConstraint::INVALID_FORMAT]);
    }

    public function test_it_should_validate_a_none_snake_case_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new SnakeCaseValidator($collector);

        $validator->validate('jenny_from_the_block');

        $this->assertFalse($collector->hasConstraints());
    }
}