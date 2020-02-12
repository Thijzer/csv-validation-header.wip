<?php

namespace Misery\Component\Reader;

trait ReaderAwareTrait
{
    private $reader;

    public function setReader(ReaderInterface $reader): void
    {
        $this->reader = $reader;
    }

    public function getReader(): ReaderInterface
    {
        return $this->reader;
    }
}