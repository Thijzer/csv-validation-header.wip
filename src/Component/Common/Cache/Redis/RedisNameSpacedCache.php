<?php

namespace Misery\Component\Common\Cache\Redis;

use Misery\Component\Common\Cache\SimpleCacheInterface;

class RedisNameSpacedCache implements SimpleCacheInterface
{
    /** @var string */
    private const SEPARATOR = '|';
    /** @var RedisCache */
    private $cache;
    /** @var string */
    private $nameSpace;

    public function __construct(RedisCache $cache, string $nameSpace)
    {
        $this->cache = $cache;
        $this->nameSpace = $nameSpace;
    }

    private function getNameSpaceKey($key): string
    {
        return static::SEPARATOR . $this->nameSpace . static::SEPARATOR . $key;
    }

    public function get($key, $default = null)
    {
        return $this->cache->get($this->getNameSpaceKey($key));
    }

    public function set($key, $value, $ttl = null): bool
    {
        return $this->cache->set($this->getNameSpaceKey($key), $value);
    }

    public function delete($key): bool
    {
        return $this->cache->del($this->getNameSpaceKey($key));
    }

    public function clear(): bool
    {
        return $this->cache->clear();
    }

    public function getMultiple($keys, $default = null): \Iterator
    {
        foreach ($keys as &$key) {
            $key = $this->getNameSpaceKey($key);
        }

        return $this->cache->getMultiple((array) $keys);
    }

    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->cache->set($this->getNameSpaceKey($key), $value);
        }

        return true;
    }

    public function deleteMultiple($keys): bool
    {
        return $this->cache->deleteMultiple(...$keys);
    }

    public function has($key): bool
    {
        return $this->cache->has($this->getNameSpaceKey($key));
    }
}