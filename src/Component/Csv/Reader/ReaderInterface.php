<?php

namespace Misery\Component\Csv\Reader;

interface ReaderInterface
{
    public function read(): \Iterator;
    public function find(array $constraints): self;
    public function filter(callable $callable): self;
    public function getIterator(): \Iterator;
    public function getItems(): array;
}