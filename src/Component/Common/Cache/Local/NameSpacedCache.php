<?php

namespace Misery\Component\Common\Cache\Local;

use Misery\Component\Common\Cache\SimpleCacheInterface;

class NameSpacedCache implements SimpleCacheInterface
{
    private const SEPARATOR = '|';
    private $cache;
    private $nameSpace;

    public function __construct(SimpleCacheInterface $cache, string $nameSpace)
    {
        $this->cache = $cache;
        $this->nameSpace = $nameSpace;
    }

    public function getNameSpace(): string
    {
        return $this->nameSpace;
    }

    private function getNameSpaceKey($key): string
    {
        return static::SEPARATOR . $this->nameSpace . static::SEPARATOR . $key;
    }

    /** @inheritDoc */
    public function get($key, $default = null)
    {
        return $this->cache->get($this->getNameSpaceKey($key));
    }

    /** @inheritDoc */
    public function set($key, $value, $ttl = null)
    {
        $this->cache->set($this->getNameSpaceKey($key), $value);
    }

    /** @inheritDoc */
    public function delete($key): void
    {
        $this->cache->delete($this->getNameSpaceKey($key));
    }

    /** @inheritDoc */
    public function clear(): void
    {
        $this->cache->clear();
    }

    /** @inheritDoc */
    public function getMultiple($keys, $default = null)
    {
        foreach ($keys as &$key) {
            $key = $this->getNameSpaceKey($key);
        }

        return $this->cache->getMultiple((array)$keys);
    }

    /** @inheritDoc */
    public function setMultiple($values, $ttl = null): void
    {
        foreach ($values as $key => $value) {
            $this->cache->set($this->getNameSpaceKey($key), $value);
        }
    }

    /** @inheritDoc */
    public function deleteMultiple($keys): int
    {
        $this->cache->deleteMultiple(...$keys);
    }

    /** @inheritDoc */
    public function has($key): bool
    {
        return $this->cache->has($this->getNameSpaceKey($key));
    }
}