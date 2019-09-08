<?php

namespace Misery\Component\Common\Cache\Local;

use Misery\Component\Common\Cache\SimpleCacheInterface;

class InMemoryCache implements SimpleCacheInterface
{
    private $cache = [];

    public function get($key, $default = null)
    {
        return $this->cache[$key] ?? null;
    }

    public function getMultiple($keys, $default = null)
    {
        $collection = [];
        foreach ($this->filterKeys($keys) as $key) {
            $collection[$key] = $this->cache[$key];
        }

        return $collection;
    }


    public function set($key, $value, $ttl = null): void
    {
        $this->cache[$key] = $value;
    }

    public function setMultiple($values, $ttl = null): void
    {
        foreach ($values as $key => $data) {
            $this->set($key, $data);
        }
    }

    public function has($key): bool
    {
        return isset($this->cache[$key]);
    }

    public function filter($cacheKey, callable $callable): array
    {
        return array_filter($this->cache[$cacheKey], static function ($row) use ($callable) {
            return $callable($row);
        });
    }

    public function delete($key): void
    {
        unset($this->cache[$key]);
    }

    public function deleteMultiple($keys): void
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    public function clear(): void
    {
        $this->cache = [];
    }

    private function filterKeys(array $keys): array
    {
        $a = array_diff($keys, array_keys($this->cache));
        return array_diff($keys, $a);
    }
}