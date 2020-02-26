<?php

namespace Misery\Component\Encoder;

use Misery\Component\Common\Format\ArrayFormat;
use Misery\Component\Common\Format\StringFormat;
use Misery\Component\Common\Modifier\CellModifier;
use Misery\Component\Common\Modifier\RowModifier;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Reader\ReaderInterface;
use Misery\Component\Validator\ValidatorInterface;

class ItemEncoder
{
    private $registryCollection;

    public function addRegistry(RegistryInterface $registry)
    {
        $this->registryCollection[$registry->getAlias()] = $registry;
    }

    public function encode(array $item, array $context = []): array
    {
        // preparation
        $context = $this->parseContext($context);

        $this->processValidations($item, $context);

        $item = $this->processEncoding($item, $context);

        return $item;
    }

    private function processEncoding(array $item, array $context): array
    {
        foreach ($context['encode'] as $header => $namedMatches) {
            foreach ($namedMatches as $property => $matches) {
                foreach ($matches as $match) {
                    $this->processMatch($item, $property, $match, $context['name']);
                }
            }
        }

        return $item;
    }

    private function processValidations(array $data, $context)
    {
        // process validations
        foreach ($context['validations'] as $property => $namedMatches) {
            foreach ($namedMatches as $columnName => $matches) {
                $this->processMatch($data, $property, $matches, $context['name']);
            }
        }
    }

    public function parseContext(array $context): array
    {
        $rules = [];
        $rules['name'] = $context['name'] ?? null;

        foreach ($context['columns'] ?? [] as $columnName => $formatters) {
            foreach ($formatters as $formatName => $formatOptions) {
                if ($class = $this->getFormatClass($formatName)) {
                    $rules['format'][$columnName][$formatName] = [
                        'class' => $class,
                        'options' => $formatOptions,
                    ];
                }
            }
        }

        foreach ($context['validations']['property'] ?? [] as $property => $converters) {
            foreach ($converters as $converterName => $converterOptions) {
                if ($class = $this->getValidationClass($converterName)) {
                    $rules['validations'][$property][$converterName] = [
                        'class' => $class,
                        'options' => $converterOptions,
                    ];
                }
            }
        }

        foreach ($context['rows'] ?? [] as $modifierName => $modifierOptions) {
            if ($class = $this->getModifierClass($modifierName)) {
                $rules['format'][$modifierName][] = [
                    'class' => $class,
                    'options' => $modifierOptions,
                ];
            }
        }

        return $rules;
    }

    private function processMatch(array &$item, string $property, array $match, string $name = null): void
    {
        $class = $match['class'];

        if ($class instanceof OptionsInterface && !empty($match['options'])) {
            $class->setOptions($match['options']);
        }

//        if ($class instanceof ItemReaderAwareInterface) {
//            //  $class->setReader($this->readers['current']);
//            return;
//        }

        switch (true) {
            case $class instanceof CellModifier:
                $item[$property] = $class->modify($item[$property]);
                break;
            case $class instanceof StringFormat:
                $item[$property] = $class->format($item[$property]);
                break;
            case $class instanceof RowModifier:
                $item = $class->modify($item);
                break;
            case $class instanceof ArrayFormat:
                $item = $class->format($item);
            case $class instanceof ValidatorInterface:
                $class->validate($item[$property], array_filter([
                    'property' => $property,
                    'value' => $item[$property],
                    //'name' => $name, source_name
                ]));
                break;
        }
    }

    private function getModifierClass(string $formatName)
    {
        return $this->registryCollection['modifier']->filterByAlias($formatName);
    }

    private function getFormatClass(string $formatName)
    {
        return $this->registryCollection['format']->filterByAlias($formatName);
    }

    private function getValidationClass(string $alias)
    {
        return $this->registryCollection['validations']->filterByAlias($alias);
    }
}