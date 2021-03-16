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
use Misery\Component\Source\SourceCollection;

class Configuration
{
    private $pipeline = null;
    private $actions = null;
    private $blueprints;
    private $encoders;
    private $decoders;
    private $lists = [];
    private $converters;
    private $sources;

    public function __construct()
    {
        $this->converters = new ArrayCollection();
        $this->encoders = new ArrayCollection();
        $this->decoders = new ArrayCollection();
        $this->blueprints = new ArrayCollection();
    }

    public function addSources(SourceCollection $sources)
    {
        $this->sources = $sources;
    }

    public function getSources(): SourceCollection
    {
        return $this->sources;
    }

    public function setPipeline(Pipeline $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    public function getPipeline(): ?Pipeline
    {
        return $this->pipeline;
    }

    public function setActions(ItemActionProcessor $actionProcessor)
    {
        $this->actions = $actionProcessor;
    }

    public function getActions(): ?ItemActionProcessor
    {
        return $this->actions;
    }

    public function addBlueprint(BluePrint $bluePrint)
    {
        $this->blueprints->add($bluePrint);
    }

    public function addBlueprints(ArrayCollection $collection)
    {
        $this->blueprints->merge($collection);
    }

    public function addLists(array $lists)
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

    public function getDecoder(string $name): ?ConverterInterface
    {
        return $this->decoders->filter(function (RegisteredByNameInterface $converter) use ($name) {
            return $converter->getName() === $name;
        })->first();
    }

    /**
     * @param mixed $encoders
     */
    public function addEncoder(ItemEncoder $encoders): void
    {
        $this->encoders->add($encoders);
    }

    public function getEncoder(string $name): ?ConverterInterface
    {
        return $this->encoders->filter(function (RegisteredByNameInterface $converter) use ($name) {
            return $converter->getName() === $name;
        })->first();
    }
}