<?php

namespace Tests\Misery\Component\Converter;

use Misery\Component\Converter\AkeneoCsvStructureConverter;
use PHPUnit\Framework\TestCase;

class AkeneoCsvStructureConverterTest extends TestCase
{
    private $items = [
        [
            'sku' => '13445',
            'code-nl_BE-ecom' => 'value',
            'code-nl_BE-print' => 'value-print-nl',
            'code-fr_BE-print' => 'value-print-fr',
            'description-fr_BE' => 'descr-fr',
            'description-nl_BE' => 'descr-nl',

            'products-RELATED' => 'asso-1',
            'products-IN_STORE' => 'asso-2',
        ],
    ];

    public function test_convert_to_csv_struct_and_back(): void
    {
        $item = $this->items[0];

        $convertedItem = AkeneoCsvStructureConverter::convert($item, ['code', 'description']);
        $revertedItem = AkeneoCsvStructureConverter::revert($convertedItem, ['code', 'description']);

        $this->assertTrue($item == $revertedItem);
    }
}