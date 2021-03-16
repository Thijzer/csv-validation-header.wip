<?php

namespace Misery\Component\Source;

class SourceListFactory
{
    /**
     * @var SourceCollection
     */
    private $collection;

    public function __construct(SourceCollection $collection)
    {
        $this->collection = $collection;
    }

    public function createFromConfiguration(array $configuration)
    {


    }
}