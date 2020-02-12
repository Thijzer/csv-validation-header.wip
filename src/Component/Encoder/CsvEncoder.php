<?php

namespace Misery\Component\Encoder;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegistryInterface;

class CsvEncoder
{
    public const FORMAT = 'csv';

    private $formatRegistry;
    private $modifierRegistry;

    public function __construct(RegistryInterface $formatRegistry, RegistryInterface $modifierRegistry)
    {
        $this->modifierRegistry = $modifierRegistry;
        $this->formatRegistry = $formatRegistry;
    }

    public function encode($data, array $context = []): array
    {
        // preparation
        $context = $this->parseContext($context);

        foreach ($context as $header => $namedMatches) {
            foreach ($namedMatches as $columnName => $matches) {
                foreach ($matches as $match) {
                    $this->processMatch($data, $columnName, $match);
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
                        'type' => 'format',
                        'options' => $formatOptions,
                    ];
                }
            }
        }

        foreach ($context['rows'] ?? [] as $modifierName => $modifierOptions) {
            if ($class = $this->getModifierClass($modifierName)) {
                $rules['format'][$modifierName][] = [
                    'type' => 'modifier',
                    'class' => $class,
                    'options' => $modifierOptions,
                ];
            }
        }

        return $rules;
    }

    private function processMatch(array &$row, string $header, array $match): void
    {
        $class = $match['class'];
//        $type = $match['type'];

        if ($class instanceof OptionsInterface && !empty($match['options'])) {
            $class->setOptions($match['options']);
        }

//        if ($type === Registry::NAME) {
//            $row[$header] = $class->modify($row[$header]);
//        }

        $row[$header] = $class->format($row[$header]);
    }

    private function getModifierClass(string $formatName)
    {
        return $this->modifierRegistry->filterByAlias($formatName);
    }

    private function getFormatClass(string $formatName)
    {
        return $this->formatRegistry->filterByAlias($formatName);
    }

    public function supports($format): bool
    {
        return self::FORMAT === $format;
    }
}