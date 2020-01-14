<?php

namespace Misery\Component\Csv\Reader;

trait RowReaderAwareTrait
{
    private $reader;

    public function setReader(RowReaderInterface $reader): void
    {
        $this->reader = $reader;
    }

    public function getReader(): RowReaderInterface
    {
        return $this->reader;
    }
}