<?php

namespace Misery\Component\Common\Cache\Redis;

use Misery\Component\Common\Cache\SimpleCacheInterface;
use Redis;

class RedisCache implements SimpleCacheInterface
{
    /** @var Redis */
    private $client;

    public function __construct(Redis $client)
    {
        $this->client = $client;
    }

    public function get($key, $default = null)
    {
        return json_decode($this->client->get($key), true);
    }

    public function set($key, $value, $ttl = null): void
    {
        $this->client->set($key, json_encode($value));
    }

    public function del($key): void
    {
        $this->client->del($key);
    }

    public function getMultiple($keys, $default = null): \Iterator
    {
        foreach ($this->client->getMultiple((array) $keys) as $key => $value) {
            yield $this->get($key);
        }
    }

    public function delete($key): int
    {
        return (int) $this->client->delete($key);
    }

    /** @inheritDoc */
    public function clear(): void
    {
        $this->client->flushAll();
    }

    /** @inheritDoc */
    public function setMultiple($values, $ttl = null): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /** @inheritDoc */
    public function deleteMultiple($keys): int
    {
        return $this->client->delete(...$keys);
    }

    public function has($key): bool
    {
        return $this->client->exists($key);
    }
}