<?php

namespace Misery\Component\Converter\Akeneo\Csv;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Converter\AkeneoCsvHeaderContext;
use Misery\Component\Converter\ConverterInterface;
use Misery\Component\Converter\Matcher;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Format\StringToBooleanFormat;
use Misery\Component\Format\StringToListFormat;

class Product implements ConverterInterface, RegisteredByNameInterface, OptionsInterface
{
    use OptionsTrait;

    private AkeneoCsvHeaderContext $csvHeaderContext;
    private ItemEncoder $encoder;

    private $options = [
        'attribute_types:list' => null,
        'properties' => [
            'sku' => [
                'text' => null,
            ],
            'enabled' => [
                'boolean' => null,
            ],
            'family' => [
                'text' => null,
            ],
            'categories'=> [
                'list' => null,
            ],
            'parent' => [
                'text' => null,
            ],
        ],
        'parse' => [],
    ];

    public function __construct(AkeneoCsvHeaderContext $csvHeaderContext)
    {
        $this->csvHeaderContext = $csvHeaderContext;
        $this->encoder = $this->ItemEncoderFactory()->createItemEncoder([
            'encode' => $this->getOption('properties'),
            'parse' => $this->getOption('parse'),
        ]);
    }

    private function ItemEncoderFactory(): ItemEncoderFactory
    {
        $encoderFactory = new ItemEncoderFactory();

        $formatRegistry = new Registry('format');
        $formatRegistry
            ->register(StringToListFormat::NAME, new StringToListFormat())
            ->register(StringToBooleanFormat::NAME, new StringToBooleanFormat())
        ;

        $encoderFactory->addRegistry($formatRegistry);

        return $encoderFactory;
    }

    public function convert(array $item): array
    {
        $codes = $this->getOption('attribute_types:list');
        $keyCodes = is_array($codes) ? array_keys($codes): null;
        $separator = '-';
        $output = [];

        $item = $this->encoder->encode($item);

        foreach ($item as $key => $value) {
            $keys = explode($separator, $key);
            $masterKey = $keys[0];

            if (in_array($masterKey, array_keys($this->getOption('properties')))) {
                continue;
            }

            if ($keyCodes && false === in_array($masterKey, $keyCodes)) {
                continue;
            }

            if (str_ends_with($key, '-unit') !== false) {
                unset($item[$key]);
                continue;
            }

            # values
            $prep = $this->csvHeaderContext->create($item)[$key];
            $prep['data'] = $value;

            # metrics
            if ($codes[$masterKey] === 'pim_catalog_metric') {
                $prep['data'] = [
                    'amount' => $value,
                    'unit' => $item[str_replace($masterKey, $masterKey.'-unit', $key)] ?? null,
                ];
            }
            # multiselect
            if ($codes[$masterKey] === 'pim_catalog_multiselect') {
                $prep['data'] = array_filter(explode(',', $prep['data']));
            }

            $matcher = Matcher::create('values|'.$masterKey, $prep['locale'], $prep['scope']);
            unset($prep['key']); // old way of storing the original key
            $output[$k = $matcher->getMainKey()] = $prep;
            $output[$k]['matcher'] = $matcher;

            unset($item[$key]);
        }

        return $item+$output;
    }

    public function revert(array $item): array
    {
        return $item;
    }

    public function getName(): string
    {
        return 'akeneo/product/csv';
    }
}