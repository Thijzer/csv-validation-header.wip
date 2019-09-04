<?php

namespace Misery\Component\Common\Processor;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\FormatRegistry;
use Misery\Component\Common\Registry\ModifierRegistry;
use Misery\Component\Common\Registry\Registry;

class CsvDataProcessor implements CsvDataProcessorInterface
{
    private $registries;
    private $processableHeaders;
    private $rowModifiers;

    public function __construct()
    {
        $this->registries = new ArrayCollection();
        $this->processableHeaders = [];
        $this->rowModifiers = [];
    }

    public function addRegistry(Registry $registry): self
    {
        $this->registries->add($registry);

        return $this;
    }

    public function filterSubjects(array $subjects): void
    {
        $headers = [];
        foreach ($subjects['columns'] ?? [] as $header => $converters) {
            foreach ($converters as $converterName => $converterOptions) {
                $this->registries->map(function (Registry $registry) use ($converterName, $converterOptions, $header, &$headers) {
                    /** @var ArrayCollection $items */
                    $items = $registry->filterByName($converterName);
                    if ($items->hasValues()) {
                        $headers[$header][$converterName][] = [
                            'type' => $registry::NAME,
                            'class' => $items->first(),
                            'options' => $converterOptions,
                        ];
                    }
                });
            }
        }

        $rows = [];
        foreach ($subjects['rows'] ?? [] as $converterName => $converterOptions) {
            $this->registries->map(function (Registry $registry) use ($converterName, $converterOptions, &$rows) {
                /** @var ArrayCollection $items */
                $items = $registry->filterByName($converterName);
                if ($items->hasValues()) {
                    $rows[$converterName][] = [
                        'type' => $registry::NAME,
                        'class' => $items->first(),
                        'options' => $converterOptions,
                    ];
                }
            });
        }
        $this->rowModifiers = $rows;

        // multi dimensional merge
        $this->processableHeaders = $headers;
    }

    private function processMatch(array $match, array &$row, string $header): void
    {
        $class = $match['class'];
        $type = $match['type'];

        if ($class instanceof OptionsInterface && !empty($match['options'])) {
            $class->setOptions($match['options']);
        }

        if ($type === ModifierRegistry::NAME) {
            $row[$header] = $class->modify($row[$header]);
        }

        if ($type === FormatRegistry::NAME) {
            $row[$header] = $class->format($row[$header]);
        }
    }

    public function processRow(array $row): array
    {
        foreach ($this->processableHeaders as $header => $namedMatches) {
            foreach ($namedMatches as $matches) {
                foreach ($matches as $match) {
                    $this->processMatch($match, $row, $header);
                }
            }
        }

        foreach ($this->rowModifiers as $matches) {
            foreach ($matches as $match) {
                $class = $match['class'];
                $type = $match['type'];

                if ($class instanceof OptionsInterface && !empty($match['options'])) {
                    $class->setOptions($match['options']);
                }

                if ($type === ModifierRegistry::NAME) {
                    $row = $class->modify($row);
                }
            }
        }

        return $row;
    }
}