<?php

namespace Misery\Component\Converter;

use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Item\Builder\KeyValuePairBuilder;
use Misery\Component\Source\SourceCollectionAwareInterface;
use Misery\Component\Source\SourceCollectionTrait;

class AkeneoCsvToStructuredDataConverter implements ConverterInterface, RegisteredByNameInterface, SourceCollectionAwareInterface
{
    use SourceCollectionTrait;

    private $codes;
    private $csvHeaderContext;

    public function __construct(AkeneoCsvHeaderContext $csvHeaderContext)
    {
        $this->csvHeaderContext = $csvHeaderContext;
    }

    public function convert(array $item): array
    {
        $separator = '-';
        $output = [];

        foreach ($item as $key => $value) {

            $keys = explode($separator, $key);

            if (false === in_array($keys[0], array_keys($this->getCodes()))) {
                continue;
            }

            if (strpos($key, '-unit') !== false) {
                unset($item[$key]);
                continue;
            }

            # values
            $prep = $this->csvHeaderContext->create($item)[$key];
            $prep['data'] = $value;

            # metric exception
            if ($this->getCodes()[$keys[0]] === 'pim_catalog_metric') {
                $prep['unit'] = $item[str_replace($keys[0], $keys[0].'-unit', $key)] ?? null;
            }

            $output['values'][$keys[0]][] = $prep;
            unset($item[$key]);
        }

        return $item+$output;
    }

    public function revert(array $item): array
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

    public function getCodes(): array
    {
        if (null === $this->codes) {
            $this->codes = KeyValuePairBuilder::build(
                $this->getSource('attribute')->getReader(),
                'code',
                'type'
            );
        }

        return $this->codes;
    }

    public function getName(): string
    {
        return 'akeneo/product/csv';
    }
}