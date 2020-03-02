<?php

namespace Misery\Component\Reader;

interface ItemReaderInterface extends ReaderInterface
{
    public function index(array $lines): self;
}
