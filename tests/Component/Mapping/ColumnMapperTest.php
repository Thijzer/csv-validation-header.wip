<?php

namespace Tests\Misery\Component\Mapping;

use Misery\Component\Mapping\ColumnMapper;
use PHPUnit\Framework\TestCase;

class ColumnMapperTest extends TestCase
{
    public function test_it_should_map_column_names(): void
    {
        $mapper = new ColumnMapper();

        $mappings = [
            'Code' => 'code',
            'Wassen' => 'wash',
            'fail' => 'fail',
        ];

        $item = [
            'Code' => '1',
            'Wassen' => 'B',
            'not_mapped' => 'C',
        ];

        $result = [
            'code'=> '1',
            'wash' => 'B',
            'not_mapped' => 'C',
        ];

        $this->assertSame($result, $mapper->map($item, $mappings));
    }

    public function test_it_should_thrown_exception_if_column_names_are_wrong_mapped(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $mapper = new ColumnMapper();

        $mappings = [
            'Temp' => 'temperature'
        ];

        $item = [
            'code' => '1',
            'Wassen' => 'B'
        ];

        $mapper->map($item, $mappings);
    }
}
