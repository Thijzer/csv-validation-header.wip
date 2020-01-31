<?php

namespace Tests\Misery\Component\Csv\Validator;

use Misery\Component\Csv\Reader\ItemReader;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Validator\UniqueValueValidator;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class UniqueValueValidatorTest extends TestCase
{
    public function test_it_should_validate_referenced_columns(): void
    {
        $exampleFile = __DIR__ . '/../../../examples/example_no_format_row.csv';

        $parser = CsvParser::create($exampleFile);
        $collector = new ValidationCollector();
        $validator = new UniqueValueValidator($collector);
        $validator->setReader(new ItemReader($parser));
        $validator->validate('code');

        $this->assertFalse($collector->hasConstraints());
    }

    public function test_it_should_invalidate_referenced_columns(): void
    {
        $exampleFile = __DIR__ . '/../../../examples/example_no_format_row.csv';

        $parser = CsvParser::create($exampleFile);
        $collector = new ValidationCollector();
        $validator = new UniqueValueValidator($collector);
        $validator->setReader($reader = new ItemReader($parser));
        $validator->validate('id');

        $this->assertTrue($collector->hasConstraints());
    }
}
