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
            'Hi Temp' => 'hi_temp',
        ];

        $data = [
            'Code' => '1',
            'Wassen' => 'B',
            'not_mapped' => 'C',
        ];

        $expected = [
            'code'=> '1',
            'wash' => 'B',
            'not_mapped' => 'C',
        ];

        $this->assertSame($expected, $mapper->map($data, $mappings));
    }

    public function test_it_should_thrown_exception_if_column_names_are_wrong_mapped(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $mapper = new ColumnMapper();

        $mappings = [
            'Temp' => 'temperature'
        ];

        $data = [
            'code' => '1',
            'Wassen' => 'B'
        ];

        $mapper->map($data, $mappings);
    }
}
