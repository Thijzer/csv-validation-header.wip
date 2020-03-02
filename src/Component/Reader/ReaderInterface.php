<?php

namespace Misery\Component\Reader;

interface ReaderInterface
{
    /** @return array|false */
    public function read();
    public function getIterator(): \Iterator;
    public function find(array $constraints): self;
    public function filter(callable $callable): self;
    public function map(callable $callable): self;
    public function getItems(): array;
}