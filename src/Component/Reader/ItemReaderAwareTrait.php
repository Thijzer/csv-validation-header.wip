<?php

namespace Misery\Component\Reader;

trait ItemReaderAwareTrait
{
    private $reader;

    public function setReader(ItemReaderInterface $reader): void
    {
        $this->reader = $reader;
    }

    public function getReader(): ItemReaderInterface
    {
        return $this->reader;
    }
}