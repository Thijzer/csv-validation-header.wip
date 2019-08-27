<?php

namespace Misery\Component\Csv\Reader;

interface ReaderAwareInterface
{
    public function setReader(ReaderInterface $reader): void;

    public function getReader(): ReaderInterface;
}