<?php

namespace Misery\Component\Converter;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;

class AkeneoProductApiConverter implements ConverterInterface, RegisteredByNameInterface, OptionsInterface
{
    use OptionsTrait;

    private $options = [
        'structure' => 'matcher', # matcher OR flat
        'container' => 'values',
    ];

    public function convert(array $item): array
    {
        $container = $this->getOption('container');
        if (false === isset($item[$container])) {
            return $item;
        }

        $tmp = [];
        // first we need to convert the values
        foreach ($item[$container] ?? [] as $key => $valueSet) {
            foreach ($valueSet ?? [] as  $value) {
                $matcher = Matcher::create($container.'|'.$key, $value['locale'], $value['scope']);
                $tmp[$keyMain = $matcher->getMainKey()] = $value['data'] ?? null;
                if ($this->getOption('structure') === 'matcher') {
                    $tmp[$keyMain] = $value;
                    $tmp[$keyMain]['matcher'] = $matcher;
                }
            }
        }

        unset($item[$container]);

        return $item+$tmp;
    }

    public function revert(array $item): array
    {
        $container = $this->getOption('container');

        foreach ($item ?? [] as $key => $itemValue) {
            $matcher = $itemValue['matcher'] ?? null;
            if ($matcher && $matcher->matches($container)) {
                unset($itemValue['matcher']);
                unset($item[$key]);
                $item[$container][$matcher->getPrimaryKey()][] = $itemValue;
            }
        }

        return $item;
    }

    public function getName(): string
    {
        return 'akeneo/product/api';
    }
}