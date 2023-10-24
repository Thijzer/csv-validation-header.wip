<?php
declare(strict_types=1);

namespace Tests\Misery\Component\Converter\Akeneo\Csv;

use Misery\Component\Converter\Akeneo\Csv\Product;
use Misery\Component\Converter\AkeneoCsvHeaderContext;
use Misery\Component\Converter\Matcher;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private array $inputData = [
        'sku' => 'SKU123',
        'enabled' => '1',
        'family' => 'Family1',
        'categories' => 'Category1,Category2',
        'parent' => 'ParentProduct',
        'attribute1' => 'Value1',
        'attribute2' => 'Value2',
        'attribute3' => '10',
        'attribute3-unit' => 'KILOGRAM',
        'attribute4' => 'option_a,option_b',
    ];

    private function getNormalizedData(): array
    {
        return [
            'sku' => 'SKU123',
            'enabled' => true,
            'family' => 'Family1',
            'categories' => ['Category1','Category2'],
            'parent' => 'ParentProduct',
            'values|attribute1' => [
                'matcher' => Matcher::create('values|attribute1'),
                'locale' => null,
                'scope' => null,
                'data' => 'Value1',
            ],
            'values|attribute2' => [
                'matcher' => Matcher::create('values|attribute2'),
                'locale' => null,
                'scope' => null,
                'data' => 'Value2',
            ],
            'values|attribute3' => [
                'matcher' => Matcher::create('values|attribute3'),
                'locale' => null,
                'scope' => null,
                'data' => [
                    'amount' => '10',
                    'unit' => 'KILOGRAM',
                ],
            ],
            'values|attribute4' => [
                'matcher' => Matcher::create('values|attribute4'),
                'locale' => null,
                'scope' => null,
                'data' => ['option_a','option_b'],
            ],
        ];
    }

    public function testConvert()
    {
        $csvHeaderContext = new AkeneoCsvHeaderContext();
        $converter = new Product($csvHeaderContext);
        $converter->setOptions([
            'attribute_types:list' => [
                'attribute1' => 'pim_catalog_text',
                'attribute2' => 'pim_catalog_text',
                'attribute3' => 'pim_catalog_metric',
                'attribute4' => 'pim_catalog_multiselect',
            ],
        ]);

        $this->assertEquals($this->getNormalizedData(), $converter->convert($this->inputData));
    }

    public function testRevert()
    {
        $csvHeaderContext = new AkeneoCsvHeaderContext();
        $converter = new Product($csvHeaderContext);

        $this->assertEquals($this->inputData, $converter->revert($this->getNormalizedData()));
    }

    public function testName()
    {
        $csvHeaderContext = new AkeneoCsvHeaderContext();
        $converter = new Product($csvHeaderContext);

        $this->assertEquals('akeneo/product/csv', $converter->getName());
    }
}
