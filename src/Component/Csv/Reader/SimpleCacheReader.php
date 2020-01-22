<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cache\SimpleCacheInterface;

class SimpleCacheReader implements ReaderInterface
{
    private $cache;

    public function __construct(SimpleCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getIterator(): \Iterator
    {
        foreach ($this->cache->getKeys() as $key) {
            yield $this->cache->get($key);
        }
    }

    public function find(array $constraints): ReaderInterface
    {
        $reader = $this;
        foreach ($constraints as $columnName => $rowValue) {
            $reader = $reader->filter(static function ($row) use ($rowValue, $columnName) {
                return $row[$columnName] === $rowValue;
            });
        }

        return $reader;
    }

    public function filter(callable $callable): ReaderInterface
    {
        return new self($this->process($callable));
    }

    private function process(callable $callable): \Generator
    {
        foreach ($this->getIterator() as $key => $row) {
            if (true === $callable($row)) {
                yield $key => $row;
            }
        }
    }

    public function getItems(): array
    {
        return iterator_to_array($this->getIterator());
    }

    public function read(): \Iterator
    {
        return $this->getIterator();
    }
}
