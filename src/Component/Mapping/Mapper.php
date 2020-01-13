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
    public function mapColumns(array $item, array $mappings);
}
