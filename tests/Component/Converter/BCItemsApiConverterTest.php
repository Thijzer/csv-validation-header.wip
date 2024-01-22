<?php

namespace Tests\Misery\Component\Converter;

use Misery\Component\Akeneo\Header\AkeneoHeaderTypes;
use Misery\Component\Converter\BCItemsApiConverter;
use Misery\Component\Converter\Matcher;
use PHPUnit\Framework\TestCase;

class BCItemsApiConverterTest extends TestCase
{
    public function testConvert()
    {
        // Define test data
        $converter = new BCItemsApiConverter();
        $converter->setOptions([
            'expand' => ['itemUnitOfMeasuresPieces', 'itemtranslations', 'itemreferences', 'itemCategories'],
            'mappings:list' => [

            ],
            'attributes:list' => [
                'sku', 'itemDescriptionERP', 'unitPrice',
            ],
            'attribute_types:list' => [
                'length' => AkeneoHeaderTypes::METRIC,
                'width' => AkeneoHeaderTypes::METRIC,
                'height' => AkeneoHeaderTypes::METRIC,
                'itemDescriptionERP' => AkeneoHeaderTypes::TEXT,
                'unitPrice' => AkeneoHeaderTypes::TEXT,
            ],
            'localizable_attribute_codes:list' => [
                'itemDescriptionERP'
            ],
            'scopable_attribute_codes:list' => [],
            'default_metrics:list' => ['length' => 'METER', 'width' => 'METER', 'height' => 'METER'],
            'attribute_option_label_codes:list' => [],
            'set_default_metrics' => TRUE,
            'default_locale' => 'en_US',
            'active_locales' => ['en_US', 'nl_BE'],
            'default_scope' => 'ecommerce',
            'default_currency' => 'EUR',
            'option_label' => 'label-nl_BE',
        ]);

        $item = [
            '@odata.etag' => 'etag_value',
            'no' => 'SKU123',
            'unitPrice' => 25.5,
            'tariffNo' => '123456',
            'standard_delivery_time' => 3,
            'itemUnitOfMeasuresPieces' => [
                ['length' => 10, 'width' => 5, 'height' => 2, 'qtyPerUnitOfMeasure' => 1],
            ],
            'itemtranslations' => [
                ['itemDescriptionERP' => 'Product Description', 'typeName' => 'Product Type'],
            ],
            'itemreferences' => [
                ['referenceNo' => 'REF123'],
            ],
            'itemCategories' => [
                ['code' => 'category1'],
                ['code' => 'category2'],
            ],
        ];

        // Perform the conversion
        $result = $converter->convert($item);

        // Define the expected result
        $expectedResult = [
            'sku' => 'SKU123',
            'categories' => ['category1','category2'],
            'values|unitPrice' => [
                'locale' => null,
                'scope' => null,
                'data' =>  25.5,
                'matcher' => Matcher::create('values|unitPrice'),
            ],
            'values|length' => [
                'locale' => null,
                'scope' => null,
                'data' =>  [
                    'amount' => 10,
                     'unit' => 'METER',
                ],
                'matcher' => Matcher::create('values|length'),
            ],
            'values|width' => [
                'locale' => null,
                'scope' => null,
                'data' =>  [
                    'amount' => 5,
                    'unit' => 'METER',
                ],
                'matcher' => Matcher::create('values|width'),
            ],
            'values|height' => [
                'locale' => null,
                'scope' => null,
                'data' =>  [
                    'amount' => 2,
                    'unit' => 'METER',
                ],
                'matcher' => Matcher::create('values|height'),
            ],
            'values|itemDescriptionERP' => [
                'locale' => 'en_US',
                'scope' => null,
                'data' => 'Product Description',
                'matcher' => Matcher::create('values|itemDescriptionERP'),
            ],
        ];

        // Perform the assertions
        $this->assertEquals($expectedResult, $result);
    }
}
