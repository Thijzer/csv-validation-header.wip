<?php

namespace Misery\Component\Reader;

interface ReaderInterface
{
    /** @return array|false */
    public function read();
    public function getIterator(): \Iterator;
    public function map(callable $callable): self;
    public function getItems(): array;

    // each part find, sort and filter should be part of it's own interface
    // these interfaces are not part of the ReaderInterface
    public function find(array $constraints): self;
    public function sort(array $criteria): self;
    public function filter(callable $callable): self;
}