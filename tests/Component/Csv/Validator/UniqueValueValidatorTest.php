<?php

namespace Tests\Misery\Component\Component\Csv\Validator;

use Misery\Component\Csv\Reader\CsvReader;
use Misery\Component\Csv\Validator\Constraint\UniqueValueConstraint;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Validator\UniqueValueValidator;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class UniqueValueValidatorTest extends TestCase
{
    // join(code, catalog_brand, X)
    public function test_it_should_validate_referenced_columns(): void
    {
        $exampleFile = __DIR__ . '/../../../examples/example_no_format_row.csv';

        $parser = CsvParser::create($exampleFile);
        $collector = new ValidationCollector();
        $validator = new UniqueValueValidator($collector, new CsvReader($parser));

        $validator->validate('code');

        $this->assertFalse($collector->hasConstraints());
    }

    public function test_it_should_invalidate_referenced_columns(): void
    {
        $exampleFile = __DIR__ . '/../../../examples/example_no_format_row.csv';

        $parser = CsvParser::create($exampleFile);
        $collector = new ValidationCollector();
        $validator = new UniqueValueValidator($collector, new CsvReader($parser));

        $validator->validate('id');

        $this->assertTrue($collector->hasConstraints());
        $this->assertSame($collector->getMessages(), [UniqueValueConstraint::UNIQUE_VALUE]);
    }
}
