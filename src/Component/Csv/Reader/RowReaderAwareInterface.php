<?php

namespace Misery\Component\Csv\Reader;

interface RowReaderAwareInterface
{
    public function setReader(RowReaderInterface $reader): void;

    public function getReader(): RowReaderInterface;
}