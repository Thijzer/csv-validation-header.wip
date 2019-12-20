<?php

namespace Tests\Misery\Component\Component\Csv\Validator;

use Misery\Component\Csv\Reader\RowReader;
use Misery\Component\Csv\Validator\ReferencedColumnValidator;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Validator\ValidationCollector;
use PHPUnit\Framework\TestCase;

class ReferencedColumnValidatorTest extends TestCase
{
    // join(code, catalog_brand, X)
    public function test_it_should_validate_referenced_columns(): void
    {
        $exampleFile = __DIR__ . '/../../../examples/example_no_format_row.csv';

        $parser = CsvParser::create($exampleFile);
        $collector = new ValidationCollector();
        $validator = new ReferencedColumnValidator($collector, new RowReader($parser));

        $validator->validate('puma', ['column' => 'brand']);

        $this->assertFalse($collector->hasConstraints());
    }

    public function test_it_should_invalidate_referenced_columns(): void
    {
        $exampleFile = __DIR__ . '/../../../examples/example_no_format_row.csv';

        $parser = CsvParser::create($exampleFile);
        $collector = new ValidationCollector();
        $validator = new ReferencedColumnValidator($collector, new RowReader($parser));

        $validator->validate('reebok', ['column' => 'brand']);

        $this->assertTrue($collector->hasConstraints());
    }
}
