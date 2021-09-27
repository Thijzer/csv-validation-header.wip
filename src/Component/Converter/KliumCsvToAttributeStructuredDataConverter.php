<?php

namespace Misery\Component\Converter;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Source\SourceTrait;

class KliumCsvToAttributeStructuredDataConverter implements ConverterInterface, RegisteredByNameInterface, OptionsInterface
{
    use OptionsTrait;
    use SourceTrait;

    private $csvHeaderContext;
    private $connectionTracker = [];

    public function convert(array $item): array
    {
        $output = [];
        foreach ($item['Attribuut'] ?? [] as $itemSet) {
            if ($itemSet['ID'] && !isset($this->connectionTracker[$itemSet['ID']])) {
                $output[$itemSet['ID']] = [
                    'code' => 'klium_'.$itemSet['ID'],
                    'label-nl_BE' => $itemSet['Label'],
                ];
            }
            $this->connectionTracker[$itemSet['ID']] = $itemSet['ID'];
        }

        dump($output);exit;

        return $output;
    }

    public function revert(array $item): array
    {
        $output = [];
        foreach ($item['values'] as $value) {
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

    public function getName(): string
    {
        return 'klium/product/to/attribute/csv';
    }
}