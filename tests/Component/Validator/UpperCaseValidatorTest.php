<?php

namespace Tests\Misery\Component\Validator;

use Misery\Component\Validator\ValidationCollector;
use Misery\Component\Validator\UpperCaseValidator;
use PHPUnit\Framework\TestCase;

class UpperCaseValidatorTest extends TestCase
{
    public function test_it_should_invalidate_a_none_upper_case_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new UpperCaseValidator($collector);

        $validator->validate($value = 'uppercase');

        $this->assertTrue($collector->hasConstraints());
    }

    public function test_it_should_validate_a_none_upper_case_value(): void
    {
        $collector = new ValidationCollector();
        $validator = new UpperCaseValidator($collector);

        $validator->validate('UPPERCASE');

        $this->assertFalse($collector->hasConstraints());
    }
}