<?php

namespace Misery\Component\Common\Processor;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\ReaderRegistry;
use Misery\Component\Common\Registry\Registry;
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

    public function addRegistry(Registry $registry): self
    {
        $this->registries->set($registry::NAME,$registry);

        return $this;
    }

    public function getRegistry(string $alias): Registry
    {
        return $this->registries->get($alias)->first();
    }

    public function filterSubjects(array $subjects, string $file): void
    {
        $headers = [];
        foreach ($subjects['validations']['property'] ?? [] as $header => $converters) {
            foreach ($converters as $converterName => $converterOptions) {
                $this->registries->map(function (Registry $registry) use ($converterName, $converterOptions, $header, &$headers) {
                    /** @var ArrayCollection $items */
                    $items = $registry->filterByName($converterName);
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
                        $reader = $this->getRegistry(ReaderRegistry::NAME)->filterByName($readerFile)->first();
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
                    $reader = $this->getRegistry(ReaderRegistry::NAME)->filterByName($file)->first();
                    /** @var $reader ReaderInterface */
                    $reader->loop(function ($row) use ($property, $reader, $class, $context) {
                        $context['line'] = $reader->getCursor()->key();
                        $context['column'] = $property;

                        $class->validate($row[$property], $context);
                    });
                }
            }
        }
    }
}