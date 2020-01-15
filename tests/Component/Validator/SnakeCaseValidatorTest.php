<?php

namespace Tests\Misery\Component\Validator;

use Misery\Component\Validator\SnakeCaseValidator;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class SnakeCaseValidatorTest extends TestCase
{
    public function test_it_should_invalidate_a_none_snake_case_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new SnakeCaseValidator($collector);

        $validator->validate($value = 'jennyFromTheBlock');

        $this->assertTrue($collector->hasConstraints());
    }

    public function test_it_should_validate_a_none_snake_case_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new SnakeCaseValidator($collector);

        $validator->validate('jenny_from_the_block');

        $this->assertFalse($collector->hasConstraints());
    }
}