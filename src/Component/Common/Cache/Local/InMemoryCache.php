<?php

namespace Misery\Component\Common\Cache\Local;

use Misery\Component\Common\Cache\SimpleCacheInterface;

class InMemoryCache implements SimpleCacheInterface
{
    /** @var array */
    private $cache = [];

    public function getIterator(): \Generator
    {
        foreach ($this->cache as $key => $value) {
            yield $value;
        }
    }

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

    public function set($key, $value, $ttl = null): bool
    {
        $this->cache[$key] = $value;

        return true;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $data) {
            $this->set($key, $data);
        }

        return true;
    }

    public function has($key): bool
    {
        return isset($this->cache[$key]);
    }

    public function delete($key): bool
    {
        unset($this->cache[$key]);

        return true;
    }

    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function getItems()
    {
        return $this->cache;
    }

    public function clear(): bool
    {
        unset($this->cache);
        $this->cache = [];

        return true;
    }

    private function filterKeys(array $keys): array
    {
        $a = array_diff($keys, array_keys($this->cache));
        return array_diff($keys, $a);
    }
}