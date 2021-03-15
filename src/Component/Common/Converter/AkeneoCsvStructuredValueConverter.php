<?php

namespace Misery\Component\Common\Converter;

class AkeneoCsvStructuredValueConverter
{
    private $codes;
    private $keyValuePair;
    private $csvHeaderContext;

    public function __construct(AkeneoCsvHeaderContext $csvHeaderContext, array $codes, array $keyValuePair)
    {
        $this->codes = $codes;
        $this->keyValuePair = $keyValuePair;
        $this->csvHeaderContext = $csvHeaderContext;
    }

    public function convert(array $item)
    {
        $separator = '-';
        $output = [];

        # we need to calculate the keys here on the header,
        # then we calculated store result an merge recursively.
        # after that we only need to set our data value or unit

        foreach ($item as $key => $value) {

            $keys = explode($separator, $key);

            if (false === in_array($keys[0], $this->codes)) {
                continue;
            }

            if (strpos( $key, '-unit') !== false) {
                unset($item[$key]);
                continue;
            }

            # values
            $prep = $this->csvHeaderContext->create($item)[$key];
            $prep['data'] = $value;

            if ($this->keyValuePair[$keys[0]] === 'pim_catalog_metric') {
                $prep['unit'] = $item[str_replace($keys[0], $keys[0].'-unit', $key)] ?? null;
            }

            $output['values'][$keys[0]][] = $prep;
            unset($item[$key]);
        }

        return $item+$output;
    }

    public function revert(array $item)
    {
        $output = [];
        foreach ($item['values'] as $key => $value) {
            foreach ($value as $valueItem) {
                $output[$valueItem['key']] = $valueItem['data'];
                if (isset($valueItem['unit'])) {
                    $output[$valueItem['key'].'-unit'] = $valueItem['unit'];
                }
            }
        }

        unset($item['values']);

        return $item+$output;
    }

    public function getName()
    {
        return 'akeneo/product/csv';
    }
}