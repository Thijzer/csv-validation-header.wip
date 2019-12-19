<?php

namespace Misery\Component\Common\Registry;

use Misery\Component\Common\Collection\ArrayCollection;

class Registry implements RegistryInterface
{
    private $collection;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function register(string $alias, $object): self
    {
        $this->collection->set($alias, $object);

        return $this;
    }

    public function filterByAlias(string $alias)
    {
        return $this->collection->get($alias)->first();
    }
}