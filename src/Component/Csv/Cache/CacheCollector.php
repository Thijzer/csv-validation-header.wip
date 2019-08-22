<?php

namespace Misery\Component\Csv\Cache;

class CacheCollector
{
    private $cache = [];

    public function setkey($cacheKey, callable $data): void
    {
        // @todo set cacheKey with lazy promise if possible
    }

    public function setCache($cacheKey, array $data): void
    {
        $this->cache[$cacheKey] = $data;
    }

    public function hasCache($cacheKey): bool
    {
        return array_key_exists($cacheKey, $this->cache);
    }

    public function filterCache(string $cacheKey, callable $callable): array
    {
        return array_keys(array_filter($this->cache[$cacheKey],static function ($row) use ($callable) {
            return $callable($row);
        }));
    }

    public function getCache($cacheKey)
    {
        return $this->cache[$cacheKey] ?? null;
    }

    public function clear($cacheKey = null): void
    {
        if ($cacheKey === null) {
            $this->cache = [];
            return;
        }

        unset($this->cache[$cacheKey]);
    }
}