<?php

namespace Misery\Component\Decoder;

use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Configurator\ConfigurationManager;
use Misery\Component\Converter\ConverterInterface;

class ItemDecoderFactory implements RegisteredByNameInterface
{
    private $registryCollection;

    public function addRegistry(RegistryInterface $registry): ItemDecoderFactory
    {
        $this->registryCollection[$registry->getAlias()] = $registry;

        return $this;
    }

    public function createFromConfiguration(array $configuration, ConfigurationManager $configurationManager, ConverterInterface $converter = null): ItemDecoder
    {
        // encoder can have a blueprint named reference
        if (isset($configuration['blueprint'])) {
            $bluePrint = $configurationManager->createBlueprint($configuration['blueprint']);
            if ($bluePrint) {
                return $bluePrint->getDecoder();
            }
        }

        return $this->createItemDecoder($configuration, $converter);
    }

    public function createItemDecoder(array $configuration, ConverterInterface $converter = null): ItemDecoder
    {
        return new ItemDecoder(
            $this->parseDirectivesFromConfiguration($configuration),
            $converter
        );
    }

    public function parseDirectivesFromConfiguration(array $configuration): array
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

    private function getConverterClass(string $formatName)
    {
        return $this->registryCollection['converter']->filterByAlias($formatName);
    }

    private function getModifierClass(string $formatName)
    {
        return $this->registryCollection['modifier']->filterByAlias($formatName);
    }

    private function getFormatClass(string $formatName)
    {
        return $this->registryCollection['format']->filterByAlias($formatName);
    }

    public function getName(): string
    {
        return 'decoder';
    }
}