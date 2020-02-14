<?php

namespace Misery\Component\Encoder;

use Misery\Component\Common\Format\ArrayFormat;
use Misery\Component\Common\Format\StringFormat;
use Misery\Component\Common\Modifier\CellModifier;
use Misery\Component\Common\Modifier\RowModifier;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegistryInterface;

class ItemEncoder
{
    private $registryCollection;

    public function addRegistry(RegistryInterface $registry)
    {
        $this->registryCollection[$registry->getAlias()] = $registry;
    }

    public function encode(array $data, array $context = []): array
    {
        // preparation
        $context = $this->parseContext($context);

        foreach ($context as $header => $namedMatches) {
            foreach ($namedMatches as $property => $matches) {
                foreach ($matches as $match) {
                    $this->processMatch($data, $property, $match);
                }
            }
        }

        return $data;
    }

    public function parseContext(array $context): array
    {
        $rules = [];
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

    private function processMatch(array &$row, string $property, array $match): void
    {
        $class = $match['class'];

        if ($class instanceof OptionsInterface && !empty($match['options'])) {
            $class->setOptions($match['options']);
        }

        switch (true) {
            case $class instanceof CellModifier:
                $row[$property] = $class->modify($row[$property]);
                break;
            case $class instanceof StringFormat:
                $row[$property] = $class->format($row[$property]);
                break;
            case $class instanceof RowModifier:
                $row = $class->modify($row);
                break;
            case $class instanceof ArrayFormat:
                $row = $class->format($row);
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
}