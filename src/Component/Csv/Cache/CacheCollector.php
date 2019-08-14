<?php

namespace Component\Csv\Cache;

class CacheCollector
{
    private $cache = [];

    public function setkey($cachekey, callable $data): void
    {
        // @todo set cacheKey with lazy promise if possible
    }

    public function setCache($cachekey, array $data): void
    {
        $this->cache[$cachekey] = $data;
    }

    public function hasCache($cachekey): bool
    {
        return in_array($cachekey, \array_keys($this->cache));
    }

    public function filterCache(string $cacheKey, callable $callable)
    {
        return array_keys(array_filter($this->cache[$cacheKey], function ($row) use ($callable) {
            return $callable($row);
        }));
    }

    public function getCache($cachekey)
    {
        return $this->cache[$cachekey] ?? null;
    }

    public function clear($cachekey = null): void
    {
        if ($cachekey === null) {
            $this->cache = [];
            return;
        }

        unset($this->cache[$cachekey]);
    }
}