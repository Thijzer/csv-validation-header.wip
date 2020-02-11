<?php

namespace Misery\Component\Reader;

interface ItemReaderAwareInterface
{
    public function setReader(ItemReaderInterface $reader): void;

    public function getReader(): ItemReaderInterface;
}