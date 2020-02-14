<?php

namespace Misery\Component\Common\Processor;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Reader\ReaderInterface;
use Misery\Component\Validator\ValidatorInterface;

class ItemValidationProcessor
{
    private $registry;
    private $readers;

    public function setRegistry(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function getRegistry(): RegistryInterface
    {
        return $this->registry;
    }

    private function getValidationClass(string $alias)
    {
        return $this->getRegistry()->filterByAlias($alias);
    }

    private function parseContext(array $context): array
    {
        $rules = [];
        foreach ($context['validations']['property'] ?? [] as $property => $converters) {
            foreach ($converters as $converterName => $converterOptions) {
                $registry = $this->getRegistry();
                if ($class = $registry->filterByAlias($converterName)) {
                    $rules[$property][$converterName] = [
                        'class' => $class,
                        'options' => $converterOptions,
                    ];
                }
            }
        }

        return $rules;
    }

    public function process($data, array $context = []): void
    {
        if ($data instanceof ReaderInterface) {
            $this->processReader($data, $context);
            return;
        }

        if (is_array($data)) {
            $this->processItem($data, $context);
        }
    }

    private function processReader(ReaderInterface $reader, array $context)
    {
        // register the reader with a name
        $this->readers['current'] = $reader;
        $this->readers[$context['resource']] = $reader;

        // process reader
        // process item
        while ($item = $reader->read()) {
            $this->processItem($item, $context);
        }
    }

    private function processItem(array $data, $context)
    {
        // preparation
        $context = $this->parseContext($context);

        foreach ($context as $property => $namedMatches) {
            foreach ($namedMatches as $columnName => $matches) {
                $this->processMatch($data, $property, $matches);
            }
        }
    }

    private function processMatch(array &$data, string $property, $match)
    {
        /** @var ValidatorInterface $class */
        $class = $match['class'];

        if ($class instanceof OptionsInterface && !empty($match['options'])) {
            $class->setOptions($match['options']);
        }

        if ($class instanceof ItemReaderAwareInterface) {
          //  $class->setReader($this->readers['current']);
            return;
        }

        $class->validate($data[$property], [
            'property' => $property,
            'value' => $data[$property],
        ]);
    }
}