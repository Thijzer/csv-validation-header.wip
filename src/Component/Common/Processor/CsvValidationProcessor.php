<?php

namespace Misery\Component\Common\Processor;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\ReaderRegistryInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Csv\Reader\ReaderAwareInterface;
use Misery\Component\Csv\Reader\ReaderInterface;
use Misery\Component\Csv\Validator\UniqueValueValidator;
use Misery\Component\Validator\ValidatorInterface;

class CsvValidationProcessor
{
    private $registries;
    private $processableHeaders;

    public function __construct()
    {
        $this->registries = new ArrayCollection();
        $this->processableHeaders = [];
    }

    public function addRegistry(RegistryInterface $registry): self
    {
        $this->registries->set(get_class($registry),$registry);

        return $this;
    }

    public function getRegistry(string $alias): RegistryInterface
    {
        return $this->registries->get($alias)->first();
    }

    public function filterSubjects(array $subjects, string $file): void
    {
        $headers = [];
        foreach ($subjects['validations']['property'] ?? [] as $header => $converters) {
            foreach ($converters as $converterName => $converterOptions) {
                $this->registries->map(function (RegistryInterface $registry) use ($converterName, $converterOptions, $header, &$headers) {
                    /** @var ArrayCollection $items */
                    $items = $registry->filterByAlias($converterName);
                    if ($items->hasValues()) {
                        $headers[$header][$converterName] = [
                            'type' => $registry::NAME,
                            'class' => $items->first(),
                            'options' => $converterOptions,
                        ];
                    }
                });
            }
        }

        // multi dimensional merge
        $this->processableHeaders[$file] = $headers;
    }

    public function processValidation(): void
    {
        foreach ($this->processableHeaders as $file => $headers) {
            foreach ($headers as $property => $matches) {
                foreach ($matches as $match) {
                    /** @var ValidatorInterface $class */
                    $class = $match['class'];
                    $type = $match['type'];

                    $context = [
                        'file' => $file,
                    ];

                    if ($class instanceof ReaderAwareInterface) {
                        $readerFile = $match['options']['file'] ?? $file;
                        $reader = $this->getRegistry('todo_alias')->filterByAlias($readerFile)->first();
                        if (null === $reader) {
                            // @todo this is invalid
                            continue;
                        }
                        $class->setReader($reader);
                    }

                    if ($class instanceof OptionsInterface && !empty($match['options'])) {
                        $class->setOptions($match['options']);
                    }

                    // this is a unique check on the column
                    // it's cheaper than inside the loop
                    if ($class instanceof UniqueValueValidator) {
                        $class->validate($property, $context);
                        continue;
                    }

                    // part of how we validate should be inside the validator
                    // uses 2 readers on for looping another for matching
                    $reader = $this->getRegistry('todo_alias')->filterByAlias($file)->first();
                    /** @var $reader ReaderInterface */
                    $reader->loop(function ($row) use ($property, $reader, $class, $context) {
                        // @TODO row not found should be validated
                        if (isset($row[$property])) {
                            $context['line'] = $reader->getIterator()->key();
                            $context['column'] = $property;

                            $class->validate($row[$property], $context);
                        }
                    });
                }
            }
        }
    }
}