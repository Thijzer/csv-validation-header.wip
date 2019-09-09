<?php

namespace Misery\Component\Common\Cache;

interface CacheAwareInterface
{
    public function setCache(SimpleCacheInterface $cache): void;
}