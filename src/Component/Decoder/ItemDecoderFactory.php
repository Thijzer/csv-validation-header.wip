<?php

namespace Misery\Component\Decoder;


use Misery\Component\Common\Registry\RegistryInterface;

class ItemDecoderFactory
{
    private $registryCollection;

    public function addRegistry(RegistryInterface $registry)
    {
        $this->registryCollection[$registry->getAlias()] = $registry;

        return $this;
    }

    public function createItemDecoder(array $configuration)
    {
        return new ItemDecoder(
            $this->prepRulesFromConfiguration($configuration)
        );
    }

    public function prepRulesFromConfiguration(array $configuration): array
    {
        $rules = [];

        foreach ($configuration['encode'] ?? [] as $property => $formatters) {
            foreach ($formatters as $formatName => $formatOptions) {
                if ($class = $this->getFormatClass($formatName)) {
                    $rules['property'][$property][$formatName] = [
                        'class' => $class,
                        'options' => $formatOptions,
                    ];
                }
            }
        }

        foreach ($configuration['parse'] ?? [] as $modifierName => $modifierOptions) {
            if ($class = $this->getModifierClass($modifierName)) {
                $rules['item'][$modifierName][] = [
                    'class' => $class,
                    'options' => $modifierOptions,
                ];
            }
        }

        return $rules;
    }

    private function getModifierClass(string $formatName)
    {
        return $this->registryCollection['modifier']->filterByAlias($formatName);
    }

    private function getFormatClass(string $formatName)
    {
        return $this->registryCollection['format']->filterByAlias($formatName);
    }
}