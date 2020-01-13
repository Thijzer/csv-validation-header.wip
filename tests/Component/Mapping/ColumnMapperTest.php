<?php

namespace Component\Mapping;

use Akeneo\Tool\Component\Connector\Exception\DataArrayConversionException;
use Misery\Component\Mapping\ColumnMapper;
use PHPUnit\Framework\TestCase;

class ColumnMapperTest extends TestCase
{
    public function test_it_should_map_column_names(): void
    {
        $mapper = new ColumnMapper();

        $mappings = [
            'Code' => 'code',
            'Wassen' => 'wash'
        ];

        $item = [
            'Code' => '1',
            'Wassen' => 'B'
        ];

        $this->assertSame($mapper->mapColumns($item, $mappings), ['code'=> '1', 'wash' => 'B']);
    }

    public function test_it_should_thrown_exception_if_column_names_are_wrong_mapped(): void
    {
        $this->expectException(DataArrayConversionException::class);

        $mapper = new ColumnMapper();

        $mappings = [
            'Code' => 'code',
            'Wassen' => 'wash',
            'Temp' => 'temperature'
        ];

        $item = [
            'code' => '1',
            'Wassen' => 'B'
        ];


        $mapper->mapColumns($item, $mappings);
    }
}
