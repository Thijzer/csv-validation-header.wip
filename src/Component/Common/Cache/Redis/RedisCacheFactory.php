<?php

namespace Misery\Component\Common\Cache\Redis;

use Redis;

class RedisCacheFactory
{
    public function create(RedisAccount $account): RedisCache
    {
        $client = new Redis();
        $client->connect($account->getHost());

        return new RedisCache($client);
    }
}