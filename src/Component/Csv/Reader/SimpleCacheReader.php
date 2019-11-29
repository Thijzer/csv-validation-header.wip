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

    public function getIterator(): \Generator
    {
        foreach ($this->cache->getKeys() as $key) {
            yield $this->cache->get($key);
        }
    }

    public function findOneBy(array $filter): array
    {
        $value = current($filter);

        return $this->cache->get($value);
    }

    public function findBy(array $filter): array
    {
    }
}
