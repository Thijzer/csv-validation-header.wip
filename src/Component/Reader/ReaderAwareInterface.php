<?php

namespace Misery\Component\Reader;

interface ReaderAwareInterface
{
    public function setReader(ReaderInterface $reader): void;

    public function getReader(): ReaderInterface;
}