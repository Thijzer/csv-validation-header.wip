<?php

namespace Misery\Component\Converter;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;

class AkeneoProductApiConverter implements ConverterInterface, RegisteredByNameInterface, OptionsInterface
{
    use OptionsTrait;

    private $options = [];

    public function convert(array $item): array
    {
        $tmp = [];
        // first we need to convert the values
        foreach ($item['values'] ?? [] as $key => $valueSet) {
            foreach ($valueSet ?? [] as  $value) {
                $matcher = Matcher::create('values|'.$key, $value['locale'], $value['scope']);
                $tmp[$keyMain = $matcher->getMainKey()] = $value;
                $tmp[$keyMain]['matcher'] = $matcher;
            }
        }
        unset($item['values']);

        return $item+$tmp;
    }

    public function revert(array $item): array
    {
        foreach ($item ?? [] as $key => $itemValue) {
            $matcher = $itemValue['matcher'] ?? null;
            if ($matcher && $matcher->matches('values')) {
                unset($itemValue['matcher']);
                unset($item[$key]);
                $item['values'][$matcher->getPrimaryKey()][] = $itemValue;
            }
        }

        return $item;
    }

    public function getName(): string
    {
        return 'akeneo/product/api';
    }
}