<?php

namespace Misery\Component\Common\Collection;

class ArrayCollection
{
    private $items = [];

    public function __construct(array $items = [])
    {
        foreach (array_filter($items) as $key => $item) {
            $this->set($key, $item);
        }
    }

    public function add($item): void
    {
        $this->items[] = $item;
    }

    public function set($key, $item): void
    {
        $this->items[$key] = $item;
    }

    public function get($key): self
    {
        return new static([$this->items[$key] ?? null]);
    }

    public function first()
    {
        return current($this->items);
    }

    public function map(\Closure $p): self
    {
        return new static(array_map($p, $this->items));
    }

    public function filter(\Closure $filter): self
    {
        return new static(array_filter($this->items, $filter, ARRAY_FILTER_USE_BOTH));
    }

    public function hasValues(): bool
    {
        return $this->count() > 0;
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function getValues(): array
    {
        return $this->items;
    }
}