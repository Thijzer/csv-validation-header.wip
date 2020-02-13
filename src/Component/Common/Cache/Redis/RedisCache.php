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

    public function set($key, $value, $ttl = null): bool
    {
        return $this->client->set($key, json_encode($value));
    }

    public function del($key): bool
    {
        return $this->client->del($key) > 0;
    }

    public function getMultiple($keys, $default = null): \Iterator
    {
        foreach ($this->client->getMultiple((array) $keys) as $key => $value) {
            yield $this->get($key);
        }
    }

    public function delete($key): bool
    {
        return $this->client->delete($key) > 0;
    }

    /** @inheritDoc */
    public function clear(): bool
    {
        return $this->client->flushAll();
    }

    /** @inheritDoc */
    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }

    /** @inheritDoc */
    public function deleteMultiple($keys): bool
    {
        return $this->client->delete(...$keys) > 0;
    }

    public function has($key): bool
    {
        return $this->client->exists($key) > 0;
    }
}