<?php

namespace Component\Format;

class ListFormat
{
    public function format(string $separtor, $value): array
    {
        return explode($separtor, $value);
    }
}