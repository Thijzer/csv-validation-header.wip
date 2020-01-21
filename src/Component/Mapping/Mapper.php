<?php

namespace Misery\Component\Mapping;

interface Mapper
{
    /**
     * @param array $item
     * @param array $mappings
     *
     * @return mixed
     */
    public function map(array $item, array $mappings);
}