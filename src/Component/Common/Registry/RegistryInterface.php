<?php

namespace Misery\Component\Common\Registry;

interface RegistryInterface
{
    public function filterByAlias(string $name);
    public function getAlias(): string;
}