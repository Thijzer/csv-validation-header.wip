<?php

namespace Misery\Component\Common\Cache\Local;

use Misery\Component\Common\Cache\SimpleCacheInterface;

class NameSpacedPoolCache
{
    /** @var SimpleCacheInterface[] */
    private $caches = [];

    public function addCache(SimpleCacheInterface $cache, string $nameSpace): void
    {
        $this->caches[$nameSpace] = $cache;
    }

    public function get(string $nameSpaceKey, $key = null)
    {
        if (!isset($this->caches[$nameSpaceKey])) {
            return null;
        }

        return $key ?
            $this->caches[$nameSpaceKey]->get($key) :
            $this->caches[$nameSpaceKey] ?? null
        ;
    }

    public function set(string $nameSpaceKey, $values)
    {
        $cache = new InMemoryCache();
        $cache->setMultiple($values);
        $this->addCache($cache, $nameSpaceKey);
    }

    public function has($key): bool
    {
        return isset($this->caches[$key]);
    }

    public function getItems(string $nameSpaceKey)
    {
        return $this->get($nameSpaceKey)->getItems();
    }

    public function clear()
    {
        foreach ($this->caches as $cache) {
            $cache->clear();
        }
    }
}