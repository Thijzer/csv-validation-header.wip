<?php

namespace Misery\Component\Csv\Cache;

class CacheCollector
{
    private $cache = [];

    public function setCache($cacheKey, array $data): void
    {
        $this->cache[$cacheKey] = $data;
    }

    public function setCaches(array $dataSet): void
    {
        foreach ($dataSet as $cacheKey => $data) {
            $this->setCache($cacheKey, $data);
        }
    }

    public function hasKey($cacheKey): bool
    {
        return array_key_exists($cacheKey, $this->cache);
    }

    public function hasCaches(array $cacheKeys): bool
    {
        return count($this->filterKeys($cacheKeys)) > 0;
    }

    private function filterKeys(array $cacheKeys): array
    {
        $a = array_diff($cacheKeys, array_keys($this->cache));
        return array_diff($cacheKeys, $a);
    }

    public function filterCache($cacheKey, callable $callable): array
    {
        return array_filter($this->cache[$cacheKey], static function ($row) use ($callable) {
            return $callable($row);
        });
    }

    public function getCache($cacheKey)
    {
        return $this->cache[$cacheKey] ?? null;
    }

    public function getCaches(array $cacheKeys)
    {
        $collection = [];
        foreach ($this->filterKeys($cacheKeys) as $filterKey) {
            $collection[$filterKey] = $this->cache[$filterKey];
        }

        return $collection;
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