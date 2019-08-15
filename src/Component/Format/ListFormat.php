<?php

namespace Component\Format;

class ListFormat
{
    public function format(string $separator, $value): array
    {
        return explode($separator, $value);
    }
}