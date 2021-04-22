<?php

namespace Misery\Component\Configurator;

use Misery\Component\Action\ItemActionProcessor;
use Misery\Component\BluePrint\BluePrint;
use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Pipeline\Pipeline;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Converter\ConverterInterface;
use Misery\Component\Decoder\ItemDecoder;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Reader\ItemReaderInterface;
use Misery\Component\Source\SourceCollection;
use Misery\Component\Writer\ItemWriterInterface;

class Configuration
{
    private $pipeline = null;
    private $actions = null;
    private $blueprints;
    private $encoders;
    private $decoders;
    private $reader;
    private $writer;
    private $lists = [];
    private $filters = [];
    private $converters;
    private $sources;

    public function __construct()
    {
        $this->converters = new ArrayCollection();
        $this->encoders = new ArrayCollection();
        $this->decoders = new ArrayCollection();
        $this->blueprints = new ArrayCollection();
    }

    public function addSources(SourceCollection $sources): void
    {
        $this->sources = $sources;
    }

    public function getSources(): SourceCollection
    {
        return $this->sources;
    }

    public function setPipeline(Pipeline $pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    public function getPipeline(): ?Pipeline
    {
        return $this->pipeline;
    }

    public function setActions(ItemActionProcessor $actionProcessor): void
    {
        $this->actions = $actionProcessor;
    }

    public function getActions(): ?ItemActionProcessor
    {
        return $this->actions;
    }

    public function addBlueprint(BluePrint $bluePrint): void
    {
        $this->blueprints->add($bluePrint);
    }

    public function getBlueprint(string $name)
    {
        return $this->blueprints->filter(function (RegisteredByNameInterface $blueprint) use ($name) {
            return $blueprint->getName() === $name;
        })->first();
    }

    public function getBlueprints(): ArrayCollection
    {
        return $this->blueprints;
    }

    public function addBlueprints(ArrayCollection $collection): void
    {
        $this->blueprints->merge($collection);
    }

    public function addLists(array $lists): void
    {
        $this->lists = array_merge($this->lists, $lists);
    }

    public function getLists(): array
    {
        return $this->lists;
    }

    public function getList(string $alias)
    {
        return $this->lists[$alias] ?? null;
    }

    public function addFilters(array $filters): void
    {
        $this->filters = array_merge($this->filters, $filters);
    }

    public function getFilter(string $alias)
    {
        return $this->filters[$alias] ?? null;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param mixed $converter
     */
    public function addConverter(ConverterInterface $converter): void
    {
        $this->decoders->add($converter);
    }

    public function getConverter(string $name): ?ConverterInterface
    {
        return $this->converters->filter(function (RegisteredByNameInterface $converter) use ($name) {
            return $converter->getName() === $name;
        })->first();
    }

    /**
     * @param mixed $decoders
     */
    public function addDecoder(ItemDecoder $decoders): void
    {
        $this->decoders->add($decoders);
    }

    public function getDecoder(string $name): ?ItemDecoder
    {
        return $this->decoders->filter(function (RegisteredByNameInterface $encoder) use ($name) {
            return $encoder->getName() === $name;
        })->first();
    }

    public function setWriter(ItemWriterInterface $writer): void
    {
        $this->writer = $writer;
    }

    public function getWriter(): ?ItemWriterInterface
    {
        return $this->writer;
    }

    public function setReader(ItemReaderInterface $reader): void
    {
        $this->reader = $reader;
    }

    public function getReader(): ?ItemReaderInterface
    {
        return $this->reader;
    }

    /**
     * @param mixed $encoders
     */
    public function addEncoder(ItemEncoder $encoders): void
    {
        $this->encoders->add($encoders);
    }

    public function getEncoder(string $name): ?ItemEncoder
    {
        return $this->encoders->filter(function (RegisteredByNameInterface $encoder) use ($name) {
            return $encoder->getName() === $name;
        })->first();
    }
}