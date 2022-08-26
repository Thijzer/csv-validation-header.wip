<?php

namespace Misery\Component\Source;

use Misery\Component\BluePrint\BluePrint;

class SourceCollection
{
    /** @var array|Source[] */
    private $items = [];
    /** @var string */
    private $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function add(Source $source): void
    {
        $this->items[$source->getAlias()] = $source;
    }

    public function get($alias): ? Source
    {
        return $this->items[$alias] ?? null;
    }

    public function getAliases(): array
    {
        return array_keys($this->items);
    }
}