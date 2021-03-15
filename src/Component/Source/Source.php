<?php

namespace Misery\Component\Source;

use Misery\Component\Reader\ItemReaderInterface;

class Source
{
    private $reader;
    private $alias;

    public function __construct(
        ItemReaderInterface $reader,
        string $alias
    ) {
        $this->alias = $alias;
        $this->reader = $reader;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getReader(): ItemReaderInterface
    {
        return $this->reader;
    }
}