<?php

namespace Misery\Component\Converter;

use Misery\Component\Akeneo\Header\AkeneoHeader;

class AS400ArticleAttributesHeaderContext
{
    public function create(array $attributes, array $localizableCodes, array $locales): AkeneoHeader
    {
        $header = new AkeneoHeader([
            'locale_codes' => $locales,
            'currencies' => ['EUR'],
        ]);
        $header->add('sku', 'pim_catalog_identifier');

        foreach ($attributes as $attributeCode => $attributeType) {
            $header->add($attributeCode, $attributeType);
            if (in_array($attributeCode, $localizableCodes)) {
                $header->add($attributeCode, $attributeType, ['has_locale' => true]);
            }
        }

        return $header;
    }
}