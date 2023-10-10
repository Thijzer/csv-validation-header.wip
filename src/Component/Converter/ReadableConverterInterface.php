<?php

namespace Misery\Component\Converter;

interface ReadableConverterInterface
{
    public function read(array $item): false|array;
}